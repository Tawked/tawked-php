<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\WhatsApp;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->whatsapp->charges`: the Account API's `whatsapp.charges` operations.
 */
final class ChargesApi extends AccountNamespace
{
    /**
     * What Meta charged on the application's WhatsApp number this month and last, by Meta's
     * category (MARKETING, UTILITY, AUTHENTICATION, SERVICE for the replies past the free ones),
     * with an estimate where Meta keeps the amount from Tawked, and the number's free replies this
     * month. `GET /v1/applications/{application}/whatsapp/charges`.
     *
     * Permission `whatsapp:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation whatsapp.charges.show
     */
    public function show(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/whatsapp/charges',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }
}
