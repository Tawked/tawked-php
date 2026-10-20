<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Clients;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->clients->apiKey`: the Account API's `clients.api_key` operations.
 */
final class ApiKeyApi extends AccountNamespace
{
    /**
     * Make a new partner key (tk_partner_…) for the Partner API; the key that worked until now
     * stops at once. Answers the key's plaintext this once, with its prefix and the prefix of the
     * key it replaced. `POST /v1/clients/api/key`.
     *
     * Permission `clients:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Never retried: its
     * answer holds a secret shown once (120 s timeout). After a failure, find what it made by its
     * id, then rotate or revoke it.
     *
     * @param array{expires_in_days?: int|null, ip_allowlist?: string|null}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation clients.api_key.create
     */
    public function create(array $params = [], array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/clients/api/key',
            options: $options,
            retry: Transport::NEVER,
            timeout: 120.0,
            json: self::body($params, []),
            secret: true,
        ));
    }
}
