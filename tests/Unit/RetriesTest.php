<?php

declare(strict_types=1);

namespace Tawked\Tests\Unit;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use Tawked\Exception\ConnectionException;
use Tawked\Exception\IdempotencyException;
use Tawked\Exception\RateLimitException;
use Tawked\Exception\ServerException;
use Tawked\Tests\TestCase;

/**
 * Only what is safe is sent twice: reads, writes whose server keeps the key
 * through a failure (`idempotent`), and anything that never reached the server. A `write_once` call
 * (a Verify start, a WhatsApp send, a template write) is sent once after it may have reached Tawked.
 */
final class RetriesTest extends TestCase
{
    private const ID = 'b7e5c2b0-9c1a-4e2f-8f2a-3a6b0e9d1c44';

    private const STARTED = ['id' => self::ID, 'status' => 'pending', 'expires_at' => '2026-09-01T12:34:56.789Z'];

    public function test_a_read_is_retried_on_a_5xx_with_backoff(): void
    {
        $tawked = $this->client([
            self::json(500, ['error' => 'internal_error']),
            self::json(503, ['error' => 'templates_unavailable']),
            self::json(200, ['data' => []]),
        ]);

        $templates = $tawked->whatsapp->templates->list();

        self::assertSame([], $templates->data);
        self::assertCount(3, $this->history);
        self::assertCount(2, $this->sleeps);
        self::assertGreaterThanOrEqual(0.375, $this->sleeps[0]);
        self::assertLessThanOrEqual(0.5, $this->sleeps[0]);
        self::assertGreaterThanOrEqual(0.75, $this->sleeps[1]);
        self::assertLessThanOrEqual(1.0, $this->sleeps[1]);
    }

    public function test_a_read_gives_up_after_max_retries(): void
    {
        $tawked = $this->client(array_fill(0, 3, self::json(500, ['error' => 'internal_error'])));

        try {
            $tawked->verify->get(self::ID);
            self::fail('No exception thrown.');
        } catch (ServerException $e) {
            self::assertSame('internal_error', $e->errorCode);
        }
        self::assertCount(3, $this->history);
    }

    public function test_max_retries_is_set_per_client_and_per_call(): void
    {
        $tawked = $this->client(array_fill(0, 6, self::json(500, ['error' => 'internal_error'])), ['max_retries' => 0]);

        try {
            $tawked->verify->get(self::ID);
        } catch (ServerException) {
        }
        self::assertCount(1, $this->history);

        try {
            $tawked->verify->get(self::ID, [], ['max_retries' => 4]);
        } catch (ServerException) {
        }
        self::assertCount(6, $this->history);
    }

    public function test_a_send_without_an_idempotency_key_is_never_retried_after_reaching_the_server(): void
    {
        $tawked = $this->client([self::json(502, ['error' => 'send_failed']), self::json(201, self::STARTED)]);

        $this->expectException(ServerException::class);

        try {
            $tawked->verify->start(['to' => '+966551234567']);
        } finally {
            self::assertCount(1, $this->history);
        }
    }

    public function test_a_write_once_send_under_a_key_is_not_retried_after_reaching_the_server(): void
    {
        // The SMS gateway may already have the code when a 502 answers: the key is released on a 5xx.
        $tawked = $this->client([self::json(502, ['error' => 'send_failed']), self::json(201, self::STARTED)]);

        try {
            $tawked->verify->start(['to' => '+966551234567'], ['idempotency_key' => 'signin-1']);
            self::fail('No exception thrown.');
        } catch (ServerException $e) {
            self::assertSame('signin-1', $e->idempotencyKey());
            // The server released the key on the 5xx: the same key would run it again, so it says to look first.
            self::assertStringStartsWith('Idempotency-Key signin-1: the request may have been applied, and Tawked released the key on this 5xx, so the same key runs it again: check whether it took effect (get or list it by its id or reference) before calling again.', $e->getMessage());
            self::assertStringNotContainsString('to retry safely', $e->getMessage());
        }
        self::assertCount(1, $this->history);
        self::assertSame('signin-1', $this->request()->getHeaderLine('Idempotency-Key'));
    }

