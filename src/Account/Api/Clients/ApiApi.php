<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Clients;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->clients->api`: the Account API's `clients.api` operations.
 */
final class ApiApi extends AccountNamespace
{
    /**
     * Read the Partner API's state: whether a partner key works (its prefix, expiry and allowlist;
     * never the key), the sandbox key's prefix, the webhook (URL, events, threshold), its health
     * and the newest deliveries. `GET /v1/clients/api`.
     *
     * Permission `clients:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation clients.api.show
     */
    public function show(array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/clients/api',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }
}
