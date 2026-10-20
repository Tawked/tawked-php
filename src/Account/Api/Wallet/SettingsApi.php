<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Wallet;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->wallet->settings`: the Account API's `wallet.settings` operations.
 */
final class SettingsApi extends AccountNamespace
{
    /**
     * The wallet's settings: the low-balance alert (threshold in halalas, the e-mail, the WhatsApp
     * number it goes to, masked) and, while Tawked offers it, the auto top-up (its setting, today's
     * charges against the daily cap, the saved cards masked, the bounds). `GET
     * /v1/account/wallet/settings`.
     *
     * Permission `wallet:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation wallet.settings.show
     */
    public function show(array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/account/wallet/settings',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }

    /**
     * Save the wallet's settings in one go, as Balance's save bar does: the low-balance alert
     * (`alert_enabled`, `alert_threshold_halalas`, `alert_email`) and optionally the auto top-up
     * (`auto_topup`: enabled, threshold, amount, card); answers the settings. `PUT
     * /v1/account/wallet/settings`.
     *
     * Permission `wallet:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Sent once: retried only
     * when the request never left (or the key is still in progress). After a timeout the exception
     * carries the `Idempotency-Key`: call again with it, never a new one. After a 5xx Tawked
     * released the key: check whether it took effect before calling again.
     *
     * @param array{alert_email?: string|null, alert_enabled: bool, alert_threshold_halalas?: int, auto_topup?: array<string, mixed>}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation wallet.settings.update
     */
    public function update(array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'PUT',
            path: '/v1/account/wallet/settings',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, ['auto_topup']),
        ));
    }
}
