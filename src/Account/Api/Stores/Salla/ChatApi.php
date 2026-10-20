<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Stores\Salla;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->stores->salla->chat`: the Account API's `stores.salla.chat` operations.
 */
final class ChatApi extends AccountNamespace
{
    /**
     * Change what the Tawked Chat team sees and does with the Salla store's orders (the orders tab:
     * customer, orders, status changes); answers the settings as saved. `PUT
     * /v1/stores/salla/{store}/chat`.
     *
     * Permission `stores:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $store `store` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation stores.salla.chat.update
     */
    public function update(string $store, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'PUT',
            path: '/v1/stores/salla/'.self::segment($store, 'store').'/chat',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }
}
