<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Wallet;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->wallet->topups`: the Account API's `wallet.topups` operations.
 */
final class TopupsApi extends AccountNamespace
{
    /**
     * The console's Top up address for the person to pay by card (optionally with `amount_halalas`
     * filled in), and the amounts it offers: the packages, the range, the bounds. Relay
     * `human_action` (what to say, the link in the person's language); read `wallet.balance.show`
     * after they paid. `GET /v1/account/wallet/top-up-link`.
     *
     * Permission `wallet:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param array{amount_halalas?: int|null}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation wallet.topups.link
     */
    public function link(array $params = [], array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/account/wallet/top-up-link',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
            query: $params,
        ));
    }
}
