<?php

declare(strict_types=1);

namespace Tawked\Tests\Support;

use PHPUnit\Framework\Attributes\DataProvider;
use Tawked\Account\AccountPage;
use Tawked\Account\AccountResponse;
use Tawked\Exception\ApiException;
use Tawked\Exception\ApprovalRequiredException;
use Tawked\Http\Transport;
use Tawked\Internal\Errors;
use Tawked\TawkedAccountClient;

/**
 * The tests of every Account API operation; the data
 * is the generated tests/Generated/AccountContractTest.php.
 */
abstract class AccountContractCase extends ContractCase
{
    public const TOKEN = 'tk_acct_dGVzdHRva2VuMDAwMDAwMDAwMDAwMDAwMA';

    /**
     * @return array<string, array<string, mixed>>
     */
    abstract public static function operationRows(): array;

    /**
     * @return iterable<string, array{array<string, mixed>}>
     */
    public static function operations(): iterable
    {
        foreach (static::operationRows() as $id => $row) {
            yield $id => [$row];
        }
    }

    /**
     * @return iterable<string, array{array<string, mixed>, string, int}>
     */
    public static function documentedStatuses(): iterable
    {
        foreach (static::operationRows() as $id => $row) {
            /** @var array<string, int> $errors */
            $errors = $row['errors'];
            foreach ($errors as $code => $status) {
                yield "{$id} {$status}" => [$row, $code, $status];
            }
        }
    }

    /**
     * @return iterable<string, array{array<string, mixed>}>
     */
    public static function writes(): iterable
    {
        foreach (static::operationRows() as $id => $row) {
            if ($row['method'] !== 'GET' && $row['retry'] === 'write_once') {
                yield $id => [$row];
            }
        }
    }

    /**
     * @return iterable<string, array{array<string, mixed>}>
     */
    public static function pagedOperations(): iterable
    {
        foreach (static::operationRows() as $id => $row) {
            if ($row['pagination'] !== null) {
                yield $id => [$row];
            }
        }
    }

    /**
     * Whether a final answer counts as the operation's: a result, or a 428 for a consequential write.
     *
     * @param array<string, mixed> $row
     * @return callable(mixed): bool
     */
    protected static function settles(array $row): callable
    {
        return static fn(mixed $r): bool => ! $r instanceof \Throwable
            || ($row['approval'] === true && $r instanceof ApprovalRequiredException);
    }

    /**
     * An approval the mock keeps, for approvals.show: a consequential write asked once.
     */
    public static function pendingApproval(TawkedAccountClient $client): string
    {
        // Its own client: the hook's armed fault is for the call under test, not this one.
        $own = new TawkedAccountClient(self::TOKEN, ['base_url' => Mock::url()]);
        unset($client);
        try {
            $own->account->sessions->revoke();
        } catch (ApprovalRequiredException $e) {
            return (string) $e->approvalId();
        }
        self::fail('account.sessions.revoke did not wait for an approval');
    }

    protected function accountClient(): TawkedAccountClient
    {
        return new TawkedAccountClient(self::TOKEN, $this->mockOptions());
    }

    /**
     * @param array<string, mixed> $row
     * @return \Closure(array<string, mixed>=): mixed
     */
    protected function callOf(array $row): \Closure
    {
        $client = $this->accountClient();
        $fn = $row['call_fn'];
        self::assertIsCallable($fn);

        return static fn(array $options = []): mixed => $fn($client, $options);
    }

