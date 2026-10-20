<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->me`: the Account API's `me` operations.
 */
final class MeApi extends AccountNamespace
{
    /**
     * Who this token acts for: the person, the account, the token's permissions, the applications
     * it reaches with their products, and `next_steps`: what is left to set up. Call it first. `GET
     * /v1/me`.
     *
     * Permission ``. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation me.show
     */
    public function show(array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/me',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }
}
