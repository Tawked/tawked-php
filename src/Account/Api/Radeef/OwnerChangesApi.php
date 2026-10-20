<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Radeef;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->radeef->ownerChanges`: the Account API's `radeef.owner_changes` operations.
 */
final class OwnerChangesApi extends AccountNamespace
{
    /**
     * Apply a change card Radeef proposed in owner mode (optionally with `edits` to its question,
     * answer, topic, text, to or word); answers the card, applied. `POST
     * /v1/applications/{application}/radeef/owner/changes/{change}/apply`.
     *
     * Permission `radeef:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $application `application` in the path
     * @param string $change `change` in the path
     * @param array{edits?: array<string, mixed>}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation radeef.owner_changes.apply
     */
    public function apply(string $application, string $change, array $params = [], array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/radeef/owner/changes/'.self::segment($change, 'change').'/apply',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, ['edits']),
        ));
    }

    /**
     * Dismiss a change card Radeef proposed in owner mode; answers the card. `POST
     * /v1/applications/{application}/radeef/owner/changes/{change}/dismiss`.
     *
     * Permission `radeef:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $application `application` in the path
     * @param string $change `change` in the path
     * @param array{edits?: array<string, mixed>}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation radeef.owner_changes.dismiss
     */
    public function dismiss(string $application, string $change, array $params = [], array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/radeef/owner/changes/'.self::segment($change, 'change').'/dismiss',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, ['edits']),
        ));
    }

    /**
     * Undo an applied change card: the value before is written back, while what it wrote still
     * stands; answers the card. `POST
     * /v1/applications/{application}/radeef/owner/changes/{change}/undo`.
     *
     * Permission `radeef:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $application `application` in the path
     * @param string $change `change` in the path
     * @param array{edits?: array<string, mixed>}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation radeef.owner_changes.undo
     */
    public function undo(string $application, string $change, array $params = [], array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/radeef/owner/changes/'.self::segment($change, 'change').'/undo',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, ['edits']),
        ));
    }
}
