<?php

declare(strict_types=1);

namespace Tawked\Tests\Support;

use GuzzleHttp\Client;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\SkippedWithMessageException;

/**
 * The local mock of the API (sdks/mock-server), started once per test process with `node`
 * (TAWKED_NODE, else `node` on the PATH), or one already running at TAWKED_MOCK_URL.
 */
final class Mock
{
    /** @var resource|null */
    private static $process = null;

    private static ?string $url = null;

    public static function url(): string
    {
        if (self::$url !== null) {
            return self::$url;
        }
        $given = getenv('TAWKED_MOCK_URL');
        if (is_string($given) && $given !== '') {
            return self::$url = rtrim($given, '/');
        }
        $bin = __DIR__.'/../../../mock-server/bin/tawked-mock.mjs';
        if (! is_file($bin)) {
            throw new SkippedWithMessageException('The mock server (sdks/mock-server) is not beside this package; set TAWKED_MOCK_URL to a running one.');
        }
        $node = getenv('TAWKED_NODE') ?: 'node';
        $process = proc_open([$node, $bin, '--port', '0'], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
        if (! is_resource($process)) {
            Assert::fail('Could not start the mock server with '.$node);
        }
        self::$process = $process;
        $line = (string) fgets($pipes[1]);
        if (preg_match('#(http://127\.0\.0\.1:\d+)#', $line, $match) !== 1) {
            Assert::fail('The mock server did not start: '.$line.stream_get_contents($pipes[2]));
        }
        register_shutdown_function(static function (): void {
            if (is_resource(self::$process)) {
                proc_terminate(self::$process);
                proc_close(self::$process);
            }
        });

        return self::$url = $match[1];
    }

    public static function reset(): void
    {
        (new Client())->post(self::url().'/__mock/reset');
    }

    /**
     * Every request the mock received since the last reset.
     *
     * @return list<array<string, mixed>>
     */
    public static function requests(): array
    {
        $body = (string) (new Client())->get(self::url().'/__mock/requests')->getBody();
        $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        Assert::assertIsArray($decoded);
        Assert::assertIsArray($decoded['data'] ?? null);

        /** @var list<array<string, mixed>> */
        return array_values($decoded['data']);
    }
}
