<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Service\Partner;

use Tawked\Http\Transport;
use Tawked\Model\Partner\EventPage;
use Tawked\Pagination\Paginator;
use Tawked\Service\Service;

/**
 * `$tawked->partner->events`: the Partner API (partner keys only).
 */
final class EventsService extends Service
{
    /**
     * Get the event feed. `GET /v1/partner/events`.
     *
     * Your event log as a feed, oldest first: every lifecycle and balance event whether or not a
     * webhook delivered it, plus the verification events you subscribed to. Poll it instead of
     * running a webhook, or catch up after an outage: keep `next_cursor` and pass it back as
     * `cursor`; it points after the last row you received whether or not more rows exist now, and
     * `has_more` says whether a page is waiting. Rows younger than two seconds are held back so a
     * saved cursor never skips one. At least once: dedupe by `id`.
     *
     * Retried after a network failure, a timeout or a 5xx (a read).
     *
     * Errors: internal_error, invalid_request, ip_not_allowed, key_expired, too_many_requests,
     * unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->partner->events->list(['event' => 'service.approved', 'limit' => 50]);
     *
     * @param array{since?: string|\DateTimeInterface, cursor?: string, event?: string, application?: string, limit?: int}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint partner-events
     */
    public function list(array $params = [], array $options = []): EventPage
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/partner/events',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
            query: $params,
        );

        return EventPage::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Every item of `list()`, page after page (following `next_cursor`): the same filters, and `limit` for the page size.
     *
     * @param array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @return \Generator<int, \Tawked\Model\Partner\PartnerEvent>
     */
    public function iterate(array $params = [], array $options = []): \Generator
    {
        return Paginator::cursor(fn(array $page): EventPage => $this->list($page, $options), $params, 'cursor');
    }
}
