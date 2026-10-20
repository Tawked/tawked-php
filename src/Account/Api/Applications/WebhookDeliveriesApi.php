<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Applications;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountPage;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;
use Tawked\Pagination\Paginator;

/**
 * `$account->applications->webhookDeliveries`: the Account API's `applications.webhook_deliveries` operations.
 */
final class WebhookDeliveriesApi extends AccountNamespace
{
    /**
     * The application's webhook delivery log, newest first, narrowed by `status` (pending,
     * delivered, failed), `event` and `endpoint`: each with its attempts, last HTTP code and error,
     * and the payload sent. `GET /v1/applications/{application}/webhook-deliveries`.
     *
     * Permission `applications:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param array{endpoint?: string, event?: string, log?: string, page?: int, q?: string, status?: string}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation applications.webhook_deliveries.list
     */
    public function list(string $application, array $params = [], array $options = []): AccountPage
    {
        return AccountPage::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/webhook-deliveries',
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
    public function iterate(string $application, array $params = [], array $options = []): \Generator
    {
        return Paginator::cursor(fn(array $page): AccountPage => $this->list($application, $page, $options), $params, 'cursor');
    }

    /**
     * Send a delivered or failed webhook delivery again, as a new delivery to the same endpoint
     * with the same payload and the same event id (your server dedupes on it); answers the new
     * delivery. `POST /v1/applications/{application}/webhook-deliveries/{delivery}/resend`.
     *
     * Permission `applications:write`. Sent once: retried only when the request never left (or the
     * key is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * @param string $application `application` in the path
     * @param string $delivery `delivery` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation applications.webhook_deliveries.resend
     */
    public function resend(string $application, string $delivery, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/webhook-deliveries/'.self::segment($delivery, 'delivery').'/resend',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }
}
