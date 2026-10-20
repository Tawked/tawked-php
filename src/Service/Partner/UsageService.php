<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Service\Partner;

use Tawked\Http\Transport;
use Tawked\Model\Partner\Usage;
use Tawked\Service\Service;

/**
 * `$tawked->partner->usage`: the Partner API (partner keys only).
 */
final class UsageService extends Service
{
    /**
     * Get usage. `GET /v1/partner/usage`.
     *
     * Sends, verified codes and spend per application over a date range, plus your current balance.
     * `sent` counts codes that went out whatever their outcome, `verified` the ones confirmed, and
     * `spend_halalas` the charges net of refunds. Applications with no activity in the window are
     * left out, rows are sorted by `sent`, and there is no paging.
     *
     * Retried after a network failure, a timeout or a 5xx (a read).
     *
     * Errors: internal_error, invalid_request, ip_not_allowed, key_expired, too_many_requests,
     * unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->partner->usage->get(['from' => '2026-10-01', 'to' => '2026-10-20']);
     *
     * @param array{from?: string|\DateTimeInterface, to?: string|\DateTimeInterface}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint partner-usage
     */
    public function get(array $params = [], array $options = []): Usage
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/partner/usage',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
            query: $params,
        );

        return Usage::fromArray($response->body)->withMeta($response->meta);
    }
}
