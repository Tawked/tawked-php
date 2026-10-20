<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Service;

use Tawked\Http\Transport;
use Tawked\Model\Webhook\WebhookEndpoint;
use Tawked\Model\Webhook\WebhookEndpointDelivery;
use Tawked\Model\Webhook\WebhookReplay;
use Tawked\Model\WebhookEndpoints\WebhookEndpointDeleteResponse;
use Tawked\Model\WebhookEndpoints\WebhookEndpointListResponse;
use Tawked\Model\WebhookEndpoints\WebhookSecretRotateResponse;
use Tawked\Service\WebhookEndpoints\DeliveriesService;
use Tawked\Service\WebhookEndpoints\ReplaysService;

/**
 * `$tawked->webhookEndpoints`.
 */
final class WebhookEndpointsService extends Service
{
    public readonly DeliveriesService $deliveries;

    public readonly ReplaysService $replays;

    public function __construct(Transport $transport)
    {
        parent::__construct($transport);
        $this->deliveries = new DeliveriesService($transport);
        $this->replays = new ReplaysService($transport);
    }

    /**
     * List webhook endpoints. `GET /v1/webhooks`.
     *
     * Lists the application's endpoints (at most five, no pages). A live key of the application
     * with the explicit `webhooks:manage`: a full-access key does not hold it.
     *
     * Scope `webhooks:manage`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * Errors: application_key_required, insufficient_scope, internal_error, ip_not_allowed,
     * key_expired, live_key_required, too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->webhookEndpoints->list();
     *
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint webhooks
     */
    public function list(array $options = []): WebhookEndpointListResponse
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/webhooks',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        );

        return WebhookEndpointListResponse::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Create a webhook endpoint. `POST /v1/webhooks`.
     *
     * Adds an endpoint, signed with both schemes, and answers its two secrets once: the Tawked
     * secret and the Standard Webhooks `whsec_` secret. Without `events` it receives the
     * application's products' events except the ones each owner adds by name: `email.sent`,
     * `email.delivery_delayed`, `email.suppressed`, `email.scheduled`, `email.canceled`,
     * `email.opened`, `email.clicked`, `domain.*` and `suppression.*`. A live key of the
     * application with the explicit `webhooks:manage`: a full-access key does not hold it.
     *
     * Scope `webhooks:manage`. Never retried once it may have reached the server: each try spends
     * something.
     *
     * Errors: application_key_required, credentials, endpoint_limit_reached,
     * idempotency_secret_not_replayable, insufficient_scope, internal_error, invalid_host,
     * invalid_json, invalid_request, ip_address, ip_not_allowed, key_expired, live_key_required,
     * not_https, private_host, too_many_requests, unauthorized, webhook_event_unknown
     * (Tawked\ErrorCode).
     *
     * @example $tawked->webhookEndpoints->create(['url' => 'https://hooks.acme.sa/tawked', 'events' => [...]]);
     *
     * @param array{url: string, events?: list<mixed>, receives_test?: bool}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint webhook-create
     */
    public function create(array $params, array $options = []): WebhookEndpoint
    {
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/webhooks',
            options: $options,
            retry: Transport::NEVER,
            timeout: 30.0,
            json: self::body($params, []),
        );

