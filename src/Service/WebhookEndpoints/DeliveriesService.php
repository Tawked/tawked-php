<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Service\WebhookEndpoints;

use Tawked\Http\Transport;
use Tawked\Model\Webhook\WebhookEndpointDelivery;
use Tawked\Model\WebhookEndpoints\WebhookDeliveryListResponse;
use Tawked\Pagination\Paginator;
use Tawked\Service\Service;

/**
 * `$tawked->webhookEndpoints->deliveries`.
 */
final class DeliveriesService extends Service
{
    /**
     * List deliveries. `GET /v1/webhooks/{id}/deliveries`.
     *
     * Lists an endpoint's deliveries, newest first, a page at a time. Email events' deliveries are
     * kept 30 days. A live key of the application with the explicit `webhooks:manage`: a
     * full-access key does not hold it.
     *
     * Scope `webhooks:manage`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * Errors: application_key_required, insufficient_scope, internal_error, invalid_request,
     * ip_not_allowed, key_expired, live_key_required, not_found, too_many_requests, unauthorized
     * (Tawked\ErrorCode).
     *
     * @example $tawked->webhookEndpoints->deliveries->list('0199f210-4e5f-7a6b-8c7d-8e9f0a1b2c34', ['status' => 'delivered', 'limit' => 20]);
     *
     * @param string $id `id` in the path
     * @param array{status?: string, event?: string, created_after?: string|\DateTimeInterface, created_before?: string|\DateTimeInterface, limit?: int, cursor?: string}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint webhook-deliveries
     */
    public function list(string $id, array $params = [], array $options = []): WebhookDeliveryListResponse
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/webhooks/'.self::segment($id, 'id').'/deliveries',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
            query: $params,
        );

        return WebhookDeliveryListResponse::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Every item of `list()`, page after page (following `next_cursor`): the same filters, and `limit` for the page size.
     *
     * @param array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @return \Generator<int, \Tawked\Model\Webhook\WebhookEndpointDelivery>
     */
    public function iterate(string $id, array $params = [], array $options = []): \Generator
    {
        return Paginator::cursor(fn(array $page): WebhookDeliveryListResponse => $this->list($id, $page, $options), $params, 'cursor');
    }

    /**
     * Get a delivery. `GET /v1/webhooks/{id}/deliveries/{delivery}`.
     *
     * Reads one delivery with the body sent and every attempt. A live key of the application with
     * the explicit `webhooks:manage`: a full-access key does not hold it.
     *
     * Scope `webhooks:manage`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * Errors: application_key_required, insufficient_scope, internal_error, ip_not_allowed,
     * key_expired, live_key_required, not_found, too_many_requests, unauthorized
     * (Tawked\ErrorCode).
     *
     * @example $tawked->webhookEndpoints->deliveries->get('0199f210-4e5f-7a6b-8c7d-8e9f0a1b2c34', '0199f220-5f6a-7b7c-8d8e-9f0a1b2c3d45');
     *
     * @param string $id `id` in the path
     * @param string $delivery `delivery` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint webhook-delivery
     */
    public function get(string $id, string $delivery, array $options = []): WebhookEndpointDelivery
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/webhooks/'.self::segment($id, 'id').'/deliveries/'.self::segment($delivery, 'delivery'),
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        );

        return WebhookEndpointDelivery::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Send a delivery again. `POST /v1/webhooks/{id}/deliveries/{delivery}/replay`.
     *
     * Sends a final delivery (delivered or failed) again as a new delivery with the same body and
     * the same `webhook-id`, so your receiver can tell it is the same event. `409 delivery_pending`
     * while it is still being retried, `409 endpoint_off` when the endpoint is off, `409
     * delivery_erased` when the received email it carried was deleted. A live key of the
     * application with the explicit `webhooks:manage`: a full-access key does not hold it.
     *
     * Scope `webhooks:manage`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * Errors: application_key_required, delivery_erased, delivery_pending, endpoint_off,
     * insufficient_scope, internal_error, invalid_json, invalid_request, ip_not_allowed,
     * key_expired, live_key_required, not_found, too_many_requests, unauthorized
     * (Tawked\ErrorCode).
     *
     * @example $tawked->webhookEndpoints->deliveries->replay('0199f210-4e5f-7a6b-8c7d-8e9f0a1b2c34', '0199f220-5f6a-7b7c-8d8e-9f0a1b2c3d45');
     *
     * @param string $id `id` in the path
     * @param string $delivery `delivery` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint webhook-delivery-replay
     */
    public function replay(string $id, string $delivery, array $options = []): WebhookEndpointDelivery
    {
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/webhooks/'.self::segment($id, 'id').'/deliveries/'.self::segment($delivery, 'delivery').'/replay',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            takesKey: false,
        );

        return WebhookEndpointDelivery::fromArray($response->body)->withMeta($response->meta);
    }
}