    public function test_a_write_once_send_without_a_key_mints_one_and_names_it_after_a_timeout(): void
    {
        $tawked = $this->client([self::connectError('cURL error 28: Operation timed out after 30001 milliseconds with 0 bytes received', 'POST'), self::json(202, [])]);

        try {
            $tawked->whatsapp->messages->send(['to' => '+966551234567', 'template' => 'order_shipped_v1', 'lang' => 'ar']);
            self::fail('No exception thrown.');
        } catch (\Tawked\Exception\TimeoutException $e) {
            $key = $this->request()->getHeaderLine('Idempotency-Key');
            self::assertMatchesRegularExpression('/^[0-9a-f-]{36}$/', $key);
            self::assertSame($key, $e->idempotencyKey());
            self::assertTrue($e->mayHaveBeenApplied());
            self::assertStringStartsWith("Idempotency-Key {$key}:", $e->getMessage());
        }
        self::assertCount(1, $this->history);
    }

    public function test_an_idempotent_send_is_retried_with_the_same_key_and_body(): void
    {
        $accepted = ['id' => self::ID, 'status' => 'accepted', 'from' => ['name' => null, 'address' => 'orders@acme.sa'], 'to' => 'sara@example.com', 'subject' => 'Hi', 'reference' => null, 'price_halalas' => 1, 'created_at' => null];
        $tawked = $this->client([
            self::json(502, ['error' => 'internal_error']),
            self::connectError('cURL error 28: Operation timed out after 30001 milliseconds with 0 bytes received', 'POST'),
            self::json(202, $accepted),
        ]);

        $sent = $tawked->email->messages->send(['from' => 'orders@acme.sa', 'to' => 'sara@example.com', 'subject' => 'Hi', 'text' => 'x']);

        self::assertSame(self::ID, $sent->id);
        self::assertCount(3, $this->history);
        $key = $this->request()->getHeaderLine('Idempotency-Key');
        self::assertNotSame('', $key);
        self::assertSame($key, $sent->meta()?->idempotencyKey);
        foreach ($this->history as $request) {
            self::assertSame($key, $request->getHeaderLine('Idempotency-Key'));
            self::assertSame((string) $this->request()->getBody(), (string) $request->getBody());
        }
    }

    public function test_a_partner_write_under_a_key_is_retried_while_the_first_is_in_progress(): void
    {
        $row = ['external_id' => 'store_88', 'disabled' => true];
        $tawked = $this->client([self::json(409, ['error' => 'idempotency_in_progress']), self::json(200, $row)], [], self::PARTNER_KEY);

        $result = $tawked->partner->applications->suspend('store_88', ['idempotency_key' => 'suspend-88']);

        self::assertTrue($result->disabled);
        self::assertCount(2, $this->history);
    }

    public function test_a_template_write_is_sent_once_with_a_minted_key(): void
    {
        // Meta may already have the template when a 503 answers.
        $deleted = ['id' => '7002', 'name' => 'promo_v1', 'language' => 'en', 'deleted' => true];
        $tawked = $this->client([self::json(503, ['error' => 'templates_unavailable']), self::json(200, $deleted)]);

        try {
            $tawked->whatsapp->templates->delete('7002');
            self::fail('A template write was sent twice.');
        } catch (ServerException $e) {
            self::assertSame('templates_unavailable', $e->errorCode);
            self::assertSame($this->request()->getHeaderLine('Idempotency-Key'), $e->idempotencyKey());
        }
        self::assertCount(1, $this->history);

        // While the first request with the key still runs, the same key is sent again.
        $tawked = $this->client([self::json(409, ['error' => 'idempotency_in_progress']), self::json(200, $deleted)]);
        $result = $tawked->whatsapp->templates->delete('7002', ['idempotency_key' => 'template-7002-delete']);
        self::assertTrue($result->deleted);
        self::assertSame('template-7002-delete', $this->request(1)->getHeaderLine('Idempotency-Key'));
        self::assertSame([1.0], $this->sleeps);
    }

