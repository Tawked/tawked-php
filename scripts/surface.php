<?php

/*
 * The PHP SDK's public surface against the contract's neutral one,
 * from two halves neither read from the generator's input:
 *
 * 1. Shape: the package as `composer archive` ships it, extracted to a temporary folder and loaded
 *    from there; ReflectionClass walks Tawked\TawkedClient, Tawked\TawkedAccountClient and
 *    Tawked\OAuth\Apps and prints each method's dotted name, its parameters by wire name and its
 *    answer (the PHP class, checked against tests/Generated/SurfaceMap.php, then its neutral name).
 * 2. Behaviour: what the contract tests saw each method do (tests/Support/Recorder.php): a key
 *    sent, the requests after a 500, after a timeout and after a failure before sending, pages.
 *
 * The joined lines must equal sdks/contract/surface.txt and surface-account.txt. When the
 * behaviour record is missing, the contract tests are run first to make it.
 *
 *   php scripts/surface.php [--print]
 */

declare(strict_types=1);

use Tawked\Account\AccountPage;
use Tawked\Account\AccountResponse;
use Tawked\Pagination\Page;

$root = dirname(__DIR__);
$print = in_array('--print', $argv, true);
$contractDir = $root.'/../contract';

// ---- the record of behaviour --------------------------------------------------------------------
$record = getenv('TAWKED_SURFACE_RECORD') ?: $root.'/.phpunit.cache/surface-observed.json';
if (! is_file($record)) {
    fwrite(STDOUT, "No behaviour record yet: running the contract tests.\n");
    passthru(escapeshellarg(PHP_BINARY).' '.escapeshellarg($root.'/vendor/bin/phpunit').' --testsuite generated,contract', $code);
    if ($code !== 0 || ! is_file($record)) {
        fwrite(STDERR, "The contract tests failed; the surface cannot be checked.\n");
        exit(1);
    }
}
$observed = json_decode((string) file_get_contents($record), true, 512, JSON_THROW_ON_ERROR);

// ---- the built artifact -------------------------------------------------------------------------
$tmp = sys_get_temp_dir().'/tawked-php-surface-'.bin2hex(random_bytes(4));
mkdir($tmp, 0777, true);
$composer = getenv('COMPOSER_BINARY') ?: 'composer';
exec(escapeshellarg($composer).' archive --working-dir='.escapeshellarg($root).' --format=zip --dir='.escapeshellarg($tmp).' --file=package 2>&1', $out, $code);
if ($code !== 0 || ! is_file($tmp.'/package.zip')) {
    fwrite(STDERR, "composer archive failed:\n".implode("\n", $out)."\n");
    exit(1);
}
$zip = new ZipArchive();
$zip->open($tmp.'/package.zip');
$zip->extractTo($tmp.'/package');
$zip->close();
foreach (['tests', 'scripts', 'src/.generated'] as $private) {
    if (file_exists($tmp.'/package/'.$private)) {
        fwrite(STDERR, "The archive ships {$private}: it must stay out of the package.\n");
        exit(1);
    }
}
$shipped = $tmp.'/package/src/';
spl_autoload_register(static function (string $class) use ($shipped): void {
    if (str_starts_with($class, 'Tawked\\') && ! str_starts_with($class, 'Tawked\\Tests\\')) {
        $file = $shipped.str_replace('\\', '/', substr($class, strlen('Tawked\\'))).'.php';
        if (is_file($file)) {
            require $file;
        }
    }
}, true, true);
require $root.'/vendor/autoload.php';

$map = require $root.'/tests/Generated/SurfaceMap.php';
$problems = [];

