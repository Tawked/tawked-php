<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Clients;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->clients->webhook`: the Account API's `clients.webhook` operations.
 */
final class WebhookApi extends AccountNamespace
{
    /**
     * Read where the webhook stands (none, idle, delivering, failing), since when it fails, what
     * waits, the last answer and the next automatic attempt, with the deliveries counted by status.
     * `GET /v1/clients/api/webhook`.
     *
     * Permission `clients:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation clients.webhook.health
     */
    public function health(array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/clients/api/webhook',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }

    /**
     * Answer the webhook's current signing secret (`secret`), to verify the deliveries' signatures;
     * each reveal is recorded on the account. `POST /v1/clients/api/webhook/reveal-secret`.
     *
     * Permission `clients:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Never retried: its
     * answer holds a secret shown once (120 s timeout). After a failure, find what it made by its
     * id, then rotate or revoke it.
     *
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation clients.webhook.reveal_secret
     */
    public function revealSecret(array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/clients/api/webhook/reveal-secret',
            options: $options,
            retry: Transport::NEVER,
            timeout: 120.0,
            secret: true,
        ));
    }

    /**
     * Make a new signing secret for the webhook; the old one stops signing at once. Answers the
     * secret this once. `POST /v1/clients/api/webhook/rotate-secret`.
     *
     * Permission `clients:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Never retried: its
     * answer holds a secret shown once (120 s timeout). After a failure, find what it made by its
     * id, then rotate or revoke it.
     *
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation clients.webhook.rotate_secret
     */
    public function rotateSecret(array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/clients/api/webhook/rotate-secret',
            options: $options,
            retry: Transport::NEVER,
            timeout: 120.0,
            secret: true,
        ));
    }

    /**
     * Set the Partner API's webhook: its URL (empty turns it off), the events it receives and the
     * balance.low threshold; answers the webhook as it stands. `PATCH /v1/clients/api/webhook`.
     *
     * Permission `clients:write`. Sent once: retried only when the request never left (or the key
     * is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * @param array{events?: list<mixed>|null, threshold_sar?: float|null, url?: string|null}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation clients.webhook.update
     */
    public function update(array $params = [], array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'PATCH',
            path: '/v1/clients/api/webhook',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
        ));
    }
}
