<?php

/*
 * The generator of tawked/tawked-php: reads the SDK contract,
 * sdks/contract/api.json and sdks/contract/account.json, and nothing else, and writes the generated
 * half of the package in today's namespaces and folders:
 *
 *   src/Model/**            the API's objects (Tawked\Model\{Verify,WhatsApp,Email,Chat,Partner,Webhook,OAuth})
 *   src/Service/**          every product endpoint, one class per namespace
 *   src/Account/Api/**      every Account API operation, for TawkedAccountClient
 *   src/ErrorCode.php       every error code with its status
 *   src/Webhook/Events.php  every webhook event and the class of its data
 *   src/Contract.php        the contract's constants (api version, key prefixes, sandbox, limits)
 *   tests/Generated/**      the contract tests' data, run against sdks/mock-server
 *   tests/fixtures/*.json   copies of the two contract files and of vectors/both-headers.json for the
 *                           hand-written tests
 *   src/.generated          the list of every file above, so a stale one is deleted
 *
 *   php scripts/generate.php                 write
 *   php scripts/generate.php --check         write nothing; exit 1 when a file is stale
 *   php scripts/generate.php --error-codes   only src/ErrorCode.php (what the app's gate reads)
 *   --contract=<dir>                         the contract's folder (../contract by default)
 *
 * Deterministic: the same contract writes the same bytes, already in the package's Pint style.
 * Hand-written: everything else (the transport, errors, pagination, webhooks, OAuth, the clients).
 */

declare(strict_types=1);

const HEADER = '// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.';
const KNOWN_CONTRACT_VERSION = 2;

$root = dirname(__DIR__);
$check = in_array('--check', $argv, true);
$onlyErrorCodes = in_array('--error-codes', $argv, true);
// In this repository the contract is beside the package; in the package's own repository, its copy.
$contractDir = is_file($root.'/../contract/api.json') ? $root.'/../contract' : $root.'/tests/fixtures';
foreach ($argv as $arg) {
    if (str_starts_with($arg, '--contract=')) {
        $contractDir = substr($arg, strlen('--contract='));
    } elseif ($arg !== $argv[0] && ! in_array($arg, ['--check', '--error-codes'], true)) {
        fwrite(STDERR, "generate.php: unknown argument {$arg}\n");
        exit(2);
    }
}

$apiRaw = (string) file_get_contents($contractDir.'/api.json');
$api = json_decode($apiRaw, true, 512, JSON_THROW_ON_ERROR);
if (($api['contract_version'] ?? null) !== KNOWN_CONTRACT_VERSION) {
    fwrite(STDERR, 'generate.php: api.json has contract_version '.json_encode($api['contract_version'] ?? null).'; this generator reads '.KNOWN_CONTRACT_VERSION." only. Update the generator first.\n");
    exit(1);
}

$names = require __DIR__.'/names.php';
$problems = [];
$files = [];

$files['src/ErrorCode.php'] = errorCodeFile($api);

if (! $onlyErrorCodes) {
    $accountRaw = (string) file_get_contents($contractDir.'/account.json');
    $account = json_decode($accountRaw, true, 512, JSON_THROW_ON_ERROR);
    if (($account['contract_version'] ?? null) !== KNOWN_CONTRACT_VERSION) {
        fwrite(STDERR, "generate.php: account.json has an unknown contract_version.\n");
        exit(1);
    }

    $gen = new PhpGenerator($api, $account, $names);
    foreach ($gen->files() as $path => $content) {
        $files[$path] = $content;
    }
    $problems = $gen->problems;
    $files['tests/fixtures/api.json'] = $apiRaw;
    $files['tests/fixtures/account.json'] = $accountRaw;
    // The both-headers vector: one delivery with both header sets (beside the contract, or this package's own copy).
    $bothHeaders = is_file($contractDir.'/vectors/both-headers.json') ? $contractDir.'/vectors/both-headers.json' : $contractDir.'/both-headers.json';
    $files['tests/fixtures/both-headers.json'] = (string) file_get_contents($bothHeaders);
    ksort($files);
    $manifest = array_keys($files);
    $files['src/.generated'] = "# Every file scripts/generate.php writes; a file listed here and no longer generated is deleted.\n".implode("\n", $manifest)."\n";
}

if ($problems !== []) {
    fwrite(STDERR, "generate.php: the contract cannot be generated:\n- ".implode("\n- ", array_unique($problems))."\n");
    exit(1);
}

// Stale files: listed by the last run and no longer written.
$stale = [];
if (! $onlyErrorCodes && is_file($root.'/src/.generated')) {
    foreach (explode("\n", (string) file_get_contents($root.'/src/.generated')) as $line) {
        $line = trim($line);
        if ($line !== '' && ! str_starts_with($line, '#') && ! isset($files[$line]) && is_file($root.'/'.$line)) {
            $stale[] = $line;
        }
    }
}

$changed = [];
foreach ($files as $path => $content) {
    $target = $root.'/'.$path;
    if (! is_file($target) || file_get_contents($target) !== $content) {
        $changed[] = $path;
        if (! $check) {
            if (! is_dir(dirname($target))) {
                mkdir(dirname($target), 0777, true);
            }
            file_put_contents($target, $content);
        }
    }
}
if (! $check) {
    foreach ($stale as $path) {
        unlink($root.'/'.$path);
        $dir = dirname($root.'/'.$path);
        while ($dir !== $root && is_dir($dir) && count((array) scandir($dir)) === 2) {
            rmdir($dir);
            $dir = dirname($dir);
        }
    }
}

if ($check) {
    if ($changed !== [] || $stale !== []) {
        fwrite(STDERR, "sdks/php is stale against the contract; run `composer generate` in sdks/php:\n");
        foreach ($changed as $path) {
            fwrite(STDERR, "  changed: {$path}\n");
        }
        foreach ($stale as $path) {
            fwrite(STDERR, "  stale:   {$path}\n");
        }
        exit(1);
    }
    fwrite(STDOUT, 'sdks/php: '.count($files)." generated files are up to date\n");
    exit(0);
}
fwrite(STDOUT, 'sdks/php: wrote '.count($changed).' file(s), removed '.count($stale).', '.count($files)." generated in all\n");

// ---------------------------------------------------------------------------------------------
// Emission helpers
// ---------------------------------------------------------------------------------------------

/** A PHP literal of a JSON value, in the package's style (short arrays, single quotes, trailing commas). */
function lit(mixed $value, int $indent = 0): string
{
    if ($value === null) {
        return 'null';
    }
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if (is_int($value)) {
        return (string) $value;
    }
    if (is_float($value)) {
        $text = var_export($value, true);

        return str_contains($text, '.') || str_contains($text, 'E') ? $text : $text.'.0';
    }
    if (is_string($value)) {
        return str_quote($value);
    }
    if ($value instanceof stdClass) {
        $value = (array) $value;
        if ($value === []) {
            return 'new \\stdClass()';
        }
    }
    if ($value === []) {
        return '[]';
    }
    $pad = str_repeat('    ', $indent + 1);
    $close = str_repeat('    ', $indent);
    $lines = [];
    $isList = array_is_list($value);
    foreach ($value as $k => $v) {
        $lines[] = $pad.($isList ? '' : str_quote((string) $k).' => ').lit($v, $indent + 1).',';
    }

    return "[\n".implode("\n", $lines)."\n".$close.']';
}

/** A list of strings on one line: `['a', 'b']`. */
function flat(array $values): string
{
    return '['.implode(', ', array_map(fn($v) => str_quote((string) $v), $values)).']';
}

/** A single-quoted PHP string, or a double-quoted one when it holds a control character. */
function str_quote(string $value): string
{
    if (preg_match('/[\x00-\x1F\x7F]/', $value) === 1) {
        $out = '';
        foreach (mb_str_split($value) as $ch) {
            $out .= match ($ch) {
                "\n" => '\\n',
                "\r" => '\\r',
                "\t" => '\\t',
                '"' => '\\"',
                '\\' => '\\\\',
                '$' => '\\$',
                default => (strlen($ch) === 1 && (ord($ch) < 32 || ord($ch) === 127)) ? sprintf('\\x%02X', ord($ch)) : $ch,
            };
        }

        return '"'.$out.'"';
    }

    return "'".str_replace(['\\', "'"], ['\\\\', "\\'"], $value)."'";
}

/** `email_messages` → `EmailMessages`; `whatsapp` → `WhatsApp`. */
function pascal(string $word, array $segments = []): string
{
    if (isset($segments[$word])) {
        return $segments[$word];
    }

    return str_replace(' ', '', ucwords(str_replace(['_', '-', '.'], ' ', $word)));
}

/** `price_halalas` → `priceHalalas`. */
function camel(string $wire): string
{
    $p = pascal($wire);

    return lcfirst($p);
}

/** A docblock's text: no comment terminator, one line per paragraph, wrapped. */
function docText(?string $text, string $pad, int $width = 100): array
{
    if ($text === null || trim($text) === '') {
        return [];
    }
    $text = str_replace(['*/', "\r"], ['*\\/', ''], trim($text));
    $out = [];
    foreach (preg_split('/\n\s*\n/', $text) ?: [] as $i => $para) {
        if ($i > 0) {
            $out[] = $pad.' *';
        }
        $para = preg_replace('/\s+/', ' ', trim($para)) ?? '';
        foreach (explode("\n", wordwrap($para, max(40, $width - strlen($pad) - 3), "\n", false)) as $line) {
            $out[] = rtrim($pad.' * '.$line);
        }
    }

    return $out;
}

function singular(string $word): string
{
    if (str_ends_with($word, 'ies')) {
        return substr($word, 0, -3).'y';
    }
    if (str_ends_with($word, 'ses')) {
        return substr($word, 0, -2);
    }
    if (str_ends_with($word, 's') && ! str_ends_with($word, 'ss')) {
        return substr($word, 0, -1);
    }

    return $word;
}

function errorCodeFile(array $api): string
{
    $codes = [];
    foreach ($api['errors'] as $error) {
        $meaning = $error['meaning'] ?? null;
        $text = null;
        if (is_string($meaning)) {
            $text = $meaning;
        } elseif (is_array($meaning) && count($meaning) === 1) {
            $text = (string) array_values($meaning)[0];
        }
        if ($text !== null && strlen($text) > 160) {
            $text = null;
        }
        $codes[] = ['code' => $error['error'], 'status' => (int) $error['http'], 'text' => $text];
    }
    usort($codes, fn($a, $b) => [$a['status'], $a['code']] <=> [$b['status'], $b['code']]);

    $out = "<?php\n\ndeclare(strict_types=1);\n\n".HEADER."\n\nnamespace Tawked;\n\n";
    $out .= "/**\n * Every `error` code the Tawked API answers, with its HTTP status. Compare them with\n * `\$exception->errorCode`. Generated from the API contract (sdks/contract/api.json).\n */\nfinal class ErrorCode\n{\n";
    foreach ($codes as $c) {
        $doc = 'HTTP '.$c['status'].'.'.($c['text'] !== null ? ' '.rtrim(str_replace('*/', '*\\/', preg_replace('/\s+/', ' ', $c['text']) ?? ''), '.').'.' : '');
        $out .= '    /** '.$doc." */\n";
        $out .= '    public const '.strtoupper($c['code']).' = '.str_quote($c['code']).";\n\n";
    }
    $out .= "    /** Not an API code: the response was not the API's JSON. */\n    public const UNEXPECTED_RESPONSE = 'unexpected_response';\n\n";
    $out .= "    /**\n     * The HTTP status of each code.\n     *\n     * @var array<string, int>\n     */\n    public const STATUS = [\n";
    foreach ($codes as $c) {
        $out .= '        self::'.strtoupper($c['code']).' => '.$c['status'].",\n";
    }
    $out .= "    ];\n}\n";

    return $out;
}

