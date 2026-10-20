<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Stores\Zid;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->stores->zid->setup`: the Account API's `stores.zid.setup` operations.
 */
final class SetupApi extends AccountNamespace
{
    /**
     * Link a Zid store waiting for its application to one of the account's applications
     * (`application_id`), or to a new one named after the store (null); answers the application's
     * id. `PUT /v1/stores/zid/{store}/setup/link`.
     *
     * Permission `stores:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Sent once: retried only
     * when the request never left (or the key is still in progress). After a timeout the exception
     * carries the `Idempotency-Key`: call again with it, never a new one. After a 5xx Tawked
     * released the key: check whether it took effect before calling again.
     *
     * @param string $store `store` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation stores.zid.setup.link
     */
    public function link(string $store, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'PUT',
            path: '/v1/stores/zid/'.self::segment($store, 'store').'/setup/link',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * Leave the WhatsApp number for later in a Zid store's setup (`later`, true when absent), so
     * its notifications list opens before the number; answers ok. `PUT
     * /v1/stores/zid/{store}/setup/number-later`.
     *
     * Permission `stores:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $store `store` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation stores.zid.setup.number_later
     */
    public function numberLater(string $store, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'PUT',
            path: '/v1/stores/zid/'.self::segment($store, 'store').'/setup/number-later',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }
}
