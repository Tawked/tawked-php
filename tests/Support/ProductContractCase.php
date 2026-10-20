<?php

declare(strict_types=1);

namespace Tawked\Tests\Support;

use PHPUnit\Framework\Attributes\DataProvider;
use Tawked\Exception\ApiException;
use Tawked\Exception\InsufficientCreditsException;
use Tawked\Internal\Errors;
use Tawked\Model\Model;
use Tawked\Pagination\Page;
use Tawked\TawkedClient;

/**
 * The tests of every product endpoint; the data is the
 * generated tests/Generated/ProductContractTest.php.
 */
abstract class ProductContractCase extends ContractCase
{
    /**
     * @return array<string, array<string, mixed>>
     */
    abstract public static function endpointRows(): array;

    /**
     * @return iterable<string, array{array<string, mixed>}>
     */
    public static function contractEndpoints(): iterable
    {
        foreach (static::endpointRows() as $id => $row) {
            yield $id => [$row];
        }
    }

    /**
     * @return iterable<string, array{array<string, mixed>, string, int}>
     */
    public static function documentedErrors(): iterable
    {
        foreach (static::endpointRows() as $id => $row) {
            /** @var array<string, int> $errors */
            $errors = $row['errors'];
            foreach ($errors as $code => $status) {
                yield "{$id} {$code}" => [$row, $code, $status];
            }
        }
    }

    /**
     * @return iterable<string, array{array<string, mixed>}>
     */
    public static function keyedEndpoints(): iterable
    {
        foreach (static::endpointRows() as $id => $row) {
            if ($row['keyed'] === true && in_array($row['retry'], ['idempotent', 'write_once'], true)) {
                yield $id => [$row];
            }
        }
    }

    /**
     * @return iterable<string, array{array<string, mixed>}>
     */
    public static function pagedEndpoints(): iterable
    {
        foreach (static::endpointRows() as $id => $row) {
            if ($row['pagination'] !== null) {
                yield $id => [$row];
            }
        }
    }

    /**
     * @param array<string, mixed> $row
     */
    protected function clientFor(array $row): TawkedClient
    {
        return new TawkedClient((string) $row['key'], $this->mockOptions());
    }

    /**
     * @param array<string, mixed> $row
     * @return \Closure(array<string, mixed>=): mixed
     */
    protected function callOf(array $row): \Closure
    {
        $client = $this->clientFor($row);
        $fn = $row['call_fn'];
        self::assertIsCallable($fn);

        return static fn(array $options = []): mixed => $fn($client, $options);
    }

    /**
     * @param array<string, mixed> $row
     */
    #[DataProvider('contractEndpoints')]
    public function test_the_call_sends_the_contract_request_and_types_the_answer(array $row): void
    {
        $result = ($this->callOf($row))();

        if ($row['returns'] === 'string') {
            // A download (a received e-mail's raw message or attachment): the bytes as the server sent them.
            self::assertIsString($result, 'answers the bytes');
            self::assertNotSame('', $result);
            self::assertStringNotContainsString('"error"', $result);
            self::assertStringStartsWith($row['id'] === 'email-received-raw' ? 'From: Sara <sara@example.com>' : '%PDF-', $result);
        } else {
            self::assertTrue(is_a($result, (string) $row['returns']), 'answers '.$row['returns']);
            self::assertInstanceOf(Model::class, $result);
            self::assertNotNull($result->meta());
            self::assertNotNull($result->requestId());
        }

        $requests = Mock::requests();
        self::assertCount(1, $requests);
        $sent = $requests[0];
        self::assertSame($row['method'], $sent['method']);
        self::assertSame($row['path'], $sent['path']);
        self::assertSame($row['id'], $sent['endpoint']);
        self::assertEquals(self::stringQuery((array) $row['query']), $sent['query']);
        $headers = (array) $sent['headers'];
        self::assertSame('Bearer '.$row['key'], $headers['authorization'] ?? null);
        self::assertMatchesRegularExpression('#^tawked-php/\d+\.\d+\.\d+ \(php/[^;]+; api \d{4}-\d{2}\)$#', (string) ($headers['user-agent'] ?? ''));
        self::assertSame('application/json', $headers['accept'] ?? null);
        self::assertSame($row['keyed'], isset($headers['idempotency-key']), 'Idempotency-Key is sent exactly when its class mints one');
        if ($row['keyed'] === true) {
            self::assertInstanceOf(Model::class, $result);
            self::assertSame($headers['idempotency-key'], $result->meta()?->idempotencyKey);
        }

        $multipart = (array) $row['multipart'];
        if ($multipart !== []) {
            foreach ($multipart as $field => $file) {
                self::assertIsArray($file);
                self::assertStringContainsString('name="'.$field.'"; filename="'.$file['filename'].'"', (string) $sent['body']);
            }
            self::assertStringStartsWith('multipart/form-data; boundary=', (string) ($headers['content-type'] ?? ''));
        } elseif ($row['body'] === null || $row['body'] === []) {
            // No params: nothing is sent, which the API reads as the empty object.
            self::assertSame('', $sent['body'], 'no body');
        } else {
            self::assertSame('application/json', $headers['content-type'] ?? null);
            // Parsed JSON, deep-equal: the bytes may differ (key order, escapes), the meaning may not.
            self::assertEquals($row['body'], json_decode((string) $sent['body'], true, 512, JSON_THROW_ON_ERROR));
        }
        Recorder::observe('product', (string) $row['call'], 'called', true);
    }

