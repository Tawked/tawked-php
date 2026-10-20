<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Clients;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountPage;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;
use Tawked\Pagination\Paginator;

/**
 * `$account->clients->deliveries`: the Account API's `clients.deliveries` operations.
 */
final class DeliveriesApi extends AccountNamespace
{
    /**
     * List the webhook's deliveries, newest first: each with its event, status (pending, delivered,
     * failed), attempts, last answer, payload and the client application it was about. `GET
     * /v1/clients/api/deliveries`.
     *
     * Permission `clients:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param array{event?: string, page?: int, q?: string, status?: string}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation clients.deliveries.list
     */
    public function list(array $params = [], array $options = []): AccountPage
    {
        return AccountPage::from($this->transport->send(
            method: 'GET',
            path: '/v1/clients/api/deliveries',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
            query: $params,
        ));
    }

    /**
     * Every item of `list()`, page after page (following `next_cursor`): the same filters, and `limit` for the page size.
     *
     * @param array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @return \Generator<int, mixed>
     */
    public function iterate(array $params = [], array $options = []): \Generator
    {
        return Paginator::cursor(fn(array $page): AccountPage => $this->list($page, $options), $params, 'cursor');
    }

    /**
     * Send one failed delivery again, now; answers whether it was queued (a delivery that did not
     * fail is left as it is). `POST /v1/clients/api/deliveries/{delivery}/retry`.
     *
     * Permission `clients:write`. Sent once: retried only when the request never left (or the key
     * is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * @param string $delivery `delivery` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation clients.deliveries.retry
     */
    public function retry(string $delivery, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/clients/api/deliveries/'.self::segment($delivery, 'delivery').'/retry',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * Send every failed delivery again, one more attempt each; answers how many were queued (0 when
     * none had failed). `POST /v1/clients/api/deliveries/retry-failed`.
     *
     * Permission `clients:write`. Sent once: retried only when the request never left (or the key
     * is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation clients.deliveries.retry_failed
     */
    public function retryFailed(array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/clients/api/deliveries/retry-failed',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }
}
