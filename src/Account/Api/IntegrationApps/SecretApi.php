<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\IntegrationApps;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->integrationApps->secret`: the Account API's `integration_apps.secret` operations.
 */
final class SecretApi extends AccountNamespace
{
    /**
     * Renew an integration app's client secret: the old one stops at once, so the app's sign-ins
     * fail until it uses the new one. Answers the new secret this once. `POST
     * /v1/integration-apps/{app}/secret`.
     *
     * Permission `integration_apps:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Never retried: its
     * answer holds a secret shown once (120 s timeout). After a failure, find what it made by its
     * id, then rotate or revoke it.
     *
     * @param string $app `app` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation integration_apps.secret.renew
     */
    public function renew(string $app, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/integration-apps/'.self::segment($app, 'app').'/secret',
            options: $options,
            retry: Transport::NEVER,
            timeout: 120.0,
            secret: true,
        ));
    }
}