// ---------------------------------------------------------------------------------------------
// The generator
// ---------------------------------------------------------------------------------------------

final class PhpGenerator
{
    /** @var list<string> */
    public array $problems = [];

    /**
     * Every model: fqcn (under Tawked\Model) => [props => [wire => schema], required => [wire => true],
     * doc, page => ?array, contract => ?string].
     *
     * @var array<string, array<string, mixed>>
     */
    private array $models = [];

    /** @var array<string, true> models whose schema has been read (the queue for nested objects). */
    private array $seen = [];

    /** @var array<string, array<string, mixed>> */
    private array $types;

    /** @var array<string, mixed> */
    private array $segments;

    /** Endpoint id => the model class of its answer. @var array<string, string> */
    private array $responseClass = [];

    /** Endpoint id => the item class of its page. @var array<string, string> */
    private array $itemClass = [];

    public function __construct(private array $api, private array $account, private array $names)
    {
        $this->types = $api['types'];
        $this->segments = $names['segments'];
    }

    /** @return array<string, string> */
    public function files(): array
    {
        $files = [];
        $this->collectModels();
        foreach ($this->models as $fqcn => $model) {
            $files['src/Model/'.str_replace('\\', '/', $fqcn).'.php'] = $this->modelFile($fqcn, $model);
        }
        foreach ($this->productServices() as $path => $content) {
            $files[$path] = $content;
        }
        foreach ($this->accountServices() as $path => $content) {
            $files[$path] = $content;
        }
        $files['src/Webhook/Events.php'] = $this->eventsFile();
        $files['src/Contract.php'] = $this->contractFile();
        foreach ($this->testFiles() as $path => $content) {
            $files[$path] = $content;
        }

        return $files;
    }

    // ---- models ---------------------------------------------------------------------------------

    private function typeClass(string $type): string
    {
        $class = $this->names['types'][$type] ?? null;
        if ($class === null) {
            $this->problems[] = "class for contract type `{$type}` in sdks/php/scripts/names.php (types)";

            return 'Unknown\\'.$type;
        }

        return $class;
    }

    private function refName(array $schema): ?string
    {
        return isset($schema['$ref']) ? substr($schema['$ref'], strlen('#/types/')) : null;
    }

    private function deref(array $schema): array
    {
        $guard = 0;
        while (isset($schema['$ref']) && $guard++ < 10) {
            $name = $this->refName($schema);
            $schema = $this->types[$name] ?? [];
        }

        return $schema;
    }

    /** The types of a schema, null removed, and whether null was one. @return array{0: list<string>, 1: bool} */
    private function typesOf(array $schema): array
    {
        $type = $schema['type'] ?? null;
        $types = is_array($type) ? $type : ($type === null ? [] : [$type]);
        $nullable = in_array('null', $types, true) || ($schema['nullable'] ?? false) === true;
        $types = array_values(array_filter($types, fn($t) => $t !== 'null'));
        if ($types === [] && isset($schema['enum'])) {
            $kinds = array_unique(array_map(fn($v) => $v === null ? 'null' : (is_int($v) ? 'integer' : (is_bool($v) ? 'boolean' : 'string')), $schema['enum']));
            if (in_array('null', $kinds, true)) {
                $nullable = true;
            }
            $types = array_values(array_filter($kinds, fn($k) => $k !== 'null'));
        }
        if ($types === [] && isset($schema['const'])) {
            $types = [is_bool($schema['const']) ? 'boolean' : (is_int($schema['const']) ? 'integer' : 'string')];
        }
        if ($types === [] && isset($schema['properties'])) {
            $types = ['object'];
        }

        return [$types, $nullable];
    }

    /**
     * An anyOf/oneOf of objects (or a $ref) as one object schema: properties merged, required = in every
     * alternative. Null alternatives make it nullable.
     *
     * @return array{0: array<string, mixed>|null, 1: bool} the merged object schema (null when not all objects), nullable
     */
    private function mergeAlternatives(array $alternatives): array
    {
        $nullable = false;
        $objects = [];
        foreach ($alternatives as $alt) {
            $resolved = $this->deref($alt);
            [$types, $n] = $this->typesOf($resolved);
            if ($types === [] && $n) {
                $nullable = true;

                continue;
            }
            if ($types !== ['object'] && ! isset($resolved['properties'])) {
                return [null, $nullable];
            }
            $nullable = $nullable || $n;
            $objects[] = $resolved;
        }
        if ($objects === []) {
            return [null, $nullable];
        }

        return [$this->mergeObjects($objects), $nullable];
    }

    private function mergeObjects(array $objects): array
    {
        $props = [];
        $required = null;
        foreach ($objects as $o) {
            foreach ((array) ($o['properties'] ?? []) as $name => $schema) {
                if (! isset($props[$name])) {
                    $props[$name] = $schema;
                } elseif ($props[$name] != $schema) {
                    $props[$name] = $this->mergeProperty($props[$name], $schema);
                }
            }
            $req = (array) ($o['required'] ?? []);
            $required = $required === null ? $req : array_values(array_intersect($required, $req));
        }
        // A property only some alternatives carry is not required.
        foreach ($objects as $o) {
            $required = array_values(array_intersect($required ?? [], array_keys((array) ($o['properties'] ?? []))));
        }

        return ['type' => 'object', 'properties' => $props, 'required' => $required ?? [], 'description' => $objects[0]['description'] ?? null];
    }

    private function mergeProperty(array $a, array $b): array
    {
        $ra = $this->deref($a);
        $rb = $this->deref($b);
        [$ta, $na] = $this->typesOf($ra);
        [$tb, $nb] = $this->typesOf($rb);
        if ($ta === $tb && $ta === ['object']) {
            $merged = $this->mergeObjects([$ra, $rb]);
            if ($na || $nb) {
                $merged['nullable'] = true;
            }

            return $merged;
        }
        if ($ta === $tb && $ta === ['array']) {
            $ia = $this->deref((array) ($ra['items'] ?? []));
            $ib = $this->deref((array) ($rb['items'] ?? []));
            [$tia] = $this->typesOf($ia);
            [$tib] = $this->typesOf($ib);
            if ($tia === ['object'] && $tib === ['object']) {
                return ['type' => ($na || $nb) ? ['array', 'null'] : 'array', 'items' => $this->mergeObjects([$ia, $ib])];
            }
        }
        if ($ta === $tb) {
            $out = $ra + ['format' => null];
            if (($ra['format'] ?? null) !== ($rb['format'] ?? null)) {
                unset($out['format']);
            }
            unset($out['enum'], $out['const']);
            if ($na || $nb) {
                $out['type'] = [...$ta, 'null'];
            }

            return array_filter($out, fn($v) => $v !== null);
        }

        return ['description' => $a['description'] ?? null, 'x-mixed' => true];
    }

    /** Registers a model for an object schema under a class name (merging when the class exists). */
    private function addModel(string $fqcn, array $schema, ?string $contract = null): void
    {
        $schema = $this->deref($schema);
        if (isset($schema['anyOf']) || isset($schema['oneOf'])) {
            [$merged] = $this->mergeAlternatives($schema['anyOf'] ?? $schema['oneOf']);
            $schema = $merged ?? ['type' => 'object', 'properties' => []];
        }
        $props = (array) ($schema['properties'] ?? []);
        $required = array_fill_keys((array) ($schema['required'] ?? []), true);
        if (isset($this->models[$fqcn])) {
            $existing = $this->models[$fqcn];
            $merged = $this->mergeObjects([
                ['properties' => $existing['props'], 'required' => array_keys($existing['required'])],
                ['properties' => $props, 'required' => array_keys($required)],
            ]);
            $this->models[$fqcn]['props'] = $merged['properties'];
            $this->models[$fqcn]['required'] = array_fill_keys($merged['required'], true);
            if ($contract !== null && $this->models[$fqcn]['contract'] === null) {
                $this->models[$fqcn]['contract'] = $contract;
            }

            return;
        }
        $this->models[$fqcn] = [
            'props' => $props,
            'required' => $required,
            'doc' => $schema['description'] ?? null,
            'page' => null,
            'contract' => $contract,
            'created' => false,
        ];
    }

    private function collectModels(): void
    {
        foreach ($this->api['endpoints'] as $e) {
            $sdk = $e['sdk'];
            if (($sdk['response']['content'] ?? 'json') === 'binary') {
                // A download (a received e-mail's raw message or attachment): the method answers the bytes, no model.
                $this->responseClass[$e['id']] = null;

                continue;
            }
            $group = pascal($sdk['namespace'][0], $this->segments);
            $schema = $e['response']['schema'];
            $class = $this->names['responses'][$e['id']] ?? null;
            if ($class === null) {
                $ref = $sdk['response']['type'] ?? null;
                $class = $ref !== null ? $this->typeClass($ref) : $group.'\\'.$sdk['type_base'].'Response';
            }
            $this->responseClass[$e['id']] = $class;
            $this->addModel($class, $schema, $sdk['response']['type'] ?? null);
            $statuses = (array) $e['response']['status'];
            if (in_array(201, $statuses, true) && in_array(200, $statuses, true)) {
                // 201 made it, 200 found it already there (a provision by external_id).
                $this->models[$class]['created'] = true;
            }
            if ($sdk['pagination'] !== null) {
                $p = $sdk['pagination'];
                $this->models[$class]['page'] = $p;
            }
        }
        foreach ($this->types as $name => $schema) {
            if ($name === 'Error') {
                continue;
            }
            $this->addModel($this->typeClass($name), $schema, $name);
        }
        // Webhook data: the application events' types are above; the partner events are inline.
        foreach ($this->api['webhooks']['partner']['events'] as $event => $spec) {
            $class = $this->eventClass($event);
            if ($class !== null && isset($spec['data'])) {
                $this->addModel($class, $spec['data']);
            }
        }
        // OAuth: the exchange's answer and the authorize URL helper's.
        foreach ($this->api['oauth']['endpoints'] as $o) {
            if ($o['sdk']['request']) {
                $this->addModel('OAuth\\'.$o['sdk']['type_base'], $o['response']['schema'], $o['sdk']['type_base']);
                $this->responseClass[$o['id']] = 'OAuth\\'.$o['sdk']['type_base'];
            } else {
                $props = [];
                foreach ($o['sdk']['returns'] as $field) {
                    $props[$field] = ['type' => 'string'];
                }
                $this->addModel('OAuth\\AuthorizeUrl', ['type' => 'object', 'properties' => $props, 'required' => $o['sdk']['returns'], 'description' => 'The URL to send the merchant to, with the state and the PKCE verifier to keep in their session until the return.'], 'AuthorizeUrl');
                $this->responseClass[$o['id']] = 'OAuth\\AuthorizeUrl';
            }
        }

        // Nested objects, breadth first, until none is new.
        do {
            $added = false;
            foreach (array_keys($this->models) as $fqcn) {
                if (isset($this->seen[$fqcn])) {
                    continue;
                }
                $this->seen[$fqcn] = true;
                $added = true;
                foreach ($this->models[$fqcn]['props'] as $wire => $schema) {
                    $this->propInfo($fqcn, (string) $wire, $schema, true);
                }
            }
        } while ($added);

        foreach ($this->api['endpoints'] as $e) {
            if ($e['sdk']['pagination'] !== null) {
                $class = $this->responseClass[$e['id']];
                $info = $this->propInfo($class, $e['sdk']['pagination']['items'], $this->models[$class]['props'][$e['sdk']['pagination']['items']] ?? [], false);
                if ($info['kind'] !== 'models') {
                    $this->problems[] = "the page items `{$e['sdk']['pagination']['items']}` of `{$e['id']}` are not objects";
                } else {
                    $this->itemClass[$e['id']] = $info['class'];
                }
            }
        }
        ksort($this->models);
    }

