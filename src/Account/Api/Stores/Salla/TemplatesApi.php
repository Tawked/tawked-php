<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Stores\Salla;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->stores->salla->templates`: the Account API's `stores.salla.templates` operations.
 */
final class TemplatesApi extends AccountNamespace
{
    /**
     * Read the store number's templates from Meta again (after Meta approved or rejected one);
     * answers ok with a `next` (read the store again), or templates_unavailable when Meta did not
     * answer. `POST /v1/stores/salla/{store}/templates/refresh`.
     *
     * Permission `stores:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $store `store` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation stores.salla.templates.refresh
     */
    public function refresh(string $store, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/stores/salla/'.self::segment($store, 'store').'/templates/refresh',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }
}
