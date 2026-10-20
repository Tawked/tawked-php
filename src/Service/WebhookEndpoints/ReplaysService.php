<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Service\WebhookEndpoints;

use Tawked\Http\Transport;
use Tawked\Model\Webhook\WebhookReplay;
use Tawked\Service\Service;

/**
 * `$tawked->webhookEndpoints->replays`.
 */
final class ReplaysService extends Service
{
    /**
     * Get a replay. `GET /v1/webhooks/{id}/replays/{replay}`.
     *
     * Reads a replay's progress: `queued`, `running` or `done`, how many deliveries matched and how
     * many were queued. A live key of the application with the explicit `webhooks:manage`: a
     * full-access key does not hold it.
     *
     * Scope `webhooks:manage`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * Errors: application_key_required, insufficient_scope, internal_error, ip_not_allowed,
     * key_expired, live_key_required, not_found, too_many_requests, unauthorized
     * (Tawked\ErrorCode).
     *
     * @example $tawked->webhookEndpoints->replays->get('0199f210-4e5f-7a6b-8c7d-8e9f0a1b2c34', '0199f230-6a7b-7c8d-9e0f-0a1b2c3d4e56');
     *
     * @param string $id `id` in the path
     * @param string $replay `replay` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint webhook-replay-status
     */
    public function get(string $id, string $replay, array $options = []): WebhookReplay
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/webhooks/'.self::segment($id, 'id').'/replays/'.self::segment($replay, 'replay'),
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        );

        return WebhookReplay::fromArray($response->body)->withMeta($response->meta);
    }
}
