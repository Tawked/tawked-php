<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Wallet;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->wallet->balance`: the Account API's `wallet.balance` operations.
 */
final class BalanceApi extends AccountNamespace
{
    /**
     * The account's prepaid balance as Wallet › Balance shows it, in halalas: the balance and its
     * state (`ok`, `low`, `out`), about how many codes it buys, this month's spend and its
     * projection, how long it lasts, the low-balance alert, the auto top-up and the card on file
     * (masked), the month by product, and the last top-up. `GET /v1/account/wallet`.
     *
     * Permission `wallet:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation wallet.balance.show
     */
    public function show(array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/account/wallet',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }
}
