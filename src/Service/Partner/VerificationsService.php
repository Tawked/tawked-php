<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Service\Partner;

use Tawked\Http\Transport;
use Tawked\Model\Partner\VerificationPage;
use Tawked\Pagination\Paginator;
use Tawked\Service\Service;

/**
 * `$tawked->partner->verifications`: the Partner API (partner keys only).
 */
final class VerificationsService extends Service
{
    /**
     * List verifications. `GET /v1/partner/verifications`.
     *
     * Every code your key sent, newest first, across all your applications. Each row is the shape
     * of `GET /v1/verify/{id}` plus `application_id`, your `external_id`, the key `mode` it was
     * sent under, the `lang` and the `price_halalas` it was charged at. Keyset paging with no
     * totals: keep `next_cursor` and pass it back as `cursor`; `has_more` says whether another page
     * is waiting. An archived application keeps its rows.
     *
     * Retried after a network failure, a timeout or a 5xx (a read).
     *
     * Errors: internal_error, invalid_request, ip_not_allowed, key_expired, not_found,
     * too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->partner->verifications->list(['status' => 'verified', 'limit' => 50]);
     *
     * @param array{application?: string, status?: string, mode?: string, reference?: string, from?: string|\DateTimeInterface, to?: string|\DateTimeInterface, cursor?: string, limit?: int}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint partner-verifications
     */
    public function list(array $params = [], array $options = []): VerificationPage
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/partner/verifications',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
            query: $params,
        );

        return VerificationPage::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Every item of `list()`, page after page (following `next_cursor`): the same filters, and `limit` for the page size.
     *
     * @param array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @return \Generator<int, \Tawked\Model\Partner\PartnerVerification>
     */
    public function iterate(array $params = [], array $options = []): \Generator
    {
        return Paginator::cursor(fn(array $page): VerificationPage => $this->list($page, $options), $params, 'cursor');
    }
}
