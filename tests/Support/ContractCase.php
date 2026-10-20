<?php

declare(strict_types=1);

namespace Tawked\Tests\Support;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise\Create;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Tawked\Exception\ApiException;
use Tawked\Exception\ConnectionException;
use Tawked\Exception\IdempotencyException;
use Tawked\Exception\ServerException;
use Tawked\Exception\TimeoutException;
use Tawked\Tests\TestCase;

/**
 * What the product and Account API contract tests share: the mock, a
 * transport hook that fails an attempt before it leaves or after it was sent, the recorded waits,
 * and the retry checks of each class.
 */
abstract class ContractCase extends TestCase
{
    /** `pre`: the next attempt fails before leaving; `timeout`: it is sent, then times out. */
    protected ?string $fault = null;

    protected function setUp(): void
    {
        Mock::reset();
        $this->sleeps = [];
        $this->fault = null;
    }

    /**
     * The options of a client on the mock, with the hook and the recorded waits.
     *
     * @return array<string, mixed>
     */
    protected function mockOptions(): array
    {
        $stack = HandlerStack::create();
        $stack->push(function (callable $next): callable {
            return function (RequestInterface $request, array $options) use ($next) {
                $fault = $this->fault;
                $this->fault = null;
                if ($fault === 'pre') {
                    return Create::rejectionFor(new ConnectException('cURL error 7: Failed to connect to 127.0.0.1 port 9: Connection refused', $request));
                }
                if ($fault === 'timeout') {
                    return $next($request, $options)->then(static function (ResponseInterface $response) use ($request): never {
                        throw new ConnectException('cURL error 28: Operation timed out after 100 milliseconds with 0 bytes received', $request);
                    });
                }

                return $next($request, $options);
            };
        });

        return [
            'base_url' => Mock::url(),
            'http_client' => new Client(['handler' => $stack]),
            'sleep' => function (float $seconds): void {
                $this->sleeps[] = $seconds;
            },
        ];
    }

    /**
     * Runs a call and answers its result or the exception it threw.
     *
     * @param callable(array<string, mixed>): mixed $call
     * @param array<string, mixed> $options
     */
    protected static function attempt(callable $call, array $options = []): mixed
    {
        try {
            return $call($options);
        } catch (\Throwable $e) {
            return $e;
        }
    }

