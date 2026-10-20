<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Stores\Salla;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->stores->salla->kinds`: the Account API's `stores.salla.kinds` operations.
 */
final class KindsApi extends AccountNamespace
{
    /**
     * Change one notification of a Salla store at once: switch it on or off, choose its template
     * (the kit's or the number's own), map the template's placeholders, set its options. Answers
     * the slot as it was before. `PUT /v1/stores/salla/{store}/kinds/{kind}`.
     *
     * Permission `stores:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Sent once: retried only
     * when the request never left (or the key is still in progress). After a timeout the exception
     * carries the `Idempotency-Key`: call again with it, never a new one. After a 5xx Tawked
     * released the key: check whether it took effect before calling again.
     *
     * @param string $store `store` in the path
     * @param string $kind `kind` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation stores.salla.kinds.update
     */
    public function update(string $store, string $kind, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'PUT',
            path: '/v1/stores/salla/'.self::segment($store, 'store').'/kinds/'.self::segment($kind, 'kind'),
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }
}
