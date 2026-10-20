<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Verify;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountPage;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;
use Tawked\Pagination\Paginator;

/**
 * `$account->verify->verifications`: the Account API's `verify.verifications` operations.
 */
final class VerificationsApi extends AccountNamespace
{
    /**
     * The application's verification log, newest first, narrowed by `status` (waiting, verified,
     * failed, expired, canceled), `mode` (live, test), `range` (today, 7d, 30d, 90d) and `q`: each
     * code's masked number, status, attempts, key, times; its price for who sees the money. `GET
     * /v1/applications/{application}/verify/verifications`.
     *
     * Permission `verify:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param array{mode?: string, page?: int, q?: string, range?: string, status?: string}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation verify.verifications.list
     */
    public function list(string $application, array $params = [], array $options = []): AccountPage
    {
        return AccountPage::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/verify/verifications',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
            query: $params,
        ));
    }

    /**
     * Every item of `list()`, page after page (following `next_cursor`): the same filters, and `limit` for the page size.
     *
     * @param array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @return \Generator<int, mixed>
     */
    public function iterate(string $application, array $params = [], array $options = []): \Generator
    {
        return Paginator::cursor(fn(array $page): AccountPage => $this->list($application, $page, $options), $params, 'cursor');
    }

    /**
     * One verification of the application by its id (from verify.test.send or
     * verify.verifications.list): its status, masked number, attempts, key and times; its price for
     * who sees the money. `GET /v1/applications/{application}/verify/verifications/{verification}`.
     *
     * Permission `verify:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param string $verification `verification` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation verify.verifications.show
     */
    public function show(string $application, string $verification, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/verify/verifications/'.self::segment($verification, 'verification'),
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }
}
