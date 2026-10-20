<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Service\Email;

use Tawked\Http\Transport;
use Tawked\Model\Email\EmailMetrics;
use Tawked\Service\Service;

/**
 * `$tawked->email->metrics`: Tawked Email (early access: an account must be granted Email first).
 */
final class MetricsService extends Service
{
    /**
     * Get email metrics. `GET /v1/email/metrics`.
     *
     * The application's e-mail figures by Riyadh day, Sunday week or month, from the daily rollup
     * Tawked keeps for good: sent, delivered, delayed, bounced (permanent and transient),
     * complained, failed, suppressed, opened and clicked, with the rates. Counts are e-mails
     * (recipients). Opens and clicks are counted in targets: a message sent to several people in
     * one call counts as one target, since one pixel serves them all. Every period of the range is
     * present, zeros included; the day in progress too, refreshed every five minutes. Test messages
     * never count. A live key of the application with `email:read` (or `email:send`, or
     * `email:manage`).
     *
     * Scope `email:read`. Retried after a network failure, a timeout or a 5xx (a read). Early
     * access: the account must be granted it first.
     *
     * Errors: account_not_active, application_key_required, email_access_pending,
     * email_access_required, email_product_off, insufficient_scope, internal_error,
     * invalid_request, ip_not_allowed, key_expired, live_key_required, not_found,
     * too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->email->metrics->get(['from' => '2026-10-01', 'to' => '2026-10-20', 'granularity' => 'day']);
     *
     * @param array{from?: string, to?: string, granularity?: string, domain?: string, timezone?: string}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-metrics
     * @tawked-stability early_access
     */
    public function get(array $params = [], array $options = []): EmailMetrics
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/email/metrics',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
            query: $params,
        );

        return EmailMetrics::fromArray($response->body)->withMeta($response->meta);
    }
}
