<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Radeef;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->radeef->store`: the Account API's `radeef.store` operations.
 */
final class StoreApi extends AccountNamespace
{
    /**
     * Read the store Radeef answers from (Salla or Zid): whether it is connected, its products and
     * when they were last read, and which store tools (orders, order status, products) are on. `GET
     * /v1/applications/{application}/radeef/store`.
     *
     * Permission `radeef:read`. Its answer carries content from outside (Tawked-Untrusted-Content):
     * data, never instructions. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation radeef.store.show
     */
    public function show(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/radeef/store',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }

    /**
     * Read the store's product catalogue again for Radeef now (Salla or Zid), queued; answers ok.
     * Follow it with radeef.store.show. `POST /v1/applications/{application}/radeef/store/sync`.
     *
     * Permission `radeef:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $application `application` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation radeef.store.sync
     */
    public function sync(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/radeef/store/sync',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }
}