    private function eventClass(string $event): ?string
    {
        foreach ($this->names['events'] as $prefix => $class) {
            if ($event === $prefix || (str_ends_with($prefix, '.') && str_starts_with($event, $prefix))) {
                return $class;
            }
        }

        return null;
    }

    private function nestedClass(string $parent, string $wire, bool $item): string
    {
        $parts = explode('\\', $parent);
        $short = array_pop($parts);
        $ns = implode('\\', $parts);
        $mapped = $this->names['nested'][$short.'.'.$wire] ?? null;
        if ($mapped !== null) {
            return str_contains($mapped, '\\') ? $mapped : $ns.'\\'.$mapped;
        }

        return $ns.'\\'.$short.pascal($item ? singular($wire) : $wire);
    }

    /**
     * How one property is typed and read. Registers the models it needs when $register.
     *
     * @return array{kind: string, php: string, nullable: bool, class: ?string, doc: ?string, phpdoc: ?string}
     */
    private function propInfo(string $parent, string $wire, array $schema, bool $register): array
    {
        $doc = $schema['description'] ?? null;
        $refName = $this->refName($schema);
        $resolved = $this->deref($schema);
        if ($doc === null) {
            $doc = $resolved['description'] ?? null;
        }
        $nullableExtra = false;
        if (isset($resolved['anyOf']) || isset($resolved['oneOf'])) {
            $alts = $resolved['anyOf'] ?? $resolved['oneOf'];
            $nonNull = array_values(array_filter($alts, fn($a) => ($a['type'] ?? null) !== 'null'));
            $nullableExtra = count($nonNull) !== count($alts);
            if (count($nonNull) === 1) {
                $refName = $this->refName($nonNull[0]) ?? $refName;
                $resolved = $this->deref($nonNull[0]) + ['description' => $doc];
            } else {
                [$merged, $n] = $this->mergeAlternatives($alts);
                if ($merged !== null) {
                    $resolved = $merged;
                    $nullableExtra = $nullableExtra || $n;
                    $refName = null;
                } else {
                    // A union of different kinds (a string or an object, a list or one address): kept as sent.
                    return ['kind' => 'mixed', 'php' => 'mixed', 'nullable' => false, 'class' => null, 'doc' => $doc, 'phpdoc' => null];
                }
            }
        }
        if (($resolved['x-mixed'] ?? false) === true) {
            return ['kind' => 'mixed', 'php' => 'mixed', 'nullable' => false, 'class' => null, 'doc' => $doc, 'phpdoc' => null];
        }
        [$types, $nullable] = $this->typesOf($resolved);
        $nullable = $nullable || $nullableExtra;
        if (count($types) > 1) {
            return ['kind' => 'mixed', 'php' => 'mixed', 'nullable' => false, 'class' => null, 'doc' => $doc, 'phpdoc' => null];
        }
        $type = $types[0] ?? null;

        switch ($type) {
            case 'string':
                if (($resolved['format'] ?? null) === 'date-time') {
                    return ['kind' => 'date', 'php' => 'DateTimeImmutable', 'nullable' => true, 'class' => null, 'doc' => $doc, 'phpdoc' => null];
                }

                return ['kind' => 'string', 'php' => 'string', 'nullable' => $nullable, 'class' => null, 'doc' => $doc, 'phpdoc' => null];
            case 'integer':
                return ['kind' => 'int', 'php' => 'int', 'nullable' => $nullable, 'class' => null, 'doc' => $doc, 'phpdoc' => null];
            case 'number':
                return ['kind' => 'float', 'php' => 'float', 'nullable' => $nullable, 'class' => null, 'doc' => $doc, 'phpdoc' => null];
            case 'boolean':
                return ['kind' => 'bool', 'php' => 'bool', 'nullable' => $nullable, 'class' => null, 'doc' => $doc, 'phpdoc' => null];
            case 'object':
                if (($resolved['properties'] ?? []) === [] && ! isset($resolved['properties']) || ((array) ($resolved['properties'] ?? [])) === []) {
                    $values = $resolved['additionalProperties'] ?? null;
                    $stringMap = is_array($values) && ($values['type'] ?? null) === 'string';

                    return ['kind' => $stringMap ? 'stringMap' : 'object', 'php' => 'array', 'nullable' => $nullable, 'class' => null, 'doc' => $doc, 'phpdoc' => $stringMap ? 'array<string, string>' : 'array<string, mixed>'];
                }
                $class = $refName !== null ? $this->typeClass($refName) : $this->nestedClass($parent, $wire, false);
                if ($register) {
                    $this->addModel($class, $resolved, $refName);
                }

                return ['kind' => 'model', 'php' => $class, 'nullable' => $nullable, 'class' => $class, 'doc' => $doc, 'phpdoc' => null];
            case 'array':
                $items = (array) ($resolved['items'] ?? []);
                $itemRef = $this->refName($items);
                $itemResolved = $this->deref($items);
                if (isset($itemResolved['anyOf']) || isset($itemResolved['oneOf'])) {
                    [$merged] = $this->mergeAlternatives($itemResolved['anyOf'] ?? $itemResolved['oneOf']);
                    if ($merged !== null) {
                        $itemResolved = $merged;
                        $itemRef = null;
                    }
                }
                [$itemTypes] = $this->typesOf($itemResolved);
                if ($itemTypes === ['object'] && ((array) ($itemResolved['properties'] ?? [])) !== []) {
                    $class = $itemRef !== null ? $this->typeClass($itemRef) : $this->nestedClass($parent, $wire, true);
                    if ($register) {
                        $this->addModel($class, $itemResolved, $itemRef);
                    }

                    return ['kind' => 'models', 'php' => 'array', 'nullable' => false, 'class' => $class, 'doc' => $doc, 'phpdoc' => 'list<'.$this->short($class, $parent).'>'];
                }
                if ($itemTypes === ['string']) {
                    return ['kind' => 'strings', 'php' => 'array', 'nullable' => false, 'class' => null, 'doc' => $doc, 'phpdoc' => 'list<string>'];
                }
                if ($itemTypes === ['integer']) {
                    return ['kind' => 'ints', 'php' => 'array', 'nullable' => false, 'class' => null, 'doc' => $doc, 'phpdoc' => 'list<int>'];
                }

                return ['kind' => 'list', 'php' => 'array', 'nullable' => false, 'class' => null, 'doc' => $doc, 'phpdoc' => 'list<mixed>'];
        }

        return ['kind' => 'mixed', 'php' => 'mixed', 'nullable' => false, 'class' => null, 'doc' => $doc, 'phpdoc' => null];
    }

    /** A class's name as written from inside $context's namespace (relative when it shares it). */
    private function short(string $class, string $context): string
    {
        $cns = substr($context, 0, (int) strrpos($context, '\\'));
        $ns = substr($class, 0, (int) strrpos($class, '\\'));

        return $ns === $cns ? substr($class, strrpos($class, '\\') + 1) : '\\Tawked\\Model\\'.$class;
    }