    public function test_a_reused_key_is_never_retried(): void
    {
        $tawked = $this->client([self::json(409, ['error' => 'idempotency_key_reused'])], [], self::PARTNER_KEY);

        $this->expectException(IdempotencyException::class);
        $tawked->partner->applications->suspend('store_88', ['idempotency_key' => 'suspend-88']);
    }

    public function test_check_is_never_retried_once_it_may_have_reached_the_server(): void
    {
        $tawked = $this->client([
            self::json(500, ['error' => 'internal_error']),
            self::connectError('cURL error 28: Operation timed out', 'POST'),
        ]);

        foreach ([ServerException::class, ConnectionException::class] as $expected) {
            try {
                // An Idempotency-Key does not make check safe: the API ignores it there.
                $tawked->verify->check(['id' => self::ID, 'code' => '123456'], ['idempotency_key' => 'k']);
                self::fail('No exception thrown.');
            } catch (\Throwable $e) {
                self::assertInstanceOf($expected, $e);
            }
        }
        self::assertCount(2, $this->history);
        self::assertSame([], $this->sleeps);
    }

    public function test_any_call_is_retried_when_the_connection_never_opened(): void
    {
        $tawked = $this->client([
            self::connectError('cURL error 6: Could not resolve host', 'POST'),
            self::connectError('cURL error 7: Failed to connect', 'POST'),
            self::json(200, ['verified' => true, 'status' => 'verified']),
        ]);

        $result = $tawked->verify->check(['id' => self::ID, 'code' => '123456']);

        self::assertTrue($result->verified);
        self::assertCount(3, $this->history);
    }

    public function test_a_connection_failure_after_the_retries_is_a_connection_exception(): void
    {
        $tawked = $this->client(array_fill(0, 3, self::connectError('cURL error 7: Failed to connect', 'GET')));

        try {
            $tawked->verify->get(self::ID);
            self::fail('No exception thrown.');
        } catch (ConnectionException $e) {
            self::assertStringContainsString('Failed to connect', $e->getMessage());
            // The client's exception carries the request and its Authorization header: only
            // its class and message are kept.
            self::assertStringStartsWith(ConnectException::class.': ', (string) $e->getPrevious()?->getMessage());
            self::assertStringNotContainsString('Bearer', self::sdkDump($e));
        }
        self::assertCount(3, $this->history);
    }

    public function test_too_many_requests_without_retry_after_is_not_retried(): void
    {
        $tawked = $this->client([self::json(429, ['error' => 'too_many_requests']), self::json(200, ['data' => []])]);

        $this->expectException(RateLimitException::class);

        try {
            $tawked->whatsapp->templates->list();
        } finally {
            self::assertCount(1, $this->history);
        }
    }

    public function test_too_many_requests_with_retry_after_waits_that_long(): void
    {
        $tawked = $this->client([
            self::json(429, ['error' => 'too_many_requests'], ['Retry-After' => '2']),
            self::json(200, ['data' => []]),
        ]);

        $tawked->whatsapp->templates->list();

        self::assertCount(2, $this->history);
        self::assertSame([2.0], $this->sleeps);
    }

    public function test_a_retry_after_over_a_minute_fails_with_it(): void
    {
        $tawked = $this->client([self::json(429, ['error' => 'too_many_requests'], ['Retry-After' => '120']), self::json(200, ['data' => []])]);

        try {
            $tawked->whatsapp->templates->list();
            self::fail('No exception thrown.');
        } catch (RateLimitException $e) {
            self::assertSame(120, $e->retryAfter());
        }
        self::assertCount(1, $this->history);
    }

