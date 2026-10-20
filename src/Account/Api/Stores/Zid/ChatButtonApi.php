<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Stores\Zid;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->stores->zid->chatButton`: the Account API's `stores.zid.chat_button` operations.
 */
final class ChatButtonApi extends AccountNamespace
{
    /**
     * Save the chat button on the Zid store's own pages, whole (on or off, position, colours,
     * greeting); answers the button as saved. Turning it on needs a number that takes messages.
     * `PUT /v1/stores/zid/{store}/chat-button`.
     *
     * Permission `stores:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $store `store` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation stores.zid.chat_button.update
     */
    public function update(string $store, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'PUT',
            path: '/v1/stores/zid/'.self::segment($store, 'store').'/chat-button',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }
}