    private function modelFile(string $fqcn, array $model): string
    {
        $ns = substr($fqcn, 0, (int) strrpos($fqcn, '\\'));
        $short = substr($fqcn, strrpos($fqcn, '\\') + 1);
        $uses = ['Tawked\\Internal\\Data' => true, 'Tawked\\Model\\Model' => true];
        $params = [];
        $paramDocs = [];
        $reads = [];
        $usedNames = ['raw' => true, 'meta' => true];
        $propNames = [];
        foreach ($model['props'] as $wire => $schema) {
            $wire = (string) $wire;
            $info = $this->propInfo($fqcn, $wire, $schema, false);
            $name = $this->names['properties'][$short.'.'.$wire] ?? camel($wire);
            if ($name === '' || preg_match('/^[a-z][A-Za-z0-9]*$/', $name) !== 1 || isset($usedNames[$name])) {
                $this->problems[] = "property `{$wire}` of {$fqcn} cannot be a PHP property `{$name}`";

                continue;
            }
            $usedNames[$name] = true;
            $propNames[$wire] = $name;
            $required = isset($model['required'][$wire]);
            $nullable = $info['nullable'] || ! $required;
            $phpType = $info['php'];
            $read = '';
            switch ($info['kind']) {
                case 'string':
                    $read = $nullable ? "Data::nullableString(\$data, '{$wire}')" : "Data::string(\$data, '{$wire}')";
                    break;
                case 'int':
                    $read = $nullable ? "Data::nullableInt(\$data, '{$wire}')" : "Data::int(\$data, '{$wire}')";
                    break;
                case 'float':
                    $read = $nullable ? "Data::nullableFloat(\$data, '{$wire}')" : "Data::float(\$data, '{$wire}')";
                    break;
                case 'bool':
                    $read = $nullable ? "Data::nullableBool(\$data, '{$wire}')" : "Data::bool(\$data, '{$wire}')";
                    break;
                case 'date':
                    $uses['DateTimeImmutable'] = true;
                    $nullable = true;
                    $read = "Data::date(\$data, '{$wire}')";
                    break;
                case 'model':
                    $cls = $this->short($info['class'], $fqcn);
                    $phpType = $cls;
                    $read = $nullable
                        ? "is_array(\$data['{$wire}'] ?? null) ? {$cls}::fromArray(Data::stringKeys(\$data['{$wire}'])) : null"
                        : "{$cls}::fromArray(Data::object(\$data, '{$wire}') ?? [])";
                    break;
                case 'models':
                    $cls = $this->short($info['class'], $fqcn);
                    $nullable = false;
                    $read = "array_map(static fn(array \$item): {$cls} => {$cls}::fromArray(\$item), Data::objects(\$data, '{$wire}'))";
                    break;
                case 'strings':
                    $nullable = false;
                    $read = "Data::strings(\$data, '{$wire}')";
                    break;
                case 'ints':
                    $nullable = false;
                    $read = "Data::ints(\$data, '{$wire}')";
                    break;
                case 'list':
                    $nullable = false;
                    $read = "Data::list(\$data, '{$wire}')";
                    break;
                case 'stringMap':
                    $read = $nullable ? "Data::stringMap(\$data, '{$wire}')" : "Data::stringMap(\$data, '{$wire}') ?? []";
                    break;
                case 'object':
                    // An object of unknown shape stays nullable, as today's models read it.
                    $nullable = true;
                    $read = "Data::object(\$data, '{$wire}')";
                    break;
                default:
                    $nullable = false;
                    $read = "\$data['{$wire}'] ?? null";
            }
            $typeDecl = ($nullable && $phpType !== 'mixed' ? '?' : '').$phpType;
            $docLines = docText($info['doc'], '        ', 100);
            $param = '';
            if ($docLines !== []) {
                $param .= count($docLines) === 1
                    ? '        /** '.substr($docLines[0], strlen('         * '))." */\n"
                    : "        /**\n".implode("\n", $docLines)."\n         */\n";
            }
            $param .= "        public readonly {$typeDecl} \${$name},";
            $params[] = $param;
            if ($info['phpdoc'] !== null) {
                $paramDocs[] = '     * @param '.($nullable ? $info['phpdoc'].'|null' : $info['phpdoc']).' $'.$name;
            }
            $reads[] = '            '.$read.',';
        }

        $page = $model['page'];
        $implements = '';
        $generic = null;
        $pageMethods = '';
        if ($page !== null) {
            $itemsWire = $page['items'];
            $itemInfo = $this->propInfo($fqcn, $itemsWire, $model['props'][$itemsWire] ?? [], false);
            $itemShort = $itemInfo['class'] !== null ? $this->short($itemInfo['class'], $fqcn) : 'mixed';
            $itemsProp = $propNames[$itemsWire] ?? 'data';
            if ($page['style'] === 'cursor') {
                $uses['Tawked\\Pagination\\CursorPage'] = true;
                $implements = ' implements CursorPage';
                $generic = "@implements CursorPage<{$itemShort}>";
                $nextProp = $propNames[$page['next']] ?? null;
                $hasMoreProp = $page['has_more'] !== null ? ($propNames[$page['has_more']] ?? null) : null;
                $pageMethods = "\n    /**\n     * @return list<{$itemShort}>\n     */\n    public function items(): array\n    {\n        return \$this->{$itemsProp};\n    }\n\n"
                    ."    public function cursor(): ?string\n    {\n        return ".($nextProp !== null ? "\$this->{$nextProp}" : 'null').";\n    }\n\n"
                    ."    public function more(): bool\n    {\n        return ".($hasMoreProp !== null ? "\$this->{$hasMoreProp} && \$this->cursor() !== null" : '$this->cursor() !== null').";\n    }\n";
            } else {
                $uses['Tawked\\Pagination\\OffsetPage'] = true;
                $implements = ' implements OffsetPage';
                $generic = "@implements OffsetPage<{$itemShort}>";
                $totalProp = $propNames[$page['total']] ?? 'total';
                $pageMethods = "\n    /**\n     * @return list<{$itemShort}>\n     */\n    public function items(): array\n    {\n        return \$this->{$itemsProp};\n    }\n\n"
                    ."    public function totalItems(): int\n    {\n        return \$this->{$totalProp};\n    }\n";
            }
            $pageMethods .= "\n    /** The class of this page's items. */\n    public const ITEM_CLASS = {$itemShort}::class;\n";
        }

        ksort($uses, SORT_STRING | SORT_FLAG_CASE);
        $out = "<?php\n\ndeclare(strict_types=1);\n\n".HEADER."\n\nnamespace Tawked\\Model\\{$ns};\n\n";
        foreach (array_keys($uses) as $use) {
            $out .= "use {$use};\n";
        }
        $out .= "\n/**\n";
        $docLines = docText($model['doc'], '', 100);
        if ($docLines === []) {
            $docLines = [' * '.($model['contract'] !== null ? "The contract's `{$model['contract']}` object." : 'An object of the Tawked API.')];
        }
        $out .= implode("\n", $docLines)."\n";
        if ($model['contract'] !== null) {
            $out .= " *\n * Contract type: `{$model['contract']}`.\n";
        }
        if ($generic !== null) {
            $out .= " *\n * {$generic}\n";
        }
        $out .= " */\nfinal class {$short} extends Model{$implements}\n{\n";
        if ($params === []) {
            $out .= "    public function __construct()\n    {\n    }\n";
        } else {
            if ($paramDocs !== []) {
                $out .= "    /**\n".implode("\n", $paramDocs)."\n     */\n";
            }
            $out .= "    public function __construct(\n".implode("\n", $params)."\n    ) {}\n";
        }
        $out .= "\n    /**\n     * @param array<string, mixed> \$data\n     */\n    public static function fromArray(array \$data): self\n    {\n";
        if ($reads === []) {
            $out .= "        \$model = new self();\n";
        } else {
            $out .= "        \$model = new self(\n".implode("\n", $reads)."\n        );\n";
        }
        $out .= "        \$model->raw = \$data;\n\n        return \$model;\n    }\n";
        $out .= $pageMethods;
        if (($model['created'] ?? false) === true) {
            $out .= "\n    /**\n     * True when this call created it (201), false when it already existed (200). Null when the\n     * object did not come from such a call.\n     */\n    public function wasCreated(): ?bool\n    {\n        \$status = \$this->meta()?->statusCode;\n\n        return \$status === null ? null : \$status === 201;\n    }\n";
        }
        $out .= "}\n";

        return $out;
    }

    // ---- product services -----------------------------------------------------------------------

    /** @return array<string, string> */
    private function productServices(): array
    {
        $tree = [];
        foreach ($this->api['endpoints'] as $e) {
            $tree[implode('.', $e['sdk']['namespace'])][] = $e;
            $ns = $e['sdk']['namespace'];
            for ($i = 1; $i < count($ns); $i++) {
                $tree[implode('.', array_slice($ns, 0, $i))] ??= [];
            }
        }
        ksort($tree);
        $files = [];
        $roots = [];
        foreach ($tree as $path => $endpoints) {
            $segments = explode('.', $path);
            if (count($segments) === 1) {
                $roots[] = $segments[0];
            }
            $children = [];
            foreach (array_keys($tree) as $other) {
                $o = explode('.', $other);
                if (count($o) === count($segments) + 1 && array_slice($o, 0, count($segments)) === $segments) {
                    $children[] = end($o);
                }
            }
            [$class, $file] = $this->serviceClass('Service', $segments);
            $files[$file] = $this->productServiceFile($class, $segments, $children, $endpoints);
        }
        $files['src/Service/ClientNamespaces.php'] = $this->namespacesTrait('Tawked\\Service', 'ClientNamespaces', 'Service', $roots, 'the product client');

        return $files;
    }

    /** @return array{0: string, 1: string} fqcn and file */
    private function serviceClass(string $kind, array $segments): array
    {
        $parts = array_map(fn($s) => pascal($s, $this->segments), $segments);
        $last = array_pop($parts);
        if ($kind === 'Service') {
            $fqcn = 'Tawked\\Service\\'.($parts === [] ? '' : implode('\\', $parts).'\\').$last.'Service';
            $file = 'src/Service/'.($parts === [] ? '' : implode('/', $parts).'/').$last.'Service.php';
        } else {
            $fqcn = 'Tawked\\Account\\Api\\'.($parts === [] ? '' : implode('\\', $parts).'\\').$last.'Api';
            $file = 'src/Account/Api/'.($parts === [] ? '' : implode('/', $parts).'/').$last.'Api.php';
        }

        return [$fqcn, $file];
    }

    /**
     * How a class refers to a child namespace class: imported by its short name, or fully qualified
     * when that name is the class's own, its base's, or another import's.
     *
     * @param array<string, true> $uses
     */
    private function childRef(string $child, string $self, array &$uses): string
    {
        $short = substr($child, strrpos($child, '\\') + 1);
        $taken = [substr($self, strrpos($self, '\\') + 1) => true, 'Service' => true, 'AccountNamespace' => true, 'Transport' => true];
        foreach (array_keys($uses) as $use) {
            if ($use !== $child) {
                $taken[substr($use, strrpos($use, '\\') + 1)] = true;
            }
        }
        if (isset($taken[$short])) {
            return '\\'.$child;
        }
        $uses[$child] = true;

        return $short;
    }

    private function namespacesTrait(string $ns, string $name, string $kind, array $roots, string $what): string
    {
        $props = [];
        $inits = [];
        $uses = ['Tawked\\Http\\Transport' => true];
        foreach ($roots as $root) {
            [$class] = $this->serviceClass($kind, [$root]);
            $uses[$class] = true;
            $short = substr($class, strrpos($class, '\\') + 1);
            $prop = camel($root);
            $props[] = "    public readonly {$short} \${$prop};";
            $inits[] = "        \$this->{$prop} = new {$short}(\$transport);";
        }
        ksort($uses, SORT_STRING | SORT_FLAG_CASE);
        $out = "<?php\n\ndeclare(strict_types=1);\n\n".HEADER."\n\nnamespace {$ns};\n\n";
        foreach (array_keys($uses) as $use) {
            if (substr($use, 0, (int) strrpos($use, '\\')) !== $ns) {
                $out .= "use {$use};\n";
            }
        }
        $out .= "\n/**\n * The namespaces of {$what}, one per area of the API.\n *\n * @internal the client's base; use the client\n */\nabstract class {$name}\n{\n".implode("\n\n", $props)."\n\n    public function __construct(Transport \$transport)\n    {\n".implode("\n", $inits)."\n    }\n}\n";

        return str_replace("\n\n\n", "\n\n", $out);
    }

    private function productServiceFile(string $fqcn, array $segments, array $children, array $endpoints): string
    {
        $ns = substr($fqcn, 0, (int) strrpos($fqcn, '\\'));
        $short = substr($fqcn, strrpos($fqcn, '\\') + 1);
        $uses = ['Tawked\\Http\\Transport' => true];
        $props = [];
        $inits = [];
        foreach ($children as $child) {
            [$cclass] = $this->serviceClass('Service', [...$segments, $child]);
            $cshort = $this->childRef($cclass, $fqcn, $uses);
            $prop = camel($child);
            $props[] = "    public readonly {$cshort} \${$prop};";
            $inits[] = "        \$this->{$prop} = new {$cshort}(\$transport);";
        }
        $methods = [];
        foreach ($endpoints as $e) {
            $methods[] = $this->productMethod($e, $uses);
        }
        $uses['Tawked\\Service\\Service'] = true;
        ksort($uses, SORT_STRING | SORT_FLAG_CASE);
        $out = "<?php\n\ndeclare(strict_types=1);\n\n".HEADER."\n\nnamespace {$ns};\n\n";
        foreach (array_keys($uses) as $use) {
            if (substr($use, 0, (int) strrpos($use, '\\')) !== $ns || $use === 'Tawked\\Service\\Service' && $ns !== 'Tawked\\Service') {
                $out .= "use {$use};\n";
            }
        }
        $title = implode('.', array_map(fn($s) => camel($s), $segments));
        $out .= "\n/**\n * `\$tawked->".str_replace('.', '->', $title).'`'.$this->areaDoc($segments)."\n */\nfinal class {$short} extends Service\n{\n";
        $body = [];
        if ($props !== []) {
            $body[] = implode("\n\n", $props)."\n\n    public function __construct(Transport \$transport)\n    {\n        parent::__construct(\$transport);\n".implode("\n", $inits)."\n    }";
        }
        foreach ($methods as $m) {
            $body[] = $m;
        }
        $out .= implode("\n\n", $body)."\n}\n";

        return $out;
    }

    private function areaDoc(array $segments): string
    {
        return match ($segments[0]) {
            'verify' => ': Tawked Verify, one-time codes by SMS.',
            'whatsapp' => ': WhatsApp template messages and templates (Tawked Notifications).',
            'email' => ': Tawked Email (early access: an account must be granted Email first).',
            'chat' => ': Tawked Chat for connected apps (a connected app\'s key only).',
            'partner' => ': the Partner API (partner keys only).',
            default => '.',
        };
    }

