<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Wallet;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->wallet->billing`: the Account API's `wallet.billing` operations.
 */
final class BillingApi extends AccountNamespace
{
    /**
     * The account's billing details (legal name, VAT number, the national address, the billing
     * e-mail), whether they are complete, and whether a top-up must ask for them first. `GET
     * /v1/account/wallet/billing`.
     *
     * Permission `wallet:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation wallet.billing.show
     */
    public function show(array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/account/wallet/billing',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }

    /**
     * Save the account's billing details, the ones every invoice is addressed to (legal name, city,
     * e-mail, and while Tawked is VAT-registered the national address); answers the billing
     * details. `PUT /v1/account/wallet/billing`.
     *
     * Permission `wallet:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param array{building_number?: string|null, city: string, district?: string|null, email: string, legal_name: string, other_id?: string|null, other_id_type?: string, postal_code?: string|null, street?: string|null, vat_number?: string|null}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation wallet.billing.update
     */
    public function update(array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'PUT',
            path: '/v1/account/wallet/billing',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
        ));
    }
}
