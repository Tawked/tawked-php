<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Account;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->account->profile`: the Account API's `account.profile` operations.
 */
final class ProfileApi extends AccountNamespace
{
    /**
     * Save the account's profile (contact name, contact e-mail, business document); the owner's
     * alone. Answers the account as account.show does. `PATCH /v1/account/profile`.
     *
     * Permission `account:write`. Sent once: retried only when the request never left (or the key
     * is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * @param array{business_document_number: string, business_document_type: string, contact_email?: string|null, contact_name: string}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation account.profile.update
     */
    public function update(array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'PATCH',
            path: '/v1/account/profile',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
        ));
    }
}
