<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Clients;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->clients->sandboxKey`: the Account API's `clients.sandbox_key` operations.
 */
final class SandboxKeyApi extends AccountNamespace
{
    /**
     * Make a new sandbox key (tk_partner_test_…) for trying the Partner API without spending; the
     * sandbox key before it stops at once. Answers its plaintext this once. `POST
     * /v1/clients/api/sandbox-key`.
     *
     * Permission `clients:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Never retried: its
     * answer holds a secret shown once (120 s timeout). After a failure, find what it made by its
     * id, then rotate or revoke it.
     *
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation clients.sandbox_key.create
     */
    public function create(array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/clients/api/sandbox-key',
            options: $options,
            retry: Transport::NEVER,
            timeout: 120.0,
            secret: true,
        ));
    }
}
