<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountPage;
use Tawked\Account\AccountResponse;
use Tawked\Account\WaitsForApproval;
use Tawked\Http\Transport;
use Tawked\Pagination\Paginator;

/**
 * `$account->approvals`: the Account API's `approvals` operations.
 */
final class ApprovalsApi extends AccountNamespace
{
    use WaitsForApproval;

    /**
     * List the approvals this token asked its person for, newest first, each with its status
     * (pending, approved, used, declined, expired, stale), operation, request, `target` (the draft
     * it acts on, as the person saw it) and expiry. `GET /v1/approvals`.
     *
     * Permission ``. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation approvals.list
     */
    public function list(array $options = []): AccountPage
    {
        return $this->listPage([], $options);
    }

    /**
     * @param array<string, mixed> $query
     * @param array<string, mixed> $options
     */
    private function listPage(array $query, array $options): AccountPage
    {
        return AccountPage::from($this->transport->send(
            method: 'GET',
            path: '/v1/approvals',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
            query: $query,
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
    public function iterate(array $params = [], array $options = []): \Generator
    {
        return Paginator::cursor(fn(array $page): AccountPage => $this->listPage($page, $options), $params, 'cursor');
    }

    /**
     * Read one approval this token asked for: its status, what it covers (the request, and
     * `target`: the campaign, automation, auto-attendant, template draft or Radeef it acts on, as
     * the person saw it) and `next`, what to do in that status. Once it is `approved`, send exactly
     * the same request again with the header `Tawked-Approval: <id>`; it runs once. `GET
     * /v1/approvals/{approval}`.
     *
     * Permission ``. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $approval `approval` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation approvals.show
     */
    public function show(string $approval, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/approvals/'.self::segment($approval, 'approval'),
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }
}