    /**
     * The params of a product endpoint: which fields, in body/query/multipart, required or not.
     *
     * @return array{fields: list<array<string, mixed>>, query: list<string>, body: list<string>, files: list<string>, word: ?string, objects: list<string>}
     */
    private function productParams(array $e): array
    {
        $sdk = $e['sdk'];
        $fields = [];
        $query = [];
        $body = [];
        $files = [];
        $objects = [];
        foreach ($e['request']['fields'] as $f) {
            if (! in_array($f['in'], ['body', 'query', 'multipart'], true)) {
                continue;
            }
            if ($f['name'] === ($sdk['list_param'] ?? null)) {
                continue;
            }
            $fields[] = $f;
            if ($f['in'] === 'query') {
                $query[] = $f['name'];
            } elseif ($f['in'] === 'multipart') {
                $files[] = $f['name'];
            } else {
                $body[] = $f['name'];
                if (($f['type'] ?? null) === 'object') {
                    $objects[] = $f['name'];
                }
            }
        }
        $word = null;
        if ($fields !== []) {
            $word = array_filter($fields, fn($f) => ($f['required'] ?? false) === true) === [] ? 'params?' : 'params';
        }

        return ['fields' => $fields, 'query' => $query, 'body' => $body, 'files' => $files, 'word' => $word, 'objects' => $objects];
    }

    private function phpParamType(array $f): string
    {
        $t = (string) ($f['type'] ?? 'mixed');
        $map = ['string' => 'string', 'integer' => 'int', 'number' => 'float', 'boolean' => 'bool', 'object' => 'array<string, mixed>', 'array' => 'list<mixed>', 'file' => '\\Tawked\\Attachment|\\SplFileInfo|\\Psr\\Http\\Message\\StreamInterface|resource|string'];
        $parts = [];
        foreach (explode('|', $t) as $one) {
            $parts[] = $map[$one] ?? 'mixed';
        }
        if (($f['items'] ?? null) === 'string' || (($f['type'] ?? null) === 'array' && ($f['also_accepts'] ?? []) === ['string'])) {
            $parts = ['string', 'list<string>'];
        }
        if (in_array('constraints', array_keys($f), true) && (($f['constraints']['format'] ?? null) === 'date-time')) {
            $parts = ['string', '\\DateTimeInterface'];
        }
        if (($f['also_accepts'] ?? []) !== [] && ! in_array('string', $parts, true)) {
            $parts[] = 'string';
        }
        if (($f['nullable'] ?? false) === true) {
            $parts[] = 'null';
        }

        return implode('|', array_unique($parts));
    }

    private function retryConst(string $retry): string
    {
        return match ($retry) {
            'safe' => 'Transport::SAFE',
            'idempotent' => 'Transport::IDEMPOTENT',
            'write_once' => 'Transport::WRITE_ONCE',
            default => 'Transport::NEVER',
        };
    }

    private function retryDoc(string $retry, bool $secret): string
    {
        return match ($retry) {
            'safe' => 'Retried after a network failure, a timeout or a 5xx (a read).',
            'idempotent' => 'Retried after a network failure, a timeout or a 5xx under the same `Idempotency-Key` (minted when you pass none): the server keeps the key through a failure.',
            'write_once' => 'Sent once: retried only when the request never left (or the key is still in progress). After a timeout the exception carries the `Idempotency-Key`: call again with it, never a new one. After a 5xx Tawked released the key: check whether it took effect before calling again.',
            default => $secret
                ? 'Never retried: its answer holds a secret shown once (120 s timeout). After a failure, find what it made by its id, then rotate or revoke it.'
                : 'Never retried once it may have reached the server: each try spends something.',
        };
    }

    private function productMethod(array $e, array &$uses): string
    {
        $sdk = $e['sdk'];
        $method = lcfirst(implode('', array_map(fn($w) => ucfirst((string) $w), $sdk['method'])));
        $p = $this->productParams($e);
        $class = $this->responseClass[$e['id']];
        $binary = $class === null;
        if ($binary) {
            $retShort = 'string';
        } else {
            $uses['Tawked\\Model\\'.$class] = true;
            $retShort = substr($class, strrpos($class, '\\') + 1);
        }

        $args = [];
        $docParams = [];
        $pathExpr = "'".$e['path']."'";
        foreach ($sdk['path_params'] as $pp) {
            $var = camel($pp);
            $args[] = "string \${$var}";
            $docParams[] = "     * @param string \${$var} `{$pp}` in the path";
        }
        $pathExpr = $this->pathExpression($e['path'], $sdk['path_params']);
        $listParam = $sdk['list_param'] ?? null;
        $maxItems = null;
        if ($listParam !== null) {
            $var = camel($listParam);
            $args[] = "array \${$var}";
            $lf = null;
            foreach ($e['request']['fields'] as $f) {
                if ($f['name'] === $listParam) {
                    $lf = $f;
                }
            }
            $maxItems = $lf['constraints']['max_items'] ?? null;
            $docParams[] = "     * @param array<int, array<string, mixed>> \${$var} `{$listParam}`, one item each".($maxItems !== null ? " (at most {$maxItems}, refused before sending)" : '');
        }
        if ($p['word'] !== null) {
            $args[] = 'array $params'.($p['word'] === 'params?' ? ' = []' : '');
            $shape = [];
            foreach ($p['fields'] as $f) {
                $opt = ($f['required'] ?? false) === true ? '' : '?';
                $shape[] = $f['name'].$opt.': '.$this->phpParamType($f);
            }
            $docParams[] = '     * @param array{'.implode(', ', $shape).'}|array<string, mixed> $params';
        }
        $args[] = 'array $options = []';
        $keyMax = 128;
        $takesKey = false;
        foreach ($e['request']['fields'] as $f) {
            if ($f['in'] === 'header' && $f['name'] === 'Idempotency-Key') {
                $takesKey = true;
                $keyMax = (int) ($f['constraints']['max_length'] ?? 128);
            }
        }
        $docParams[] = '     * @param array{'.($takesKey ? 'idempotency_key?: string, ' : '').'timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options';

        $doc = [];
        $doc[] = '    /**';
        foreach (docText(rtrim((string) $e['title'], '.').". `{$e['method']} {$e['path']}`.", '    ') as $l) {
            $doc[] = $l;
        }
        if (! empty($e['summary'])) {
            $doc[] = '     *';
            foreach (docText($e['summary'], '    ') as $l) {
                $doc[] = $l;
            }
        }
        $doc[] = '     *';
        $meta = [];
        if (! empty($e['scope'])) {
            $meta[] = 'Scope `'.$e['scope'].'`.';
        }
        $meta[] = $this->retryDoc($sdk['retry'], (bool) $sdk['secret']);
        if ($sdk['stability'] === 'early_access') {
            $meta[] = 'Early access: the account must be granted it first.';
        }
        foreach (docText(implode(' ', $meta), '    ') as $l) {
            $doc[] = $l;
        }
        $codes = array_map(fn($x) => $x['error'], $e['errors'] ?? []);
        if ($codes !== []) {
            $doc[] = '     *';
            foreach (docText('Errors: '.implode(', ', $codes).' (Tawked\\ErrorCode).', '    ') as $l) {
                $doc[] = $l;
            }
        }
        $doc[] = '     *';
        $doc[] = '     * @example $tawked->'.implode('->', array_map(fn($s) => camel($s), $sdk['namespace'])).'->'.$method.'('.$this->exampleArgs($e, $p).');';
        $doc[] = '     *';
        foreach ($docParams as $d) {
            $doc[] = $d;
        }
        if ($binary) {
            $doc[] = '     * @return string the bytes as they were stored (never parsed)';
        }
        $doc[] = '     *';
        $doc[] = '     * @tawked-endpoint '.$e['id'];
        if ($sdk['stability'] === 'early_access') {
            $doc[] = '     * @tawked-stability early_access';
        }
        $doc[] = '     */';

        $call = [];
        $call[] = "method: '{$e['method']}'";
        $call[] = "path: {$pathExpr}";
        $call[] = 'options: $options';
        $call[] = 'retry: '.$this->retryConst($sdk['retry']);
        $call[] = 'timeout: '.number_format((float) $sdk['timeout_s'], 1, '.', '');
        $pre = [];
        if ($e['method'] === 'GET') {
            if ($p['word'] !== null) {
                $call[] = 'query: $params';
            }
        } else {
            if ($p['query'] !== []) {
                $call[] = 'query: self::only($params, '.flat($p['query']).')';
            }
            if ($p['files'] !== []) {
                $call[] = 'multipart: '.self::withoutExpr($p['query']);
                $call[] = 'files: '.flat($p['files']);
            } elseif ($listParam !== null) {
                $var = camel($listParam);
                if ($maxItems !== null) {
                    $pre[] = "        self::assertListSize(\${$var}, '{$listParam}', 1, {$maxItems});";
                }
                $call[] = $p['word'] === null
                    ? "json: ['{$listParam}' => array_values(\${$var})]"
                    : 'json: [...'.self::withoutExpr($p['query']).", '{$listParam}' => array_values(\${$var})]";
            } elseif ($p['word'] !== null) {
                $call[] = 'json: self::body('.self::withoutExpr($p['query']).', '.flat($p['objects']).')';
            }
        }
        if ($takesKey && $keyMax !== 128) {
            $call[] = "keyMax: {$keyMax}";
        }
        if (! $takesKey && in_array($sdk['retry'], ['idempotent', 'write_once'], true)) {
            if ($sdk['retry'] === 'idempotent') {
                $this->problems[] = "`{$e['id']}` is idempotent but takes no Idempotency-Key";
            }
            // The API reads no key here: none is sent (the SDK sends no header the API does not read).
            $call[] = 'takesKey: false';
        }
        if ($sdk['secret']) {
            $call[] = 'secret: true';
        }
        if ($binary) {
            $call[] = 'binary: true';
        }

        $sig = '    public function '.$method.'('.implode(', ', $args).'): '.$retShort;
        if (strlen($sig) > 160) {
            $sig = '    public function '.$method."(\n        ".implode(",\n        ", $args).",\n    ): ".$retShort;
        }
        $body = $pre;
        $body[] = "        \$response = \$this->transport->send(\n            ".implode(",\n            ", $call).",\n        );";
        $body[] = '';
        $body[] = $binary
            ? '        return $response->raw ?? \'\';'
            : "        return {$retShort}::fromArray(\$response->body)->withMeta(\$response->meta);";

        $out = implode("\n", $doc)."\n".$sig."\n    {\n".implode("\n", $body)."\n    }";

        if ($sdk['pagination'] !== null) {
            $out .= "\n\n".$this->iterateMethod($e, $method, $sdk['pagination'], $args, $retShort, 'product');
            $uses['Tawked\\Pagination\\Paginator'] = true;
        }

        return $out;
    }

    private static function withoutExpr(array $query): string
    {
        return $query === [] ? '$params' : 'self::without($params, '.flat($query).')';
    }

