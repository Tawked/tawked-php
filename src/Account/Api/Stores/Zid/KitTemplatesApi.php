<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Stores\Zid;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->stores->zid->kitTemplates`: the Account API's `stores.zid.kit_templates` operations.
 */
final class KitTemplatesApi extends AccountNamespace
{
    /**
     * Submit to Meta the kit templates the store's enabled notifications still need; answers how
     * many were created and which failed. Meta reviews each one: `next` says how to follow it
     * (stores.zid.templates.refresh, then stores.zid.show). `POST
     * /v1/stores/zid/{store}/templates/kit`.
     *
     * Permission `stores:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $store `store` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation stores.zid.kit_templates.create
     */
    public function create(string $store, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/stores/zid/'.self::segment($store, 'store').'/templates/kit',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }
}