    /**
     * @param array<string, mixed> $row
     */
    #[DataProvider('operations')]
    public function test_the_call_sends_the_operation_and_reads_its_data(array $row): void
    {
        $client = $this->accountClient();
        $call = $row['call_fn'];
        self::assertIsCallable($call);

        if ($row['approval'] === true) {
            // the 428, then waitAndRetry sends the same request with the same key and Tawked-Approval.
            $held = self::attempt(static fn(array $o): mixed => $call($client, $o), ['headers' => ['X-Mock-Approval' => 'approved']]);
            self::assertInstanceOf(ApprovalRequiredException::class, $held, "{$row['id']} waits for its person's yes");
            self::assertNotNull($held->approvalId());
            self::assertNotNull($held->humanAction());
            if ($row['retry'] === 'write_once') {
                self::assertNotNull($held->idempotencyKey());
            }
            $result = $client->approvals->waitAndRetry($held, ['sleep' => static function (float $s): void {}]);
            $requests = Mock::requests();
            $sends = array_values(array_filter($requests, static fn(array $r): bool => $r['endpoint'] === $row['id']));
            self::assertCount(2, $sends);
            self::assertSame($sends[0]['headers']['idempotency-key'] ?? null, $sends[1]['headers']['idempotency-key'] ?? null);
            self::assertSame($held->approvalId(), $sends[1]['headers']['tawked-approval'] ?? null);
            self::assertSame($sends[0]['body'], $sends[1]['body']);
            Recorder::observe('account', (string) $row['call'], 'approval', 'waited');
        } else {
            $result = $call($client, []);
            $sends = self::requestsOf((string) $row['id']);
            self::assertCount(1, $sends);
        }

        self::assertInstanceOf($row['pagination'] !== null ? AccountPage::class : AccountResponse::class, $result);
        self::assertNotNull($result->requestId());
        $sent = $sends[0];
        self::assertSame($row['method'], $sent['method']);
        self::assertMatchesRegularExpression('#^'.str_replace('mock_approval', '[^/]+', preg_quote((string) $row['path'], '#')).'$#', (string) $sent['path']);
        self::assertSame($row['id'], $sent['endpoint']);
        $headers = (array) $sent['headers'];
        self::assertSame('Bearer '.self::TOKEN, $headers['authorization'] ?? null);
        self::assertSame($row['method'] !== 'GET' && $row['retry'] === 'write_once', isset($headers['idempotency-key']), 'a write mints its key');
        if ($row['body_kind'] === 'json' && $row['params'] !== []) {
            self::assertEquals($row['params'], json_decode((string) $sent['body'], true, 512, JSON_THROW_ON_ERROR));
        } elseif ($row['body_kind'] === 'multipart') {
            self::assertStringStartsWith('multipart/form-data; boundary=', (string) ($headers['content-type'] ?? ''));
        } elseif ($row['in'] === 'query') {
            $expected = [];
            foreach ((array) $row['params'] as $name => $value) {
                $expected[$name] = is_bool($value) ? ($value ? 'true' : 'false') : (is_array($value) ? $value : (string) $value);
            }
            self::assertEquals($expected, $sent['query']);
        }
        if ($row['secret'] === true) {
            self::assertIsArray($result->data);
        }
        Recorder::observe('account', (string) $row['call'], 'called', true);
    }

    /**
     * @param array<string, mixed> $row
     */
    #[DataProvider('documentedStatuses')]
    public function test_every_documented_status_becomes_its_exception(array $row, string $code, int $status): void
    {
        $header = $code === 'mock_conflict' ? "{$code}:{$status}" : $code;
        $result = self::attempt($this->callOf($row), ['headers' => ['X-Mock-Error' => $header], 'max_retries' => 0]);

        self::assertInstanceOf(ApiException::class, $result, "{$row['id']} {$status}");
        self::assertSame(Errors::classFor($code, $status, Transport::SURFACE_ACCOUNT), $result::class);
        self::assertSame($status, $result->statusCode);
        self::assertSame($code, $result->errorCode);
        self::assertNotNull($result->requestId);
        self::assertNotNull($result->next(), 'every Account API error says what to call next');
        if ($status === 428) {
            self::assertInstanceOf(ApprovalRequiredException::class, $result);
            self::assertNotNull($result->approval());
            self::assertNotNull($result->humanAction());
        }
        Recorder::observe('account', (string) $row['call'], 'errors', true);
    }

    /**
     * @param array<string, mixed> $row
     */
    #[DataProvider('operations')]
    public function test_the_retries_follow_the_class_of_the_operation(array $row): void
    {
        $keyed = $row['method'] !== 'GET' && $row['retry'] === 'write_once';
        $this->checkRetries('account', (string) $row['call'], (string) $row['id'], $this->callOf($row), (string) $row['retry'], $keyed, self::settles($row));
    }

    /**
     * @param array<string, mixed> $row
     */
    #[DataProvider('writes')]
    public function test_a_write_waits_while_its_first_request_is_in_progress(array $row): void
    {
        $this->checkInProgress('account', (string) $row['call'], (string) $row['id'], $this->callOf($row), self::settles($row));
    }

    /**
     * @param array<string, mixed> $row
     */
    #[DataProvider('pagedOperations')]
    public function test_iterate_follows_next_cursor_to_the_end(array $row): void
    {
        $client = $this->accountClient();
        $iterate = $row['iterate_fn'];
        self::assertIsCallable($iterate);
        $first = ($row['call_fn'])($client, []);
        self::assertInstanceOf(AccountPage::class, $first);
        $perPage = count($first->items());

        Mock::reset();
        $items = iterator_to_array($iterate($client, ['headers' => ['X-Mock-Pages' => '3']]), false);
        self::assertCount(3, Mock::requests());
        self::assertCount(3 * $perPage, $items);
        Recorder::observe('account', (string) $row['call'], 'pages', $row['pagination']);
    }
}