    /**
     * @param array<string, mixed> $row
     */
    #[DataProvider('documentedErrors')]
    public function test_every_documented_error_becomes_its_exception(array $row, string $code, int $status): void
    {
        $result = self::attempt($this->callOf($row), ['headers' => ['X-Mock-Error' => $code], 'max_retries' => 0]);

        self::assertInstanceOf(ApiException::class, $result, "{$row['id']} {$code}");
        self::assertSame(Errors::classFor($code, $status), $result::class);
        self::assertSame($code, $result->errorCode);
        self::assertSame($code, $result->code());
        self::assertSame($status, $result->statusCode);
        self::assertNotNull($result->requestId);
        self::assertStringContainsString('(request id '.$result->requestId.')', $result->getMessage());
        if ($row['keyed'] === true) {
            self::assertNotNull($result->idempotencyKey(), 'every error of a keyed call names its key');
        }
        match ($code) {
            'insufficient_scope' => self::assertNotNull($result->requiredScope()),
            'template_in_use' => self::assertNotSame([], $result->usedBy()),
            'not_resendable', 'not_cancelable', 'template_not_editable' => self::assertNotNull($result->resourceStatus()),
            default => null,
        };
        if ($result instanceof InsufficientCreditsException && $row['id'] === 'whatsapp-send') {
            self::assertNotNull($result->balanceHalalas());
        }
        Recorder::observe('product', (string) $row['call'], 'errors', true);
    }

    /**
     * @param array<string, mixed> $row
     */
    #[DataProvider('contractEndpoints')]
    public function test_the_retries_follow_the_class_of_the_endpoint(array $row): void
    {
        $this->checkRetries('product', (string) $row['call'], (string) $row['id'], $this->callOf($row), (string) $row['retry'], (bool) $row['keyed']);
    }

    /**
     * @param array<string, mixed> $row
     */
    #[DataProvider('keyedEndpoints')]
    public function test_a_keyed_write_waits_while_its_first_request_is_in_progress(array $row): void
    {
        $this->checkInProgress('product', (string) $row['call'], (string) $row['id'], $this->callOf($row));
    }

    /**
     * @param array<string, mixed> $row
     */
    #[DataProvider('pagedEndpoints')]
    public function test_iterate_walks_every_page_and_stops(array $row): void
    {
        $client = $this->clientFor($row);
        $iterate = $row['iterate_fn'];
        self::assertIsCallable($iterate);
        $first = ($row['call_fn'])($client, []);
        self::assertInstanceOf(Page::class, $first);
        $perPage = count($first->items());
        self::assertGreaterThan(0, $perPage);

        Mock::reset();
        $items = iterator_to_array($iterate($client, ['headers' => ['X-Mock-Pages' => '3']]), false);
        $requests = Mock::requests();

        self::assertCount(3, $requests, 'one request a page, none past the last');
        self::assertCount(3 * $perPage, $items);
        $class = constant($first::class.'::ITEM_CLASS');
        self::assertIsString($class);
        foreach ($items as $item) {
            self::assertTrue(is_a($item, $class), 'every item is a '.$class);
        }
        Recorder::observe('product', (string) $row['call'], 'pages', $row['pagination']);
    }

    /**
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    private static function stringQuery(array $query): array
    {
        $out = [];
        foreach ($query as $name => $value) {
            $out[$name] = is_bool($value) ? ($value ? 'true' : 'false') : (is_array($value) ? $value : (string) $value);
        }

        return $out;
    }
}
