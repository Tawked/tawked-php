<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Service\Partner\Webhook;

use Tawked\Http\Transport;
use Tawked\Model\Partner\WebhookDelivery;
use Tawked\Model\Partner\WebhookDeliveryPage;
use Tawked\Pagination\Paginator;
use Tawked\Service\Service;

/**
 * `$tawked->partner->webhook->deliveries`: the Partner API (partner keys only).
 */
final class DeliveriesService extends Service
{
    /**
     * List deliveries. `GET /v1/partner/webhook/deliveries`.
     *
     * The delivery log the console shows, newest first: every webhook call with its status
     * (`pending`, `delivered`, `failed`), the attempts so far, the last response code or error, and
     * the next attempt when one is scheduled. Pages with `cursor`; `next_cursor` is null on the
     * last page.
     *
     * Retried after a network failure, a timeout or a 5xx (a read).
     *
     * Errors: internal_error, invalid_request, ip_not_allowed, key_expired, too_many_requests,
     * unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->partner->webhook->deliveries->list(['status' => 'failed', 'limit' => 50]);
     *
     * @param array{status?: string, event?: string, cursor?: string, limit?: int}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint partner-deliveries
     */
    public function list(array $params = [], array $options = []): WebhookDeliveryPage
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/partner/webhook/deliveries',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
            query: $params,
        );

        return WebhookDeliveryPage::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Every item of `list()`, page after page (following `next_cursor`): the same filters, and `limit` for the page size.
     *
     * @param array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @return \Generator<int, \Tawked\Model\Partner\WebhookDelivery>
     */
    public function iterate(array $params = [], array $options = []): \Generator
    {
        return Paginator::cursor(fn(array $page): WebhookDeliveryPage => $this->list($page, $options), $params, 'cursor');
    }

    /**
     * Retry a delivery. `POST /v1/partner/webhook/deliveries/{id}/retry`.
     *
     * One more attempt for a failed delivery, like the console's retry: the delivery goes back to
     * `pending` and is posted again to the URL configured now, with a fresh signature. Only a
     * failed delivery can be retried.
     *
     * Sent once: retried only when the request never left (or the key is still in progress). After
     * a timeout the exception carries the `Idempotency-Key`: call again with it, never a new one.
     * After a 5xx Tawked released the key: check whether it took effect before calling again.
     *
     * Errors: idempotency_in_progress, idempotency_key_reused, internal_error, ip_not_allowed,
     * key_expired, live_key_required, not_found, not_retryable, partner_suspended,
     * too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->partner->webhook->deliveries->retry('7e3c5d9b-0a4f-4c1e-9b8d-2f6a1c3e5d7b');
     *
     * @param string $id `id` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint partner-delivery-retry
     */
    public function retry(string $id, array $options = []): WebhookDelivery
    {
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/partner/webhook/deliveries/'.self::segment($id, 'id').'/retry',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        );

        return WebhookDelivery::fromArray($response->body)->withMeta($response->meta);
    }
}
