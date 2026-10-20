<?php

declare(strict_types=1);

namespace Tawked\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Psr\Http\Message\RequestInterface;
use Tawked\TawkedClient;

abstract class TestCase extends BaseTestCase
{
    public const KEY = 'tk_live_dGVzdC1rZXktbmV2ZXItcmVhbC0wMDAwMDAwMDAw';

    /** A connected app's key: an application key like any other to the API; the mock knows it by `tk_live_app_`. */
    public const APP_KEY = 'tk_live_app_dGVzdC1rZXktbmV2ZXItcmVhbC0wMDAw';

    public const PARTNER_KEY = 'tk_partner_dGVzdC1rZXktbmV2ZXItcmVhbC0wMDAwMDAwMA';

    /** @var list<RequestInterface> */
    protected array $history = [];

    /** @var list<float> */
    protected array $sleeps = [];

    protected MockHandler $handler;

    /** @var array<string, mixed>|null */
    private static ?array $contract = null;

    /**
     * The API contract (sdks/contract/api.json), copied into tests/fixtures.
     *
     * @return array<string, mixed>
     */
    public static function contract(): array
    {
        if (self::$contract === null) {
            $decoded = json_decode((string) file_get_contents(__DIR__.'/fixtures/api.json'), true, 512, JSON_THROW_ON_ERROR);
            self::assertIsArray($decoded);
            /** @var array<string, mixed> $decoded */
            self::$contract = $decoded;
        }

        return self::$contract;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function endpoints(): array
    {
        $out = [];
        $endpoints = self::contract()['endpoints'];
        self::assertIsArray($endpoints);
        foreach ($endpoints as $endpoint) {
            self::assertIsArray($endpoint);
            /** @var array<string, mixed> $endpoint */
            $out[(string) $endpoint['id']] = $endpoint;
        }

        return $out;
    }

    /**
     * A client whose HTTP answers are queued; every request lands in $this->history.
     *
     * @param list<Response|\Throwable> $responses
     * @param array<string, mixed> $options
     */
    protected function client(array $responses, array $options = [], string $key = self::KEY): TawkedClient
    {
        $this->history = [];
        $this->sleeps = [];
        $this->handler = new MockHandler($responses);
        $stack = HandlerStack::create($this->handler);
        $stack->push(function (callable $next): callable {
            return function (RequestInterface $request, array $options) use ($next) {
                $this->history[] = $request;

                return $next($request, $options);
            };
        });

        return new TawkedClient($key, [
            'http_client' => new Client(['handler' => $stack]),
            'sleep' => function (float $seconds): void {
                $this->sleeps[] = $seconds;
            },
            ...$options,
        ]);
    }

    /**
     * @param array<string, mixed>|list<mixed> $body
     * @param array<string, string> $headers
     */
    protected static function json(int $status, array $body, array $headers = []): Response
    {
        return new Response($status, [
            'Content-Type' => 'application/json',
            'X-Request-Id' => 'req_'.$status.'_'.substr(md5(serialize($body)), 0, 8),
            ...$headers,
        ], json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
    }

    /**
     * An exception as a log would print it, with the frames of the SDK's own code and their
     * arguments (traces keep them in the tests, phpunit.xml.dist), but not the test harness's frames:
     * those hold the test case and the requests it recorded.
     */
    protected static function sdkDump(\Throwable $e): string
    {
        $out = '';
        for ($t = $e; $t !== null; $t = $t->getPrevious()) {
            $frames = array_filter($t->getTrace(), static fn(array $f): bool => str_starts_with((string) ($f['class'] ?? ''), 'Tawked\\') && ! str_starts_with((string) ($f['class'] ?? ''), 'Tawked\\Tests\\'));
            $out .= $t::class.': '.$t->getMessage()."\n".print_r($frames, true).$t->getTraceAsString()."\n";
        }

        return $out;
    }

    protected function request(int $index = 0): RequestInterface
    {
        self::assertArrayHasKey($index, $this->history, "No request #{$index} was sent.");

        return $this->history[$index];
    }

    /**
     * @return array<string, mixed>
     */
    protected function sentJson(int $index = 0): array
    {
        $body = (string) $this->request($index)->getBody();
        $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($decoded);

        /** @var array<string, mixed> $decoded */
        return $decoded;
    }

    /**
     * @return array<string, mixed>
     */
    protected function sentQuery(int $index = 0): array
    {
        parse_str($this->request($index)->getUri()->getQuery(), $query);

        /** @var array<string, mixed> $query */
        return $query;
    }
}
