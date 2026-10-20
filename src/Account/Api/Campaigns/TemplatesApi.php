<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Campaigns;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountPage;
use Tawked\Http\Transport;
use Tawked\Pagination\Paginator;

/**
 * `$account->campaigns->templates`: the Account API's `campaigns.templates` operations.
 */
final class TemplatesApi extends AccountNamespace
{
    /**
     * List the number's marketing templates as a campaign may use them: each with its label,
     * language, placeholders to map and, when it cannot be picked, why (`reason`: pending,
     * no_stop…). `GET /v1/applications/{application}/campaigns/templates`.
     *
     * Permission `campaigns:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation campaigns.templates.list
     */
    public function list(string $application, array $options = []): AccountPage
    {
        return $this->listPage($application, [], $options);
    }

    /**
     * @param array<string, mixed> $query
     * @param array<string, mixed> $options
     */
    private function listPage(string $application, array $query, array $options): AccountPage
    {
        return AccountPage::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/campaigns/templates',
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
    public function iterate(string $application, array $params = [], array $options = []): \Generator
    {
        return Paginator::cursor(fn(array $page): AccountPage => $this->listPage($application, $page, $options), $params, 'cursor');
    }
}
