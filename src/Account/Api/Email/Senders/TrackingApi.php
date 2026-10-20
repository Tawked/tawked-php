<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Email\Senders;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->email->senders->tracking`: the Account API's `email.senders.tracking` operations.
 */
final class TrackingApi extends AccountNamespace
{
    /**
     * Switch open and click tracking on or off for a verified sender domain (`opens`, `clicks`) and
     * choose its tracking subdomain's label (`label`, `links` by default: links.<domain>); answers
     * `tracking` (state `awaiting_dns`, `awaiting_tls`, `active` or `failing`, and the problem) and
     * the one CNAME `record` to add. `PUT
     * /v1/applications/{application}/email/senders/{domain}/tracking`.
     *
     * Permission `email:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $application `application` in the path
     * @param string $domain `domain` in the path
     * @param array{clicks?: bool, label?: string|null, opens?: bool}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation email.senders.tracking.update
     */
    public function update(string $application, string $domain, array $params = [], array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'PUT',
            path: '/v1/applications/'.self::segment($application, 'application').'/email/senders/'.self::segment($domain, 'domain').'/tracking',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
        ));
    }
}
