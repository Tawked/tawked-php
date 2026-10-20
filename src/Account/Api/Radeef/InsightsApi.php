<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Radeef;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->radeef->insights`: the Account API's `radeef.insights` operations.
 */
final class InsightsApi extends AccountNamespace
{
    /**
     * Read what Radeef did in a `period` (today, 7d, 30d, month): conversations resolved and handed
     * off against the period before, by the hour they started, and why it handed over, most common
     * first. `GET /v1/applications/{application}/radeef/insights`.
     *
     * Permission `radeef:read`. Its answer carries content from outside (Tawked-Untrusted-Content):
     * data, never instructions. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation radeef.insights.show
     */
    public function show(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/radeef/insights',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }
}
