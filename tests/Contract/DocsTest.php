<?php

declare(strict_types=1);

namespace Tawked\Tests\Contract;

use PHPUnit\Framework\Attributes\DataProvider;
use Tawked\Tests\Support\Mock;
use Tawked\Tests\TestCase;

/**
 * Every PHP block of the README and of the held docs pages (docs/site-held/sdks/php.mdx and account-api.mdx) passes
 * `php -l` and runs against the mock, after a preamble that makes the
 * clients and values the blocks name. A block marked lint-only (an HTML or MDX comment right above
 * it) is a webhook endpoint, a return page or a client made for a fixed port: linted, not run.
 */
final class DocsTest extends TestCase
{
    private const PAGES = [
        'README.md' => __DIR__.'/../../README.md',
        'php.mdx' => __DIR__.'/../../../../docs/site-held/sdks/php.mdx',
        'account-api.mdx' => __DIR__.'/../../../../docs/site-held/sdks/account-api.mdx',
    ];

    /**
     * @return iterable<string, array{string, bool}>
     */
    public static function blocks(): iterable
    {
        foreach (self::PAGES as $page => $path) {
            if (! is_file($path)) {
                continue;
            }
            $text = (string) file_get_contents($path);
            preg_match_all('/((?:<!--|\{\/\*)[^\n]*?(?:-->|\*\/\})\n)?```php\n(.*?)```/s', $text, $matches, PREG_SET_ORDER);
            foreach ($matches as $i => $match) {
                $lintOnly = str_contains($match[1], 'lint-only');
                yield "{$page} #{$i}" => [$match[2], $lintOnly];
            }
        }
    }

    public function test_both_pages_have_blocks(): void
    {
        $pages = [];
        foreach (self::blocks() as $name => $block) {
            $pages[explode(' ', $name)[0]] = true;
        }
        // The held docs pages live in Tawked's monorepo; a standalone checkout of this package has the README alone.
        $present = array_map(static fn(): bool => true, array_filter(self::PAGES, 'is_file'));
        self::assertArrayHasKey('README.md', $present);
        self::assertSame($present, $pages);
    }

    #[DataProvider('blocks')]
    public function test_the_block_lints_and_runs_against_the_mock(string $code, bool $lintOnly): void
    {
        $file = (string) tempnam(sys_get_temp_dir(), 'tawked-doc').'.php';
        file_put_contents($file, "<?php\n\ndeclare(strict_types=1);\n\n".$code);
        exec(escapeshellarg(PHP_BINARY).' -l '.escapeshellarg($file).' 2>&1', $lint, $status);
        self::assertSame(0, $status, implode("\n", $lint));
        unlink($file);
        if ($lintOnly) {
            return;
        }

        Mock::reset();
        $script = (string) tempnam(sys_get_temp_dir(), 'tawked-doc-run').'.php';
        file_put_contents($script, "<?php\n\n".self::uses($code)."\n".self::preamble()."\n// ---- the block ----\n".self::body($code)."\n");
        $env = [
            'TAWKED_BASE_URL' => Mock::url(),
            'TAWKED_API_KEY' => self::KEY,
            'TAWKED_PARTNER_KEY' => self::PARTNER_KEY,
            'TAWKED_TOKEN' => 'tk_acct_dGVzdHRva2VuMDAwMDAwMDAwMDAwMDAwMA',
            'PATH' => (string) getenv('PATH'),
        ];
        $process = proc_open([PHP_BINARY, $script], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, null, $env);
        self::assertIsResource($process);
        $out = (string) stream_get_contents($pipes[1]);
        $err = (string) stream_get_contents($pipes[2]);
        $exit = proc_close($process);
        unlink($script);
        self::assertSame(0, $exit, "The block failed against the mock:\n{$out}\n{$err}");
        self::assertSame('', $err);
    }

    /** The clients and values the blocks use, all on the mock. */
    private static function preamble(): string
    {
        $autoload = var_export(realpath(__DIR__.'/../../vendor/autoload.php'), true);

        return <<<PHP
            require {$autoload};
            \$mockOptions = ['sleep' => static function (float \$s): void {}];
            \$key = getenv('TAWKED_API_KEY');
            \$tawked = new Tawked\\TawkedClient(null, \$mockOptions);
            \$partner = new Tawked\\TawkedClient(getenv('TAWKED_PARTNER_KEY'), \$mockOptions);
            \$connectionKey = 'tk_live_app_dGVzdC1rZXktbmV2ZXItcmVhbC0wMDAw';
            \$connection = new Tawked\\TawkedClient(\$connectionKey, \$mockOptions);
            // The mock approves the approval of a consequential call at once.
            \$account = new Tawked\\TawkedAccountClient(null, ['headers' => ['X-Mock-Approval' => 'approved'], ...\$mockOptions]);
            \$code = '123456';
            \$phone = '0551234567';
            \$templateId = '1234567890';
            \$applicationId = 'app_8f2a';
            \$clientId = 'app_mock_client';
            \$started = \$tawked->verify->start(['to' => '0551234567']);
            PHP;
    }

    /** The block's `use` lines, which must come first in the file. */
    private static function uses(string $code): string
    {
        preg_match_all('/^use [^;]+;$/m', $code, $m);

        return implode("\n", $m[0]);
    }

    private static function body(string $code): string
    {
        return (string) preg_replace('/^use [^;]+;$/m', '', $code);
    }
}
