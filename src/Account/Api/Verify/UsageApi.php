<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Verify;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->verify->usage`: the Account API's `verify.usage` operations.
 */
final class UsageApi extends AccountNamespace
{
    /**
     * The application's Verify usage for a `period` (today, 7d, month, 30d; Riyadh time) against
     * the same span before: codes sent, delivered, verified, expired, failed, not delivered
     * (refunded), the verified rate, and what the wallet was charged (`charged_halalas`, for who
     * sees the money). `GET /v1/applications/{application}/verify/usage`.
     *
     * Permission `verify:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param array{period?: string|null}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation verify.usage.show
     */
    public function show(string $application, array $params = [], array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/verify/usage',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
            query: $params,
        ));
    }
}