    private function iterateMethod(array $e, string $method, array $pg, array $args, string $retShort, string $surface): string
    {
        $item = $surface === 'product' ? $this->itemClass[$e['id']] ?? null : null;
        $itemShort = $item !== null ? '\\Tawked\\Model\\'.$item : 'mixed';
        $pathArgs = [];
        foreach ($e['sdk']['path_params'] as $pp) {
            $pathArgs[] = '$'.camel($pp);
        }
        $pathArgsList = $pathArgs === [] ? '' : implode(', ', $pathArgs).', ';
        $hasParams = false;
        foreach ($args as $a) {
            if (str_starts_with($a, 'array $params')) {
                $hasParams = true;
            }
        }
        $iterArgs = $args;
        if (! $hasParams) {
            array_splice($iterArgs, count($iterArgs) - 1, 0, ['array $params = []']);
        }
        foreach ($iterArgs as $i => $a) {
            if ($a === 'array $params') {
                $iterArgs[$i] = 'array $params = []';
            }
        }
        $call = "\$this->{$method}({$pathArgsList}".($hasParams ? '$page' : '').($hasParams ? ', ' : '').'$options)';
        if (! $hasParams) {
            if ($surface === 'product') {
                $this->problems[] = "the paginated `{$e['id']}` takes no params, so iterate() could not send a cursor";
            }
            $call = "\$this->{$method}Page({$pathArgsList}\$page, \$options)";
        }
        if ($pg['style'] === 'cursor') {
            $runner = "Paginator::cursor(fn(array \$page): {$retShort} => {$call}, \$params, '{$pg['cursor_param']}')";
        } else {
            $runner = "Paginator::offset(fn(array \$page): {$retShort} => {$call}, \$params, '{$pg['offset_param']}')";
        }
        $docItem = $surface === 'product' ? $itemShort : 'mixed';

        return "    /**\n     * Every item of `{$method}()`, page after page".($pg['style'] === 'cursor' ? " (following `{$pg['next']}`)" : ' (by `offset` until `total`').''.($pg['style'] === 'cursor' ? '' : ')').": the same filters, and `{$pg['limit_param']}` for the page size.\n     *\n     * @param array<string, mixed> \$params\n     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> \$options\n     *\n     * @return \\Generator<int, {$docItem}>\n     */\n"
            .'    public function iterate('.implode(', ', $iterArgs)."): \\Generator\n    {\n        return {$runner};\n    }";
    }

    private function pathExpression(string $path, array $params): string
    {
        $parts = preg_split('/(\{[a-z_]+\})/', $path, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY) ?: [];
        $out = [];
        foreach ($parts as $part) {
            if (preg_match('/^\{([a-z_]+)\}$/', $part, $m) === 1) {
                $out[] = 'self::segment($'.camel($m[1]).", '{$m[1]}')";
            } else {
                $out[] = str_quote($part);
            }
        }

        return implode('.', $out);
    }

    private function exampleArgs(array $e, array $p): string
    {
        $ex = $e['sdk']['example'] ?? null;
        $args = [];
        foreach ($e['sdk']['path_params'] as $pp) {
            $args[] = str_quote((string) ($ex['path'][$pp] ?? 'id'));
        }
        $params = [];
        if ($ex !== null) {
            foreach (['query', 'body'] as $where) {
                foreach ((array) ($ex[$where] ?? []) as $k => $v) {
                    $params[$k] = $v;
                }
            }
        }
        $listParam = $e['sdk']['list_param'] ?? null;
        if ($listParam !== null) {
            $args[] = '[...]';
            unset($params[$listParam]);
        }
        if ($p['word'] !== null) {
            $args[] = $params === [] ? '[]' : $this->inlineLit($params);
        }

        return implode(', ', $args);
    }

    private function inlineLit(array $params): string
    {
        $parts = [];
        foreach ($params as $k => $v) {
            $parts[] = str_quote((string) $k).' => '.(is_array($v) ? '[...]' : lit($v));
            if (count($parts) >= 4) {
                $parts[] = '...';
                break;
            }
        }

        return '['.implode(', ', $parts).']';
    }

    // ---- account services -----------------------------------------------------------------------

    /** @return array<string, string> */
    private function accountServices(): array
    {
        $tree = [];
        foreach ($this->account['endpoints'] as $e) {
            $tree[implode('.', $e['sdk']['namespace'])][] = $e;
            $ns = $e['sdk']['namespace'];
            for ($i = 1; $i < count($ns); $i++) {
                $tree[implode('.', array_slice($ns, 0, $i))] ??= [];
            }
        }
        ksort($tree);
        $files = [];
        $roots = [];
        foreach ($tree as $path => $endpoints) {
            $segments = explode('.', $path);
            if (count($segments) === 1) {
                $roots[] = $segments[0];
            }
            $children = [];
            foreach (array_keys($tree) as $other) {
                $o = explode('.', $other);
                if (count($o) === count($segments) + 1 && array_slice($o, 0, count($segments)) === $segments) {
                    $children[] = end($o);
                }
            }
            [$class, $file] = $this->serviceClass('Api', $segments);
            $files[$file] = $this->accountServiceFile($class, $segments, $children, $endpoints);
        }
        $files['src/Account/Api/AccountNamespaces.php'] = $this->namespacesTrait('Tawked\\Account\\Api', 'AccountNamespaces', 'Api', $roots, 'the Account API client');

        return $files;
    }

    private function accountServiceFile(string $fqcn, array $segments, array $children, array $endpoints): string
    {
        $ns = substr($fqcn, 0, (int) strrpos($fqcn, '\\'));
        $short = substr($fqcn, strrpos($fqcn, '\\') + 1);
        $uses = ['Tawked\\Http\\Transport' => true, 'Tawked\\Account\\AccountNamespace' => true];
        $props = [];
        $inits = [];
        foreach ($children as $child) {
            [$cclass] = $this->serviceClass('Api', [...$segments, $child]);
            $cshort = $this->childRef($cclass, $fqcn, $uses);
            $prop = camel($child);
            $props[] = "    public readonly {$cshort} \${$prop};";
            $inits[] = "        \$this->{$prop} = new {$cshort}(\$transport);";
        }
        $methods = [];
        foreach ($endpoints as $e) {
            $methods[] = $this->accountMethod($e, $uses);
        }
        $traitUse = '';
        if ($segments === ['approvals']) {
            $uses['Tawked\\Account\\WaitsForApproval'] = true;
            $traitUse = "    use WaitsForApproval;\n\n";
        }
        ksort($uses, SORT_STRING | SORT_FLAG_CASE);
        $out = "<?php\n\ndeclare(strict_types=1);\n\n".HEADER."\n\nnamespace {$ns};\n\n";
        foreach (array_keys($uses) as $use) {
            if (substr($use, 0, (int) strrpos($use, '\\')) !== $ns) {
                $out .= "use {$use};\n";
            }
        }
        $title = implode('->', array_map(fn($s) => camel($s), $segments));
        $out .= "\n/**\n * `\$account->{$title}`: the Account API's `".implode('.', $segments)."` operations.\n */\nfinal class {$short} extends AccountNamespace\n{\n".$traitUse;
        $body = [];
        if ($props !== []) {
            $body[] = implode("\n\n", $props)."\n\n    public function __construct(Transport \$transport)\n    {\n        parent::__construct(\$transport);\n".implode("\n", $inits)."\n    }";
        }
        foreach ($methods as $m) {
            $body[] = $m;
        }
        $out .= implode("\n\n", $body)."\n}\n";

        return $out;
    }

    private function accountMethod(array $e, array &$uses): string
    {
        $sdk = $e['sdk'];
        $method = lcfirst(implode('', array_map(fn($w) => pascal((string) $w), $sdk['method'])));
        $schema = $e['request']['schema'] ?? [];
        $properties = (array) ($schema['properties'] ?? []);
        $required = (array) ($schema['required'] ?? []);
        $word = $properties === [] ? null : ($required === [] ? 'params?' : 'params');
        $args = [];
        $docParams = [];
        foreach ($sdk['path_params'] as $pp) {
            $var = camel($pp);
            $args[] = "string \${$var}";
            $docParams[] = "     * @param string \${$var} `{$pp}` in the path";
        }
        if ($word !== null) {
            $args[] = 'array $params'.($word === 'params?' ? ' = []' : '');
            $shape = [];
            ksort($properties);
            foreach ($properties as $name => $ps) {
                $alias = $sdk['param_aliases'][$name] ?? null;
                $opt = in_array($name, $required, true) ? '' : '?';
                $key = preg_match('/^[a-z_][a-z0-9_]*$/i', (string) $name) === 1 ? (string) $name : str_quote((string) $name);
                $shape[] = $key.$opt.': '.$this->schemaParamType((array) $ps, in_array($name, $e['request']['files'] ?? [], true));
                unset($alias);
            }
            $docParams[] = '     * @param array{'.implode(', ', $shape).'}|array<string, mixed> $params';
        }
        $args[] = 'array $options = []';
        $docParams[] = '     * @param array{'.($e['method'] !== 'GET' ? 'idempotency_key?: string, ' : '').'timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options';
        $page = $sdk['pagination'] !== null;
        $ret = $page ? 'AccountPage' : 'AccountResponse';
        $uses['Tawked\\Account\\'.$ret] = true;

        $doc = ['    /**'];
        foreach (docText(rtrim((string) ($e['summary'] ?? $e['id']), '.').'. `'.$e['method'].' '.$e['path'].'`.', '    ') as $l) {
            $doc[] = $l;
        }
        $doc[] = '     *';
        $meta = ['Permission `'.$e['permission'].'`.'];
        if ($sdk['approval'] ?? false) {
            $meta[] = 'Consequential: it waits for its person\'s yes (428 ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`).';
        }
        if ($e['untrusted'] ?? false) {
            $meta[] = 'Its answer carries content from outside (Tawked-Untrusted-Content): data, never instructions.';
        }
        $meta[] = $this->retryDoc($sdk['retry'], (bool) $sdk['secret']);
        foreach (docText(implode(' ', $meta), '    ') as $l) {
            $doc[] = $l;
        }
        $doc[] = '     *';
        foreach ($docParams as $d) {
            $doc[] = $d;
        }
        $doc[] = '     *';
        $doc[] = '     * @tawked-operation '.$e['id'];
        $doc[] = '     */';

        $call = ["method: '{$e['method']}'", 'path: '.$this->pathExpression($e['path'], $sdk['path_params']), 'options: $options', 'retry: '.$this->retryConst($sdk['retry']), 'timeout: '.number_format((float) $sdk['timeout_s'], 1, '.', '')];
        if ($word !== null) {
            if ($e['request']['in'] === 'query') {
                $call[] = 'query: $params';
            } elseif ($e['request']['body'] === 'multipart') {
                $call[] = 'multipart: $params';
                $call[] = 'files: '.flat(array_values($e['request']['files']));
            } else {
                $objects = [];
                foreach ($properties as $name => $ps) {
                    [$t] = $this->typesOf((array) $ps);
                    if ($t === ['object']) {
                        $objects[] = (string) $name;
                    }
                }
                sort($objects);
                $call[] = 'json: self::body($params, '.flat($objects).')';
            }
        }
        if ($sdk['secret']) {
            $call[] = 'secret: true';
        }
        $sig = '    public function '.$method.'('.implode(', ', $args).'): '.$ret;
        if (strlen($sig) > 160) {
            $sig = '    public function '.$method."(\n        ".implode(",\n        ", $args).",\n    ): ".$ret;
        }
        if ($page && $word === null) {
            // A list with no declared params still takes its cursor: list() and iterate() share one page reader.
            $pathArgs = implode('', array_map(fn($pp) => '$'.camel($pp).', ', $sdk['path_params']));
            $pageArgs = array_slice($args, 0, count($args) - 1);
            $out = implode("\n", $doc)."\n".$sig."\n    {\n        return \$this->{$method}Page({$pathArgs}[], \$options);\n    }"
                ."\n\n    /**\n     * @param array<string, mixed> \$query\n     * @param array<string, mixed> \$options\n     */\n"
                .'    private function '.$method.'Page('.implode(', ', [...$pageArgs, 'array $query', 'array $options']).'): '.$ret
                ."\n    {\n        return {$ret}::from(\$this->transport->send(\n            ".implode(",\n            ", [...$call, 'query: $query']).",\n        ));\n    }";
        } else {
            $out = implode("\n", $doc)."\n".$sig."\n    {\n        return {$ret}::from(\$this->transport->send(\n            ".implode(",\n            ", $call).",\n        ));\n    }";
        }
        if ($page) {
            $out .= "\n\n".$this->iterateMethod($e, $method, $sdk['pagination'], $args, 'AccountPage', 'account');
            $uses['Tawked\\Pagination\\Paginator'] = true;
        }

        return $out;
    }

