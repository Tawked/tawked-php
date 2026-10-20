<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Wallet;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->wallet->proformas`: the Account API's `wallet.proformas` operations.
 */
final class ProformasApi extends AccountNamespace
{
    /**
     * Cancel an open proforma (the transfer will not be expected); answers the proforma, now
     * cancelled. `DELETE /v1/account/wallet/proformas/{document}`.
     *
     * Permission `wallet:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Sent once: retried only
     * when the request never left (or the key is still in progress). After a timeout the exception
     * carries the `Idempotency-Key`: call again with it, never a new one. After a 5xx Tawked
     * released the key: check whether it took effect before calling again.
     *
     * @param string $document `document` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation wallet.proformas.cancel
     */
    public function cancel(string $document, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'DELETE',
            path: '/v1/account/wallet/proformas/'.self::segment($document, 'document'),
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * Ask for a proforma to top up by bank transfer (`amount_halalas`, the credit wanted); answers
     * the proforma with its number, total and PDF. The balance moves only once Tawked's team
     * settles the transfer. `POST /v1/account/wallet/proformas`.
     *
     * Permission `wallet:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param array{amount_halalas: int, billing?: array<string, mixed>}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation wallet.proformas.create
     */
    public function create(array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/account/wallet/proformas',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, ['billing']),
        ));
    }
}
