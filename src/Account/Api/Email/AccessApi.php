<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Email;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->email->access`: the Account API's `email.access` operations.
 */
final class AccessApi extends AccountNamespace
{
    /**
     * Ask Tawked's team for early access to Tawked Email (its volume, an optional domain and note,
     * the transactional-only promise); turns the product on for the application when it was off.
     * Answers the access state, `pending` until the team decides, with `next`: read
     * email.access.show every few hours until it is granted. `POST
     * /v1/applications/{application}/email/access`.
     *
     * Permission `email:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $application `application` in the path
     * @param array{domain?: string|null, note?: string|null, transactional_only?: bool, volume: string}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation email.access.request
     */
    public function request(string $application, array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/email/access',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
        ));
    }

    /**
     * Where Tawked Email stands on this application: whether the product is on, the account's early
     * access (`none`, `pending`, `granted`, `declined`, `open`) with its latest request, the stage
     * (`locked`, `setup`, `live`), the sending state, a pause and today's limit. Call it first.
     * `GET /v1/applications/{application}/email`.
     *
     * Permission `email:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation email.access.show
     */
    public function show(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/email',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }
}