    private function schemaParamType(array $schema, bool $file): string
    {
        if ($file) {
            return '\\Tawked\\Attachment|\\SplFileInfo|\\Psr\\Http\\Message\\StreamInterface|resource|string|null';
        }
        [$types, $nullable] = $this->typesOf($schema);
        $map = ['string' => 'string', 'integer' => 'int', 'number' => 'float', 'boolean' => 'bool', 'object' => 'array<string, mixed>', 'array' => 'list<mixed>'];
        $parts = [];
        foreach ($types as $t) {
            $parts[] = $map[$t] ?? 'mixed';
        }
        if ($parts === []) {
            $parts[] = 'mixed';
        }
        if ($nullable && ! in_array('mixed', $parts, true)) {
            $parts[] = 'null';
        }

        return implode('|', array_unique($parts));
    }

    // ---- webhook events, contract constants ----------------------------------------------------

    private function eventsFile(): string
    {
        $events = [];
        foreach ($this->api['webhooks']['application']['event_data'] as $event => $spec) {
            $data = $spec['data'];
            $alts = $data['anyOf'] ?? [$data];
            $classes = [];
            foreach ($alts as $alt) {
                $name = $this->refName($alt);
                if ($name !== null) {
                    $classes[] = $this->typeClass($name);
                }
            }
            $events[$event] = ['side' => 'application', 'classes' => $classes];
        }
        foreach ($this->api['webhooks']['partner']['events'] as $event => $spec) {
            $class = $this->eventClass($event);
            if ($class === null && isset($spec['data']['$ref'])) {
                $class = $this->typeClass((string) $this->refName($spec['data']));
            }
            if ($class === null && str_starts_with($event, 'verification.')) {
                $class = $this->typeClass('PartnerVerificationEventData');
            }
            if ($class === null) {
                $this->problems[] = "class for the partner webhook event `{$event}` in sdks/php/scripts/names.php (events)";

                continue;
            }
            $existing = $events[$event]['classes'] ?? [];
            $events[$event] = ['side' => isset($events[$event]) ? 'both' : 'partner', 'classes' => array_values(array_unique([...$existing, $class]))];
        }
        ksort($events);
        $required = [];
        foreach ($events as $e) {
            foreach ($e['classes'] as $c) {
                $required[$c] = array_keys($this->models[$c]['required'] ?? []);
                sort($required[$c]);
            }
        }
        ksort($required);

        $out = "<?php\n\ndeclare(strict_types=1);\n\n".HEADER."\n\nnamespace Tawked\\Webhook;\n\n";
        $out .= "/**\n * Every webhook event of the contract, with the class of its `data`\n * (Tawked\\Model\\WebhookEvent::typedData() reads it). An event this version does not know\n * comes back as Tawked\\Model\\Webhook\\UnknownEvent, never an exception.\n */\nfinal class Events\n{\n";
        foreach (array_keys($events) as $event) {
            $out .= '    public const '.strtoupper(str_replace('.', '_', $event)).' = '.str_quote($event).";\n\n";
        }
        $out .= "    /**\n     * The data classes of each event, the most specific first (an e-mail event of a message of\n     * several recipients carries `recipient`).\n     *\n     * @var array<string, list<class-string<\\Tawked\\Model\\Model>>>\n     */\n    public const DATA = [\n";
        foreach ($events as $event => $e) {
            $classes = $e['classes'];
            usort($classes, fn($a, $b) => count($required[$b]) <=> count($required[$a]));
            $out .= '        self::'.strtoupper(str_replace('.', '_', $event)).' => ['.implode(', ', array_map(fn($c) => '\\Tawked\\Model\\'.$c.'::class', $classes))."],\n";
        }
        $out .= "    ];\n\n    /**\n     * The fields each data class requires, to choose between two classes of one event.\n     *\n     * @var array<class-string<\\Tawked\\Model\\Model>, list<string>>\n     */\n    public const REQUIRED = [\n";
        foreach ($required as $c => $fields) {
            $out .= '        \\Tawked\\Model\\'.$c.'::class => '.str_replace("\n", "\n        ", lit($fields)).",\n";
        }
        $out .= "    ];\n\n    /**\n     * Which webhook sends each event: an application's, the partner account's, or both.\n     *\n     * @var array<string, string>\n     */\n    public const SIDE = [\n";
        foreach ($events as $event => $e) {
            $out .= '        self::'.strtoupper(str_replace('.', '_', $event)).' => '.str_quote($e['side']).",\n";
        }
        $out .= "    ];\n}\n";

        return $out;
    }

    private function contractFile(): string
    {
        $api = $this->api;
        $prefixes = $api['auth']['key_prefixes'];
        $prefixes['account'] = $this->account['auth']['token_prefix'];
        uasort($prefixes, fn($a, $b) => strlen($b) <=> strlen($a) ?: strcmp($a, $b));
        $limits = [];
        foreach ($api['endpoints'] as $e) {
            foreach ($e['request']['fields'] as $f) {
                if ($e['id'] === 'email-send' && $f['name'] === 'attachments') {
                    $limits['email_attachments_max_bytes'] = $f['constraints']['max_total_decoded_bytes'] ?? null;
                    $limits['email_attachments_max_items'] = $f['constraints']['max_items'] ?? null;
                }
                if ($e['id'] === 'email-batch' && $f['name'] === 'emails') {
                    $limits['email_batch_max_items'] = $f['constraints']['max_items'] ?? null;
                }
                if ($e['id'] === 'partner-batch' && $f['name'] === 'applications') {
                    $limits['partner_batch_max_items'] = $f['constraints']['max_items'] ?? null;
                }
            }
        }
        $limits = array_filter($limits, fn($v) => $v !== null);
        ksort($limits);
        $out = "<?php\n\ndeclare(strict_types=1);\n\n".HEADER."\n\nnamespace Tawked;\n\n";
        $out .= "/**\n * The contract's constants: the API version, the key prefixes, what a test key does, the\n * limits an SDK checks before sending, and the e-mail headers the server refuses.\n */\nfinal class Contract\n{\n";
        $out .= "    /** The API version the SDK was generated from (sent in the User-Agent; the API has no request-time versioning). */\n";
        $out .= '    public const API_VERSION = '.str_quote($api['api_version']).";\n\n";
        $out .= "    /**\n     * Each kind of key by its prefix, the longest first: ".str_replace('*/', '', $api['auth']['prefix_rule'])."\n     *\n     * @var array<string, string>\n     */\n    public const KEY_PREFIXES = ".str_replace("\n", "\n    ", lit($prefixes)).";\n\n";
        $out .= "    /**\n     * What a test key (tk_test_, tk_partner_test_) does, per the contract: it is not a sandbox that sends nothing.\n     *\n     * @var list<string>\n     */\n    public const SANDBOX_NOTES = ".str_replace("\n", "\n    ", lit($api['sandbox']['notes'])).";\n\n";
        $out .= "    /** Lifetime sends of a test key per application. */\n    public const SANDBOX_SEND_CAP = ".(int) ($api['sandbox']['send_cap'] ?? 0).";\n\n";
        $out .= "    /**\n     * Limits checked before sending.\n     *\n     * @var array<string, int>\n     */\n    public const LIMITS = ".str_replace("\n", "\n    ", lit($limits)).";\n\n";
        $out .= "    /**\n     * The custom e-mail headers the server refuses (422 header_not_allowed); exported for adapters that\n     * warn early. The server's answer is the one truth; no SDK keeps an allow-list.\n     *\n     * @var list<string>\n     */\n    public const EMAIL_DENIED_HEADERS = ".str_replace("\n", "\n    ", lit(array_values($api['headers_policy']['denied'] ?? []))).";\n";
        $out .= "}\n";

        return $out;
    }

    // ---- generated contract tests --------------------------------------------------------------

    /** @return array<string, string> */
    private function testFiles(): array
    {
        $files = [];
        $files['tests/Generated/ProductContractTest.php'] = $this->productTests();
        $files['tests/Generated/AccountContractTest.php'] = $this->accountTests();
        $files['tests/Generated/SurfaceMap.php'] = $this->surfaceMap();

        return $files;
    }

    private function productTests(): string
    {
        $rows = [];
        foreach ($this->api['endpoints'] as $e) {
            $sdk = $e['sdk'];
            $p = $this->productParams($e);
            $ex = $sdk['example'];
            $method = lcfirst(implode('', array_map(fn($w) => ucfirst((string) $w), $sdk['method'])));
            $args = [];
            foreach ($sdk['path_params'] as $pp) {
                $args[] = str_quote((string) $ex['path'][$pp]);
            }
            $params = [];
            foreach ((array) ($ex['query'] ?? []) as $k => $v) {
                $params[$k] = $v;
            }
            foreach ((array) ($ex['body'] ?? []) as $k => $v) {
                $params[$k] = $v;
            }
            $multipart = [];
            foreach ((array) ($ex['multipart'] ?? []) as $k => $file) {
                $multipart[$k] = $file;
            }
            $listParam = $sdk['list_param'] ?? null;
            if ($listParam !== null) {
                $args[] = lit($params[$listParam] ?? [], 3);
                unset($params[$listParam]);
            }
            $paramCode = lit($this->objectsAsStd($params, $p['objects']), 3);
            if ($multipart !== []) {
                $parts = [];
                foreach ($multipart as $k => $file) {
                    $parts[] = str_quote((string) $k).' => \\Tawked\\Attachment::fromBytes(base64_decode('.str_quote($file['content_base64']).'), '.str_quote($file['filename']).', '.str_quote($file['content_type']).')';
                }
                $paramCode = $params === [] ? '['.implode(', ', $parts).']' : rtrim(substr($paramCode, 0, -1)).'    '.implode(",\n", $parts).",\n            ]";
            }
            if ($p['word'] !== null) {
                $args[] = $paramCode;
            }
            $args[] = '$o';
            $chain = implode('->', array_map(fn($s) => camel($s), $sdk['namespace']));
            $closure = "static fn(TawkedClient \$c, array \$o): mixed => \$c->{$chain}->{$method}(".implode(', ', $args).')';
            $iterate = null;
            if ($sdk['pagination'] !== null) {
                $iterArgs = [];
                foreach ($sdk['path_params'] as $pp) {
                    $iterArgs[] = str_quote((string) $ex['path'][$pp]);
                }
                $iterArgs[] = '[]';
                $iterArgs[] = '$o';
                $iterate = "static fn(TawkedClient \$c, array \$o): \\Generator => \$c->{$chain}->iterate(".implode(', ', $iterArgs).')';
            }
            $errors = [];
            foreach ($e['errors'] ?? [] as $err) {
                $errors[$err['error']] = (int) $err['http'];
            }
            $expectedPath = $e['path'];
            foreach ($sdk['path_params'] as $pp) {
                $expectedPath = str_replace('{'.$pp.'}', rawurlencode((string) $ex['path'][$pp]), $expectedPath);
            }
            $takesKey = false;
            foreach ($e['request']['fields'] as $f) {
                if ($f['in'] === 'header' && $f['name'] === 'Idempotency-Key') {
                    $takesKey = true;
                }
            }
            $keyed = $takesKey && in_array($sdk['retry'], ['idempotent', 'write_once'], true);
            $key = $this->connectedApp($e) ? 'APP_KEY' : ($this->partnerOnly($e) ? 'PARTNER_KEY' : 'KEY');
            $row = "        '{$e['id']}' => [\n"
                ."            'id' => '{$e['id']}',\n"
                ."            'call' => '".implode('.', array_map(fn($s) => camel($s), $sdk['namespace'])).'.'.$method."',\n"
                ."            'key' => self::{$key},\n"
                ."            'method' => '{$e['method']}',\n"
                .'            \'path\' => '.str_quote($expectedPath).",\n"
                .'            \'query\' => '.str_replace("\n", "\n            ", lit((array) ($ex['query'] ?? []), 0)).",\n"
                .'            \'body\' => '.str_replace("\n", "\n            ", lit($ex['body'] ?? null, 0)).",\n"
                .'            \'multipart\' => '.str_replace("\n", "\n            ", lit(array_map(fn($f) => ['filename' => $f['filename'], 'sha256' => hash('sha256', (string) base64_decode($f['content_base64']))], $multipart), 0)).",\n"
                ."            'retry' => '{$sdk['retry']}',\n"
                .'            \'keyed\' => '.($keyed ? 'true' : 'false').",\n"
                .'            \'secret\' => '.($sdk['secret'] ? 'true' : 'false').",\n"
                .'            \'pagination\' => '.($sdk['pagination'] === null ? 'null' : str_quote($sdk['pagination']['style'])).",\n"
                .($this->responseClass[$e['id']] === null ? "            'returns' => 'string',\n" : "            'returns' => \\Tawked\\Model\\{$this->responseClass[$e['id']]}::class,\n")
                .'            \'errors\' => '.str_replace("\n", "\n            ", lit($errors, 0)).",\n"
                ."            'call_fn' => {$closure},\n"
                .'            \'iterate_fn\' => '.($iterate ?? 'null').",\n"
                .'        ],';
            $rows[] = $row;
        }

        return "<?php\n\ndeclare(strict_types=1);\n\n".HEADER."\n\nnamespace Tawked\\Tests\\Generated;\n\nuse Tawked\\TawkedClient;\nuse Tawked\\Tests\\Support\\ProductContractCase;\n\n"
            ."/**\n * For every product endpoint, from its first example request: the request the\n * mock receives, the typed answer, every documented error, the retries of its class and its pages.\n * The tests are ProductContractCase's; this file holds the contract's data.\n */\nfinal class ProductContractTest extends ProductContractCase\n{\n"
            ."    /**\n     * @return array<string, array<string, mixed>>\n     */\n    public static function endpointRows(): array\n    {\n        return [\n".implode("\n", array_map(fn($r) => '    '.str_replace("\n", "\n    ", $r), $rows))."\n        ];\n    }\n}\n";
    }

