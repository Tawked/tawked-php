<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountPage;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;
use Tawked\Pagination\Paginator;

/**
 * `$account->operations`: the Account API's `operations` operations.
 */
final class OperationsApi extends AccountNamespace
{
    /**
     * Search the operations by words (`q`) and area (`area`); each with its method, path, summary,
     * whether it is consequential, and `allowed`: whether this token may call it. When `allowed` is
     * false, `needs` names the permission its person would add on the token's page. The best
     * `limit` matches (default 50) in one answer. `GET /v1/operations`.
     *
     * Permission ``. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation operations.search
     */
    public function search(array $options = []): AccountPage
    {
        return $this->searchPage([], $options);
    }

    /**
     * @param array<string, mixed> $query
     * @param array<string, mixed> $options
     */
    private function searchPage(array $query, array $options): AccountPage
    {
        return AccountPage::from($this->transport->send(
            method: 'GET',
            path: '/v1/operations',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
            query: $query,
        ));
    }

    /**
     * Every item of `search()`, page after page (following `next_cursor`): the same filters, and `limit` for the page size.
     *
     * @param array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @return \Generator<int, mixed>
     */
    public function iterate(array $params = [], array $options = []): \Generator
    {
        return Paginator::cursor(fn(array $page): AccountPage => $this->searchPage($page, $options), $params, 'cursor');
    }

    /**
     * One operation in full: its path parameters, the JSON Schema of its body (a write) or its
     * query (a read), `allowed` (whether this token may call it) and `next` (how to call it). When
     * `allowed` is false, `needs` names the permission and `human_action` opens the token's page
     * with it ready to allow. `GET /v1/operations/{operation}`.
     *
     * Permission ``. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $operation `operation` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation operations.show
     */
    public function show(string $operation, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/operations/'.self::segment($operation, 'operation'),
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }
}
