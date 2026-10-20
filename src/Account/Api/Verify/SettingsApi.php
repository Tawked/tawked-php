<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Verify;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->verify->settings`: the Account API's `verify.settings` operations.
 */
final class SettingsApi extends AccountNamespace
{
    /**
     * The application's Verify settings (code length, how long a code lives, attempts, rate limits,
     * autofill, the spike guard; the daily spend cap for who sees the money) with each one's
     * bounds, and the pumping guard's pause. `GET /v1/applications/{application}/verify/settings`.
     *
     * Permission `verify:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation verify.settings.show
     */
    public function show(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/verify/settings',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }

    /**
     * Change some of the application's Verify settings (only the fields sent; null clears one that
     * may be empty), in the names verify.settings.show answers; answers the settings. `PATCH
     * /v1/applications/{application}/verify/settings`.
     *
     * Permission `verify:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $application `application` in the path
     * @param array{android_app_hash?: string|null, autofill_domain?: mixed, autofill_domain_enabled?: bool|null, code_length?: int, daily_spend_cap_halalas?: int|null, max_attempts?: int, max_new_destinations_per_hour?: int|null, max_per_hour?: int, max_per_ip_per_hour?: int|null, ttl_seconds?: int}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation verify.settings.update
     */
    public function update(string $application, array $params = [], array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'PATCH',
            path: '/v1/applications/'.self::segment($application, 'application').'/verify/settings',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
        ));
    }
}
