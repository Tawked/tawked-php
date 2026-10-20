<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Wallet;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->wallet->statements`: the Account API's `wallet.statements` operations.
 */
final class StatementsApi extends AccountNamespace
{
    /**
     * One month's statement (YYYY-MM, Riyadh): the opening and closing balance, the top-ups, the
     * usage by product, the refunds and adjustments, the month day by day, and the address of its
     * PDF. `GET /v1/account/wallet/statements/{period}`.
     *
     * Permission `wallet:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $period `period` in the path
     * @param array{application?: string, day?: string, kind?: string, month?: string, page?: int, product?: string, q?: string, reference?: string, side?: string}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation wallet.statements.show
     */
    public function show(string $period, array $params = [], array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/account/wallet/statements/'.self::segment($period, 'period'),
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
            query: $params,
        ));
    }
}