    private function connectedApp(array $e): bool
    {
        return in_array($e['auth'], ['connected_app'], true) || in_array($e['id'], ['whatsapp-template-create', 'whatsapp-template-update', 'whatsapp-template-delete'], true);
    }

    private function partnerOnly(array $e): bool
    {
        return str_starts_with($e['id'], 'partner-');
    }

    /** Request examples hold `{}` where the schema says object: a stdClass in PHP, so it encodes as {}. */
    private function objectsAsStd(array $params, array $objects): array
    {
        foreach ($objects as $name) {
            if (array_key_exists($name, $params) && $params[$name] === []) {
                $params[$name] = new stdClass();
            }
        }

        return $params;
    }

    private function accountTests(): string
    {
        $statusCodes = [401 => 'unauthorized', 403 => 'insufficient_permission', 404 => 'not_found', 409 => 'mock_conflict', 410 => 'approval_expired', 422 => 'validation_failed', 428 => 'approval_required', 429 => 'rate_limited'];
        $rows = [];
        foreach ($this->account['endpoints'] as $e) {
            $sdk = $e['sdk'];
            $method = lcfirst(implode('', array_map(fn($w) => pascal((string) $w), $sdk['method'])));
            $schema = $e['request']['schema'] ?? [];
            $properties = (array) ($schema['properties'] ?? []);
            $required = (array) ($schema['required'] ?? []);
            $args = [];
            $path = $e['path'];
            foreach ($sdk['path_params'] as $pp) {
                // approvals.show reads an approval the mock keeps: one asked for first.
                $args[] = $e['id'] === 'approvals.show' ? 'self::pendingApproval($c)' : str_quote('mock_'.$pp);
                $path = str_replace('{'.$pp.'}', 'mock_'.$pp, $path);
            }
            $params = [];
            $files = $e['request']['files'] ?? [];
            foreach ($required as $name) {
                if (in_array($name, $files, true)) {
                    continue;
                }
                $params[$name] = $this->sampleValue((array) ($properties[$name] ?? []));
            }
            $paramCode = lit($params, 3);
            $filesCode = [];
            foreach ($files as $name) {
                if (in_array($name, $required, true) || $e['request']['body'] === 'multipart') {
                    $filesCode[] = str_quote($name)." => \\Tawked\\Attachment::fromBytes('mock file', 'mock.txt', 'text/plain')";
                }
            }
            if ($filesCode !== []) {
                $paramCode = $params === [] ? '['.implode(', ', $filesCode).']' : rtrim(substr($paramCode, 0, -1)).'    '.implode(",\n", $filesCode).",\n            ]";
            }
            if ($properties !== []) {
                $args[] = $paramCode;
            }
            $args[] = '$o';
            $chain = implode('->', array_map(fn($s) => camel($s), $sdk['namespace']));
            $closure = "static fn(TawkedAccountClient \$c, array \$o): mixed => \$c->{$chain}->{$method}(".implode(', ', $args).')';
            $iterate = null;
            if ($sdk['pagination'] !== null) {
                $iterArgs = [];
                foreach ($sdk['path_params'] as $pp) {
                    $iterArgs[] = str_quote('mock_'.$pp);
                }
                $iterArgs[] = $properties === [] ? '[]' : $paramCode;
                $iterArgs[] = '$o';
                $iterate = "static fn(TawkedAccountClient \$c, array \$o): \\Generator => \$c->{$chain}->iterate(".implode(', ', $iterArgs).')';
            }
            $errors = [];
            foreach ($e['errors'] as $status) {
                if (isset($statusCodes[$status])) {
                    $errors[$statusCodes[$status]] = (int) $status;
                }
            }
            if (! ($sdk['approval'] ?? false)) {
                unset($errors['approval_required'], $errors['approval_expired']);
            }
            $rows[] = "        '{$e['id']}' => [\n"
                ."            'id' => '{$e['id']}',\n"
                ."            'call' => '".implode('.', array_map(fn($s) => camel($s), $sdk['namespace'])).'.'.$method."',\n"
                ."            'method' => '{$e['method']}',\n"
                .'            \'path\' => '.str_quote($path).",\n"
                ."            'in' => '{$e['request']['in']}',\n"
                ."            'body_kind' => '{$e['request']['body']}',\n"
                .'            \'params\' => '.str_replace("\n", "\n            ", lit($params, 0)).",\n"
                ."            'retry' => '{$sdk['retry']}',\n"
                .'            \'approval\' => '.(($sdk['approval'] ?? false) ? 'true' : 'false').",\n"
                .'            \'secret\' => '.($sdk['secret'] ? 'true' : 'false').",\n"
                .'            \'pagination\' => '.($sdk['pagination'] === null ? 'null' : str_quote($sdk['pagination']['style'])).",\n"
                .'            \'errors\' => '.str_replace("\n", "\n            ", lit($errors, 0)).",\n"
                ."            'call_fn' => {$closure},\n"
                .'            \'iterate_fn\' => '.($iterate ?? 'null').",\n"
                .'        ],';
        }

        return "<?php\n\ndeclare(strict_types=1);\n\n".HEADER."\n\nnamespace Tawked\\Tests\\Generated;\n\nuse Tawked\\TawkedAccountClient;\nuse Tawked\\Tests\\Support\\AccountContractCase;\n\n"
            ."/**\n * For every Account API operation: the request the mock receives (path\n * parameters and the smallest valid params), the {data} answer, each documented status, the retries\n * of its class, its pages and the 428 loop of a consequential write.\n */\nfinal class AccountContractTest extends AccountContractCase\n{\n"
            ."    /**\n     * @return array<string, array<string, mixed>>\n     */\n    public static function operationRows(): array\n    {\n        return [\n".implode("\n", array_map(fn($r) => '    '.str_replace("\n", "\n    ", $r), $rows))."\n        ];\n    }\n}\n";
    }

    private function sampleValue(array $schema): mixed
    {
        if (isset($schema['enum']) && $schema['enum'] !== []) {
            $first = $schema['enum'][0];
            [$types] = $this->typesOf($schema);
            if (in_array('integer', $types, true) && is_string($first) && ctype_digit($first)) {
                return (int) $first;
            }

            return $first;
        }
        [$types] = $this->typesOf($schema);

        return match ($types[0] ?? 'string') {
            'integer' => (int) ($schema['minimum'] ?? 1),
            'number' => 1,
            'boolean' => true,
            'array' => ['mock'],
            'object' => ['mock' => 'mock'],
            default => ($schema['format'] ?? null) === 'email' ? 'mock@example.com' : (($schema['format'] ?? null) === 'uri' ? 'https://example.com' : 'mock'),
        };
    }

    /**
     * For the surface check: each method, the PHP class it returns and the contract's neutral
     * name of that answer. The surface script checks the class against the built artifact's reflection.
     */
    private function surfaceMap(): string
    {
        $rows = [];
        foreach ($this->api['endpoints'] as $e) {
            $sdk = $e['sdk'];
            $call = implode('.', array_map(fn($s) => camel($s), $sdk['namespace'])).'.'.lcfirst(implode('', array_map(fn($w) => ucfirst((string) $w), $sdk['method'])));
            $class = $this->responseClass[$e['id']];
            if ($class === null) {
                $rows[$call] = ['class' => 'string', 'neutral' => 'binary'];
            } elseif ($sdk['pagination'] !== null) {
                $item = $this->itemClass[$e['id']];
                $schema = $this->deref($e['response']['schema']);
                $items = $schema['properties'][$sdk['pagination']['items']]['items'] ?? [];
                $neutralItem = $this->refName($items) ?? $sdk['type_base'].'Item';
                $rows[$call] = ['class' => 'Tawked\\Model\\'.$class, 'neutral' => 'page<'.$neutralItem.'>', 'item' => 'Tawked\\Model\\'.$item];
            } else {
                $rows[$call] = ['class' => 'Tawked\\Model\\'.$class, 'neutral' => $sdk['type_base'].'Response'];
            }
        }
        foreach ($this->api['oauth']['endpoints'] as $o) {
            $call = 'apps.'.lcfirst(implode('', array_map(fn($w) => ucfirst((string) $w), $o['sdk']['method'])));
            $class = $this->responseClass[$o['id']];
            $rows[$call] = ['class' => 'Tawked\\Model\\'.$class, 'neutral' => substr($class, strrpos($class, '\\') + 1)];
        }
        ksort($rows);

        return "<?php\n\ndeclare(strict_types=1);\n\n".HEADER."\n\n/*\n * Each product and OAuth method: the PHP class it returns (checked against reflection) and the\n * contract's neutral name of that answer, for scripts/surface.php.\n */\n\nreturn ".lit($rows).";\n";
    }
}
