<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Clients;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->clients->verifySettings`: the Account API's `clients.verify_settings` operations.
 */
final class VerifySettingsApi extends AccountNamespace
{
    /**
     * Change some of a client application's Verify settings (a partial patch: only the fields sent
     * change); answers the application with its settings. `PATCH
     * /v1/clients/applications/{application}/verify-settings`.
     *
     * Permission `clients:write`. Sent once: retried only when the request never left (or the key
     * is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * @param string $application `application` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation clients.verify_settings.update
     */
    public function update(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'PATCH',
            path: '/v1/clients/applications/'.self::segment($application, 'application').'/verify-settings',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }
}