    /**
     * The retry checks of one method, each recorded for the surface check.
     *
     * @param callable(array<string, mixed>): mixed $call the method, taking its options
     * @param string $retry its class in the contract
     * @param bool $keyed whether a key is minted and sent
     * @param string $endpoint the contract id: only its requests are counted
     * @param (callable(mixed): bool)|null $settles whether a final answer counts as the call's (a result by default)
     */
    protected function checkRetries(string $surface, string $name, string $endpoint, callable $call, string $retry, bool $keyed, ?callable $settles = null): void
    {
        $ok = $settles ?? static fn(mixed $r): bool => ! $r instanceof \Throwable;

        // A 500 (the mock answers it before acting, as the servers release the key).
        Mock::reset();
        $result = self::attempt($call, ['headers' => ['X-Mock-Fault' => '500']]);
        $requests = self::requestsOf($endpoint);
        $keys = array_values(array_unique(array_map(static fn(array $r): string => (string) ($r['headers']['idempotency-key'] ?? ''), $requests)));
        Recorder::observe($surface, $name, 'after_500', count($requests));
        Recorder::observe($surface, $name, 'key_sent', $keys !== [''] && $keys !== []);
        Recorder::observe($surface, $name, 'same_key', count($keys) === 1);
        if (in_array($retry, ['safe', 'idempotent'], true)) {
            self::assertTrue($ok($result), "{$name} is {$retry}: a 500 is retried. Got ".($result instanceof \Throwable ? $result->getMessage() : 'a result'));
            self::assertCount(2, $requests, "{$name}: one retry after a 500");
            self::assertCount(1, $keys, "{$name}: the retry carries the same key");
        } else {
            self::assertInstanceOf(ServerException::class, $result, "{$name} is {$retry}: a 500 is not retried");
            self::assertCount(1, $requests, "{$name} is {$retry}: exactly one request after a 500");
            if ($keyed) {
                self::assertInstanceOf(ApiException::class, $result);
                self::assertNotNull($result->idempotencyKey());
                self::assertSame((string) ($requests[0]['headers']['idempotency-key'] ?? ''), $result->idempotencyKey());
                self::assertStringStartsWith('Idempotency-Key '.$result->idempotencyKey(), $result->getMessage());
            }
        }
        if ($keyed) {
            self::assertNotSame([''], $keys, "{$name}: a key is minted and sent");
        }

        // A timeout once sent.
        Mock::reset();
        $this->fault = 'timeout';
        $result = self::attempt($call);
        $requests = self::requestsOf($endpoint);
        Recorder::observe($surface, $name, 'after_timeout', count($requests));
        if (in_array($retry, ['safe', 'idempotent'], true)) {
            self::assertTrue($ok($result), "{$name} is {$retry}: a timeout is retried");
            self::assertCount(2, $requests);
        } else {
            self::assertInstanceOf(TimeoutException::class, $result, "{$name} is {$retry}: a timeout is not retried");
            self::assertTrue($result->mayHaveBeenApplied());
            self::assertCount(1, $requests, "{$name} is {$retry}: exactly one request after a timeout");
            if ($keyed) {
                self::assertSame((string) ($requests[0]['headers']['idempotency-key'] ?? ''), $result->idempotencyKey());
                self::assertStringStartsWith('Idempotency-Key '.$result->idempotencyKey(), $result->getMessage());
            }
        }

        // A failure before the request left: every class retries it.
        Mock::reset();
        $this->fault = 'pre';
        $result = self::attempt($call);
        $requests = self::requestsOf($endpoint);
        Recorder::observe($surface, $name, 'before_send', $ok($result) && count($requests) === 1 ? 'retried' : 'not_retried');
        self::assertTrue($ok($result), "{$name}: a failure before sending is retried, whatever the class. Got ".($result instanceof \Throwable ? $result::class.': '.$result->getMessage() : ''));
        self::assertCount(1, $requests);
        self::assertNotEmpty($this->sleeps, 'the retry waited');
        $this->sleeps = [];

        // A connection failure before sending, every time: the error says nothing was applied.
        Mock::reset();
        $result = self::attempt(function (array $o) use ($call) {
            $this->fault = 'pre';

            return $call(['max_retries' => 0] + $o);
        });
        self::assertInstanceOf(ConnectionException::class, $result);
        self::assertFalse($result->mayHaveBeenApplied());
        self::assertCount(0, self::requestsOf($endpoint));
    }

    /**
     * The requests the mock received for one endpoint since the last reset.
     *
     * @return list<array<string, mixed>>
     */
    protected static function requestsOf(string $endpoint): array
    {
        return array_values(array_filter(Mock::requests(), static fn(array $r): bool => ($r['endpoint'] ?? null) === $endpoint));
    }

    /**
     * The 409 idempotency_in_progress wait of a keyed write: waited on with the same key and then
     * answered; past the 30 s window, an IdempotencyException whose message starts with the key.
     *
     * @param callable(array<string, mixed>): mixed $call
     */
    protected function checkInProgress(string $surface, string $name, string $endpoint, callable $call, ?callable $settles = null): void
    {
        $ok = $settles ?? static fn(mixed $r): bool => ! $r instanceof \Throwable;
        Mock::reset();
        $this->sleeps = [];
        $result = self::attempt($call, ['headers' => ['X-Mock-Fault' => 'in-progress:3']]);
        $requests = self::requestsOf($endpoint);
        self::assertTrue($ok($result), "{$name}: answered once the first request finished. Got ".($result instanceof \Throwable ? $result->getMessage() : ''));
        self::assertCount(4, $requests);
        self::assertCount(1, array_unique(array_map(static fn(array $r): string => (string) ($r['headers']['idempotency-key'] ?? ''), $requests)));
        self::assertSame([1.0, 2.0, 4.0], $this->sleeps);

        Mock::reset();
        $this->sleeps = [];
        $result = self::attempt($call, ['headers' => ['X-Mock-Fault' => 'in-progress:99;times=99']]);
        self::assertInstanceOf(IdempotencyException::class, $result);
        $key = $result->idempotencyKey();
        self::assertNotNull($key);
        self::assertStringStartsWith("`Idempotency-Key {$key}` is still being processed", $result->getMessage());
        self::assertEqualsWithDelta(30.0, array_sum($this->sleeps), 0.001);
        Recorder::observe($surface, $name, 'in_progress', 'waited');
    }
}