        return WebhookEndpoint::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Get a webhook endpoint. `GET /v1/webhooks/{id}`.
     *
     * Reads one endpoint: its events, whether it receives, its last delivery and how it is signed.
     * A live key of the application with the explicit `webhooks:manage`: a full-access key does not
     * hold it.
     *
     * Scope `webhooks:manage`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * Errors: application_key_required, insufficient_scope, internal_error, ip_not_allowed,
     * key_expired, live_key_required, not_found, too_many_requests, unauthorized
     * (Tawked\ErrorCode).
     *
     * @example $tawked->webhookEndpoints->get('0199f210-4e5f-7a6b-8c7d-8e9f0a1b2c34');
     *
     * @param string $id `id` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint webhook
     */
    public function get(string $id, array $options = []): WebhookEndpoint
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/webhooks/'.self::segment($id, 'id'),
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        );

        return WebhookEndpoint::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Update a webhook endpoint. `PATCH /v1/webhooks/{id}`.
     *
     * Changes an endpoint's address, its events, whether it is on, whether it receives test events,
     * or its Standard Webhooks signing. Turning Standard signing on for the first time answers its
     * `whsec_` secret once; turning an endpoint switched off for failing back on clears its
     * failures. Every field is optional. A live key of the application with the explicit
     * `webhooks:manage`: a full-access key does not hold it.
     *
     * Scope `webhooks:manage`. Never retried once it may have reached the server: each try spends
     * something.
     *
     * Errors: application_key_required, credentials, insufficient_scope, internal_error,
     * invalid_host, invalid_json, invalid_request, ip_address, ip_not_allowed, key_expired,
     * live_key_required, not_found, not_https, private_host, too_many_requests, unauthorized,
     * webhook_event_unknown (Tawked\ErrorCode).
     *
     * @example $tawked->webhookEndpoints->update('0199f210-4e5f-7a6b-8c7d-8e9f0a1b2c34', ['receives_test' => true]);
     *
     * @param string $id `id` in the path
     * @param array{url?: string, events?: list<mixed>, enabled?: bool, receives_test?: bool, signing?: array<string, mixed>}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint webhook-update
     */
    public function update(string $id, array $params = [], array $options = []): WebhookEndpoint
    {
        $response = $this->transport->send(
            method: 'PATCH',
            path: '/v1/webhooks/'.self::segment($id, 'id'),
            options: $options,
            retry: Transport::NEVER,
            timeout: 30.0,
            json: self::body($params, ['signing']),
        );

        return WebhookEndpoint::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Delete a webhook endpoint. `DELETE /v1/webhooks/{id}`.
     *
     * Deletes an endpoint; its pending deliveries stop. A live key of the application with the
     * explicit `webhooks:manage`: a full-access key does not hold it.
     *
     * Scope `webhooks:manage`. Retried after a network failure, a timeout or a 5xx under the same
     * `Idempotency-Key` (minted when you pass none): the server keeps the key through a failure.
     *
     * Errors: application_key_required, insufficient_scope, internal_error, invalid_json,
     * invalid_request, ip_not_allowed, key_expired, live_key_required, not_found,
     * too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->webhookEndpoints->delete('0199f210-4e5f-7a6b-8c7d-8e9f0a1b2c34');
     *
     * @param string $id `id` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint webhook-delete
     */
    public function delete(string $id, array $options = []): WebhookEndpointDeleteResponse
    {
        $response = $this->transport->send(
            method: 'DELETE',
            path: '/v1/webhooks/'.self::segment($id, 'id'),
            options: $options,
            retry: Transport::IDEMPOTENT,
            timeout: 30.0,
        );

        return WebhookEndpointDeleteResponse::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Rotate a webhook secret. `POST /v1/webhooks/{id}/rotate-secret`.
     *
     * Makes a new signing secret and answers it once. `standard` (the default) rotates the Standard
     * Webhooks secret: the previous one keeps signing beside it until `previous_valid_until`, and
     * an endpoint without Standard signing gets it now. `tawked` or `both` rotate the Tawked secret
     * too, which switches at once: your server rejects every delivery until it has the new one. A
     * live key of the application with the explicit `webhooks:manage`: a full-access key does not
     * hold it.
     *
     * Scope `webhooks:manage`. Never retried once it may have reached the server: each try spends
     * something.
     *
     * Errors: application_key_required, idempotency_secret_not_replayable, insufficient_scope,
     * internal_error, invalid_json, invalid_request, ip_not_allowed, key_expired,
     * live_key_required, not_found, too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->webhookEndpoints->rotateSecret('0199f210-4e5f-7a6b-8c7d-8e9f0a1b2c34', ['scheme' => 'standard']);
     *
     * @param string $id `id` in the path
     * @param array{scheme?: string}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint webhook-rotate-secret
     */
    public function rotateSecret(string $id, array $params = [], array $options = []): WebhookSecretRotateResponse
    {
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/webhooks/'.self::segment($id, 'id').'/rotate-secret',
            options: $options,
            retry: Transport::NEVER,
            timeout: 30.0,
            json: self::body($params, []),
        );

        return WebhookSecretRotateResponse::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Send a test event. `POST /v1/webhooks/{id}/test`.
     *
     * Queues one sample of an event the endpoint receives, with `"test": true` in the envelope and
     * made-up values. `202` with the delivery. A live key of the application with the explicit
     * `webhooks:manage`: a full-access key does not hold it.
     *
     * Scope `webhooks:manage`. Never retried once it may have reached the server: each try spends
     * something.
     *
     * Errors: application_key_required, endpoint_off, insufficient_scope, internal_error,
     * invalid_json, invalid_request, ip_not_allowed, key_expired, live_key_required, not_found,
     * too_many_requests, unauthorized, webhook_event_unknown (Tawked\ErrorCode).
     *
     * @example $tawked->webhookEndpoints->test('0199f210-4e5f-7a6b-8c7d-8e9f0a1b2c34', ['event' => 'email.delivered']);
     *
     * @param string $id `id` in the path
     * @param array{event?: string}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint webhook-test
     */
    public function test(string $id, array $params = [], array $options = []): WebhookEndpointDelivery
    {
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/webhooks/'.self::segment($id, 'id').'/test',
            options: $options,
            retry: Transport::NEVER,
            timeout: 30.0,
            json: self::body($params, []),
        );

        return WebhookEndpointDelivery::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Replay deliveries. `POST /v1/webhooks/{id}/replay`.
     *
     * Sends again, in date order, up to 1000 final deliveries of the endpoint created in a window
     * of at most 7 days within the last 30 (Tawked's limits): the failed ones by default, or all.
     * One replay runs at a time for an endpoint (`409 replay_running`); six an hour. `202` with the
     * replay; read its progress with `GET /v1/webhooks/{id}/replays/{replay}`. A live key of the
     * application with the explicit `webhooks:manage`: a full-access key does not hold it.
     *
     * Scope `webhooks:manage`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * Errors: application_key_required, endpoint_off, insufficient_scope, internal_error,
     * invalid_json, invalid_request, ip_not_allowed, key_expired, live_key_required, not_found,
     * replay_running, too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->webhookEndpoints->replay('0199f210-4e5f-7a6b-8c7d-8e9f0a1b2c34', ['since' => '2026-10-22T00:00:00+00:00', 'until' => '2026-10-23T00:00:00+00:00', 'status' => 'failed']);
     *
     * @param string $id `id` in the path
     * @param array{since: string|\DateTimeInterface, until?: string|\DateTimeInterface, status?: string, events?: list<mixed>}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint webhook-replay
     */
    public function replay(string $id, array $params, array $options = []): WebhookReplay
    {
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/webhooks/'.self::segment($id, 'id').'/replay',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
            takesKey: false,
        );

        return WebhookReplay::fromArray($response->body)->withMeta($response->meta);
    }
}
