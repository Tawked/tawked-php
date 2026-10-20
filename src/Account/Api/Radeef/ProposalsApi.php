<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Radeef;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->radeef->proposals`: the Account API's `radeef.proposals` operations.
 */
final class ProposalsApi extends AccountNamespace
{
    /**
     * Set aside one proposal of a reading (it stays among the set-aside ones, to restore); answers
     * its id and status. `POST
     * /v1/applications/{application}/radeef/knowledge/review/{proposal}/reject`.
     *
     * Permission `radeef:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $application `application` in the path
     * @param string $proposal `proposal` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation radeef.proposals.reject
     */
    public function reject(string $application, string $proposal, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/radeef/knowledge/review/'.self::segment($proposal, 'proposal').'/reject',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * Bring a set-aside proposal back to the review; answers its id and status. `POST
     * /v1/applications/{application}/radeef/knowledge/review/{proposal}/restore`.
     *
     * Permission `radeef:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $application `application` in the path
     * @param string $proposal `proposal` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation radeef.proposals.restore
     */
    public function restore(string $application, string $proposal, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/radeef/knowledge/review/'.self::segment($proposal, 'proposal').'/restore',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }
}
