<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\IntegrationApps;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->integrationApps->pendingChange`: the Account API's `integration_apps.pending_change` operations.
 */
final class PendingChangeApi extends AccountNamespace
{
    /**
     * Drop the change of an approved integration app that waits for Tawked's team; the approved
     * facts stay. Answers the app. `DELETE /v1/integration-apps/{app}/pending`.
     *
     * Permission `integration_apps:write`. Sent once: retried only when the request never left (or
     * the key is still in progress). After a timeout the exception carries the `Idempotency-Key`:
     * call again with it, never a new one. After a 5xx Tawked released the key: check whether it
     * took effect before calling again.
     *
     * @param string $app `app` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation integration_apps.pending_change.cancel
     */
    public function cancel(string $app, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'DELETE',
            path: '/v1/integration-apps/'.self::segment($app, 'app').'/pending',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }
}
