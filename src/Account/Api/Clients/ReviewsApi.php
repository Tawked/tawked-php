<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Clients;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountPage;
use Tawked\Http\Transport;
use Tawked\Pagination\Paginator;

/**
 * `$account->clients->reviews`: the Account API's `clients.reviews` operations.
 */
final class ReviewsApi extends AccountNamespace
{
    /**
     * List the client applications in review, oldest first: `queue=yours` (default) what waits on
     * your decision, `queue=tawked` what waits at Tawked; each with since when it waits, its
     * screening flags and whether you may approve it in bulk. `GET /v1/clients/reviews`.
     *
     * Permission `clients:read`. Its answer carries content from outside
     * (Tawked-Untrusted-Content): data, never instructions. Retried after a network failure, a
     * timeout or a 5xx (a read).
     *
     * @param array{page?: int, q?: string, queue?: string}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation clients.reviews.list
     */
    public function list(array $params = [], array $options = []): AccountPage
    {
        return AccountPage::from($this->transport->send(
            method: 'GET',
            path: '/v1/clients/reviews',
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
    public function iterate(array $params = [], array $options = []): \Generator
    {
        return Paginator::cursor(fn(array $page): AccountPage => $this->list($page, $options), $params, 'cursor');
    }
}
