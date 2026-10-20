<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Service\Partner;

use Tawked\Http\Transport;
use Tawked\Model\Partner\PartnerWebhook;
use Tawked\Service\Partner\Webhook\DeliveriesService;
use Tawked\Service\Service;

/**
 * `$tawked->partner->webhook`: the Partner API (partner keys only).
 */
final class WebhookService extends Service
{
    public readonly DeliveriesService $deliveries;

    public function __construct(Transport $transport)
    {
        parent::__construct($transport);
        $this->deliveries = new DeliveriesService($transport);
    }

    /**
     * Get the webhook. `GET /v1/partner/webhook`.
     *
     * The webhook as it stands: the URL and whether a secret exists (`configured`), the low-balance
     * threshold, the events that reach the URL (`events`), the default set and every event you may
     * subscribe to. The secret is never returned.
     *
     * Retried after a network failure, a timeout or a 5xx (a read).
     *
     * Errors: internal_error, ip_not_allowed, key_expired, too_many_requests, unauthorized
     * (Tawked\ErrorCode).
     *
     * @example $tawked->partner->webhook->get();
     *
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint partner-webhook
     */
    public function get(array $options = []): PartnerWebhook
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/partner/webhook',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        );

        return PartnerWebhook::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Update the webhook. `PUT /v1/partner/webhook`.
     *
     * Chooses which events reach the URL and sets the low-balance threshold; send one or both. The
     * URL and the signing secret are set in the console, behind sign-in, and a body naming them is
     * refused. The answer is the webhook as it stands.
     *
     * Retried after a network failure, a timeout or a 5xx under the same `Idempotency-Key` (minted
     * when you pass none): the server keeps the key through a failure.
     *
     * Errors: idempotency_in_progress, idempotency_key_reused, internal_error, invalid_json,
     * invalid_request, ip_not_allowed, key_expired, live_key_required, partner_suspended,
     * too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->partner->webhook->update(['events' => [...], 'low_balance_threshold_halalas' => 10000]);
     *
     * @param array{events?: list<mixed>|null, low_balance_threshold_halalas?: int}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint partner-webhook-update
     */
    public function update(array $params = [], array $options = []): PartnerWebhook
    {
        $response = $this->transport->send(
            method: 'PUT',
            path: '/v1/partner/webhook',
            options: $options,
            retry: Transport::IDEMPOTENT,
            timeout: 30.0,
            json: self::body($params, []),
        );

        return PartnerWebhook::fromArray($response->body)->withMeta($response->meta);
    }
}
