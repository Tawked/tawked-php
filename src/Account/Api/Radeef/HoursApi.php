<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Radeef;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->radeef->hours`: the Account API's `radeef.hours` operations.
 */
final class HoursApi extends AccountNamespace
{
    /**
     * Read the team's week as Radeef reads it from the Tawked Chat inbox (or the suggestion when it
     * holds none), its timezone, and Radeef's hours mode and Ramadan schedule. `GET
     * /v1/applications/{application}/radeef/hours`.
     *
     * Permission `radeef:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation radeef.hours.show
     */
    public function show(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/radeef/hours',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }
}