/** `externalId` → `external_id`. */
function wire(string $name): string
{
    return strtolower((string) preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
}

/**
 * @return list<string> the parameters of a method by wire name, its options left out
 */
function params(ReflectionMethod $method): array
{
    $out = [];
    foreach ($method->getParameters() as $p) {
        if ($p->getName() === 'options') {
            continue;
        }
        $out[] = $p->getName() === 'params' ? ($p->isOptional() ? 'params?' : 'params') : wire($p->getName());
    }

    return $out;
}

/**
 * The namespaces a client or a namespace holds: its public typed properties whose class has methods.
 *
 * @return array<string, object>
 */
function namespacesOf(object $object): array
{
    $out = [];
    foreach ((new ReflectionObject($object))->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
        $type = $property->getType();
        if ($type instanceof ReflectionNamedType && ! $type->isBuiltin() && (str_starts_with($type->getName(), 'Tawked\\Service\\') || str_starts_with($type->getName(), 'Tawked\\Account\\Api\\'))) {
            $out[$property->getName()] = $property->getValue($object);
        }
    }

    return $out;
}

/**
 * Every endpoint method under a namespace object, `dotted.name` => ReflectionMethod.
 *
 * @return array<string, ReflectionMethod>
 */
function walk(object $namespace, string $prefix): array
{
    $out = [];
    $class = new ReflectionClass($namespace);
    foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
        if ($method->getDeclaringClass()->getName() !== $class->getName() || $method->isConstructor() || $method->isStatic()) {
            continue;
        }
        // iterate() is observed as [cursor]/[offset] on list(); waitAndRetry() is the approvals' helper.
        if (in_array($method->getName(), ['iterate', 'waitAndRetry'], true)) {
            continue;
        }
        $out[$prefix.'.'.$method->getName()] = $method;
    }
    foreach (namespacesOf($namespace) as $name => $child) {
        $out += walk($child, $prefix.'.'.$name);
    }

    return $out;
}

/**
 * The bracketed flags a method's observed behaviour earns, and its retry class.
 *
 * @param array<string, mixed>|null $seen
 * @return list<string>
 */
function behaviour(?array $seen, string $retryInContract): array
{
    if ($seen === null) {
        return ['unobserved'];
    }
    if (($seen['requests'] ?? null) === 0) {
        return ['local'];
    }
    $after500 = $seen['after_500'] ?? null;
    $afterTimeout = $seen['after_timeout'] ?? null;
    $keySent = ($seen['key_sent'] ?? false) === true;
    $preRetried = ($seen['before_send'] ?? null) === 'retried';
    $flags = [];
    if ($after500 === 2 && ! $keySent) {
        $flags[] = 'safe';
    } elseif ($after500 === 2 && $keySent && ($seen['same_key'] ?? false) === true) {
        $flags[] = 'idempotent';
    } elseif ($after500 === 1 && $afterTimeout === 1 && $keySent && $preRetried) {
        $flags[] = 'write_once';
    } elseif ($after500 === 1 && $afterTimeout === 1 && ! $keySent) {
        // Sent once after it may have been applied, retried before sending, no key: `never`, or a
        // `write_once` call whose endpoint reads no Idempotency-Key (the SDK sends no header the
        // API does not read), which no observation can tell apart.
        $flags[] = $retryInContract === 'write_once' ? 'write_once' : 'never';
    } else {
        $flags[] = 'misbehaved:'.json_encode($seen);
    }
    if (isset($seen['pages'])) {
        $flags[] = (string) $seen['pages'];
    }

    return $flags;
}

/**
 * @return array<string, string> dotted call => its contract line, for the retry class
 */
function contractLines(string $file): array
{
    $out = [];
    foreach (file($file, FILE_IGNORE_NEW_LINES) ?: [] as $line) {
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (preg_match('/^\S+\s+(\S+?)\(/', $line, $m) === 1) {
            $out[$m[1]] = $line;
        }
    }

    return $out;
}

$expectedProduct = contractLines($contractDir.'/surface.txt');
$expectedAccount = contractLines($contractDir.'/surface-account.txt');
$retryOf = static function (string $line): string {
    preg_match('/\[(safe|idempotent|write_once|never|local)\]/', $line, $m);

    return $m[1] ?? '';
};

$lines = [];

// ---- product ------------------------------------------------------------------------------------
$client = new Tawked\TawkedClient('tk_live_surface_check');
$methods = [];
foreach (namespacesOf($client) as $name => $namespace) {
    $methods += walk($namespace, $name);
}
foreach ($methods as $call => $method) {
    $type = $method->getReturnType();
    $class = $type instanceof ReflectionNamedType ? $type->getName() : (string) $type;
    $entry = $map[$call] ?? null;
    if ($entry === null) {
        $problems[] = "{$call}: a public method the contract does not name";

        continue;
    }
    if ($class !== $entry['class']) {
        $problems[] = "{$call}: answers {$class}, the map says {$entry['class']}";
    }
    if (isset($entry['item'])) {
        if (! is_subclass_of($class, Page::class) || constant($class.'::ITEM_CLASS') !== $entry['item']) {
            $problems[] = "{$call}: {$class} is not a page of {$entry['item']}";
        }
    }
    $flags = behaviour($observed['product '.$call] ?? null, $retryOf($expectedProduct[$call] ?? ''));
    if (str_contains((string) $method->getDocComment(), '@tawked-stability early_access')) {
        $flags[] = 'early_access';
    }
    $lines[] = str_pad('product', 8).' '.$call.'('.implode(', ', params($method)).') -> '.$entry['neutral'].'  '.implode(' ', array_map(fn($f) => '['.$f.']', $flags));
}

