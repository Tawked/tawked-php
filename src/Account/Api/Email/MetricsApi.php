<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Email;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->email->metrics`: the Account API's `email.metrics` operations.
 */
final class MetricsApi extends AccountNamespace
{
    /**
     * The application's e-mail figures over Riyadh days: sent, delivered, delayed, bounced
     * (permanent and transient), complained, failed, suppressed, opens and clicks, as totals, rates
     * and one row per day, week (from Sunday) or month; answers `totals`, `rates` and `data`. `GET
     * /v1/applications/{application}/email/metrics`.
     *
     * Permission `email:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation email.metrics.show
     */
    public function show(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/email/metrics',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }
}
