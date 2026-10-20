<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Email;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->email->limit`: the Account API's `email.limit` operations.
 */
final class LimitApi extends AccountNamespace
{
    /**
     * Take back the pending request for a higher daily limit; answers the limit page. `DELETE
     * /v1/applications/{application}/email/limit`.
     *
     * Permission `email:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $application `application` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation email.limit.cancel
     */
    public function cancel(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'DELETE',
            path: '/v1/applications/'.self::segment($application, 'application').'/email/limit',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * Ask Tawked's team for a higher daily e-mail limit (`requested_limit`, one of the offered
     * choices up to the maximum; a note); answers the limit page with the request `pending` and
     * `next`: read email.limit.show every few hours until the team answers. `POST
     * /v1/applications/{application}/email/limit`.
     *
     * Permission `email:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $application `application` in the path
     * @param array{note?: string|null, requested_limit: int}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation email.limit.request
     */
    public function request(string $application, array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/email/limit',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
        ));
    }

    /**
     * The account's daily e-mail limit: today's use against it (each recipient counts), the
     * requests a second (`per_second`; `per_minute` is the same rate a minute), when it resets,
     * whether it refused a send today; this month's free e-mails (`free_allowance`) and the price
     * after them (`price`); the higher limits one may ask for; the latest request with the team's
     * answer; and why a request is not open now (`blocked`, `available_from`). `GET
     * /v1/applications/{application}/email/limit`.
     *
     * Permission `email:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation email.limit.show
     */
    public function show(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/email/limit',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }
}