    public function test_check_is_not_retried_even_on_too_many_requests(): void
    {
        // `never`: each check spends an attempt.
        $tawked = $this->client([
            self::json(429, ['error' => 'too_many_requests'], ['Retry-After' => '2']),
            self::json(200, ['verified' => true, 'status' => 'verified']),
        ]);

        $this->expectException(RateLimitException::class);

        try {
            $tawked->verify->check(['id' => self::ID, 'code' => '123456']);
        } finally {
            self::assertCount(1, $this->history);
        }
    }

    public function test_a_retry_after_as_an_http_date_is_read(): void
    {
        $tawked = $this->client([self::json(429, ['error' => 'too_many_requests'], ['Retry-After' => gmdate('D, d M Y H:i:s', time() + 3600).' GMT'])]);

        try {
            $tawked->whatsapp->templates->list();
            self::fail('No exception thrown.');
        } catch (RateLimitException $e) {
            self::assertGreaterThan(3500, (int) $e->retryAfter());
        }
    }

    public function test_a_business_limit_without_retry_after_is_never_retried(): void
    {
        foreach (['daily_cap_reached', 'spend_cap_reached', 'rate_limited'] as $code) {
            $this->history = [];
            $tawked = $this->client([self::json(429, ['error' => $code]), self::json(201, self::STARTED)]);

            try {
                $tawked->verify->start(['to' => '+966551234567'], ['idempotency_key' => 'k']);
                self::fail('No exception thrown for '.$code.'.');
            } catch (RateLimitException $e) {
                self::assertSame($code, $e->errorCode);
            }
            self::assertCount(1, $this->history, $code);
        }
    }

    public function test_a_rate_limit_with_a_short_retry_after_is_waited_on_whatever_its_code(): void
    {
        // Email's per-second limit answers 429 rate_limited with Retry-After: 1:
        // the server's Retry-After, not the code, says the wait is worth it.
        $tawked = $this->client([self::json(429, ['error' => 'rate_limited'], ['Retry-After' => '1']), self::json(201, self::STARTED)]);

        $tawked->verify->start(['to' => '+966551234567'], ['idempotency_key' => 'k']);

        self::assertCount(2, $this->history);
        self::assertSame([1.0], $this->sleeps);
    }

    public function test_client_errors_are_never_retried(): void
    {
        $tawked = $this->client([self::json(404, ['error' => 'not_found']), self::json(200, [])]);

        try {
            $tawked->verify->get(self::ID);
        } catch (\Tawked\Exception\NotFoundException) {
        }

        self::assertCount(1, $this->history);
    }

    public function test_timeouts_go_to_guzzle_per_call(): void
    {
        $tawked = $this->client([self::json(200, ['data' => []]), self::json(200, ['data' => []])], ['timeout' => 12, 'connect_timeout' => 3]);

        $tawked->whatsapp->templates->list();
        self::assertSame(12.0, $this->handler->getLastOptions()['timeout']);
        self::assertSame(3.0, $this->handler->getLastOptions()['connect_timeout']);
        self::assertFalse($this->handler->getLastOptions()['http_errors']);
        self::assertFalse($this->handler->getLastOptions()['allow_redirects']);

        $tawked->whatsapp->templates->list([], ['timeout' => 2.5]);
        self::assertSame(2.5, $this->handler->getLastOptions()['timeout']);
    }

    /**
     * A cURL connection error as the installed Guzzle builds it: Guzzle 7 carries the errno in
     * the handler context, Guzzle 8 only in the message.
     */
    private static function connectError(string $message, string $method): ConnectException
    {
        $class = new \ReflectionClass(ConnectException::class);
        $arguments = [$message, new Request($method, '/')];
        if (($class->getConstructor()?->getNumberOfParameters() ?? 0) >= 4 && preg_match('/^cURL error (\d+):/', $message, $m) === 1) {
            $arguments = [...$arguments, null, ['errno' => (int) $m[1]]];
        }

        return $class->newInstanceArgs($arguments);
    }
}