// ---- oauth --------------------------------------------------------------------------------------
foreach ((new ReflectionClass(Tawked\OAuth\Apps::class))->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_STATIC) as $method) {
    if (! $method->isPublic() || ! $method->isStatic() || in_array($method->getName(), ['challenge', 'newVerifier'], true)) {
        continue; // PKCE helpers, not endpoints
    }
    $call = 'apps.'.$method->getName();
    $type = $method->getReturnType();
    $class = $type instanceof ReflectionNamedType ? $type->getName() : '';
    $entry = $map[$call] ?? null;
    if ($entry === null || $class !== $entry['class']) {
        $problems[] = "{$call}: answers {$class}, the map says ".($entry['class'] ?? 'nothing');

        continue;
    }
    $flags = behaviour($observed['oauth '.$call] ?? null, 'never');
    $lines[] = str_pad('oauth', 8).' '.$call.'('.implode(', ', params($method)).') -> '.$entry['neutral'].'  '.implode(' ', array_map(fn($f) => '['.$f.']', $flags));
}

// ---- account ------------------------------------------------------------------------------------
$account = new Tawked\TawkedAccountClient('tk_acct_surfacecheck');
$accountLines = [];
$methods = [];
foreach (namespacesOf($account) as $name => $namespace) {
    $methods += walk($namespace, $name);
}
foreach ($methods as $call => $method) {
    $type = $method->getReturnType();
    $class = $type instanceof ReflectionNamedType ? $type->getName() : '';
    $returns = match ($class) {
        AccountPage::class => 'page<data>',
        AccountResponse::class => 'data',
        default => 'unknown:'.$class,
    };
    $flags = behaviour($observed['account '.$call] ?? null, $retryOf($expectedAccount[$call] ?? ''));
    $accountLines[] = str_pad('account', 8).' '.$call.'('.implode(', ', params($method)).') -> '.$returns.'  '.implode(' ', array_map(fn($f) => '['.$f.']', $flags));
}

// ---- compare ------------------------------------------------------------------------------------
sort($lines);
sort($accountLines);
$compare = static function (array $actual, string $file) use (&$problems): void {
    $expected = array_values(array_filter(file($file, FILE_IGNORE_NEW_LINES) ?: [], fn($l) => $l !== '' && ! str_starts_with($l, '#')));
    sort($expected);
    foreach (array_diff($expected, $actual) as $line) {
        $problems[] = 'missing or different: '.$line;
    }
    foreach (array_diff($actual, $expected) as $line) {
        $problems[] = 'the SDK has:          '.$line;
    }
};
$compare($lines, $contractDir.'/surface.txt');
$compare($accountLines, $contractDir.'/surface-account.txt');

if ($print) {
    fwrite(STDOUT, implode("\n", [...$lines, ...$accountLines])."\n");
}
exec('rm -rf '.escapeshellarg($tmp));
if ($problems !== []) {
    fwrite(STDERR, "The PHP surface differs from the contract:\n- ".implode("\n- ", $problems)."\n");
    exit(1);
}
$tested = ['product' => 0, 'account' => 0, 'errors' => 0];
foreach ($observed as $key => $seen) {
    $surface = strtok((string) $key, ' ');
    if (($seen['called'] ?? false) === true && isset($tested[$surface])) {
        $tested[$surface]++;
    }
    if (($seen['errors'] ?? false) === true) {
        $tested['errors']++;
    }
}
fwrite(STDOUT, "sdks/php: contract tests called {$tested['product']} product endpoints and {$tested['account']} Account API operations, and {$tested['errors']} methods' documented errors\n");
fwrite(STDOUT, 'sdks/php: the surface matches the contract ('.count($lines).' product and OAuth methods, '.count($accountLines)." Account API operations; shape from the composer archive, behaviour from the contract tests)\n");
