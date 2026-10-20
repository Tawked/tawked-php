<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Wallet;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->wallet->autoTopup`: the Account API's `wallet.auto_topup` operations.
 */
final class AutoTopupApi extends AccountNamespace
{
    /**
     * The auto top-up as set (on or off, the threshold and the amount in halalas, the card,
     * failures and why it turned itself off), or null while Tawked does not offer it or it was
     * never set. `GET /v1/account/wallet/auto-top-up`.
     *
     * Permission `wallet:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation wallet.auto_topup.show
     */
    public function show(array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/account/wallet/auto-top-up',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }

    /**
     * Turn the auto top-up on or off and set its threshold, amount and saved card (`enabled`,
     * `threshold_halalas`, `amount_halalas`, `payment_method_id`); answers the setting. `PUT
     * /v1/account/wallet/auto-top-up`.
     *
     * Permission `wallet:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Sent once: retried only
     * when the request never left (or the key is still in progress). After a timeout the exception
     * carries the `Idempotency-Key`: call again with it, never a new one. After a 5xx Tawked
     * released the key: check whether it took effect before calling again.
     *
     * @param array{amount_halalas: int, enabled: bool, payment_method_id?: string|null, threshold_halalas: int}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation wallet.auto_topup.update
     */
    public function update(array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'PUT',
            path: '/v1/account/wallet/auto-top-up',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
        ));
    }
}
