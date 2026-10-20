<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Stores\Zid;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->stores->zid->test`: the Account API's `stores.zid.test` operations.
 */
final class TestApi extends AccountNamespace
{
    /**
     * Send a test store notification to the account owner's own phone (never a customer), from the
     * store's number; answers ok. `POST /v1/stores/zid/{store}/test`.
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
     * @tawked-operation stores.zid.test.send
     */
    public function send(string $store, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/stores/zid/'.self::segment($store, 'store').'/test',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }
}
