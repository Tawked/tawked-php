<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Service\Partner;

use Tawked\Http\Transport;
use Tawked\Model\Partner\ScreeningResult;
use Tawked\Service\Service;

/**
 * `$tawked->partner->screening`: the Partner API (partner keys only).
 */
final class ScreeningService extends Service
{
    /**
     * Preview the screening. `POST /v1/partner/screening`.
     *
     * The verdict two brand names would get, and what would happen next under your approval mode,
     * without creating anything: whether a provision would wait for a decision, and whether that
     * decision is yours or Tawked's. Warn the merchant at signup instead of after.
     *
     * Never retried once it may have reached the server: each try spends something.
     *
     * Errors: internal_error, invalid_json, invalid_request, ip_not_allowed, key_expired,
     * too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->partner->screening->check(['name_ar' => 'متجر الورد', 'name_en' => 'Flower Shop']);
     *
     * @param array{name_ar: string, name_en: string}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint partner-screening
     */
    public function check(array $params, array $options = []): ScreeningResult
    {
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/partner/screening',
            options: $options,
            retry: Transport::NEVER,
            timeout: 30.0,
            json: self::body($params, []),
        );

        return ScreeningResult::fromArray($response->body)->withMeta($response->meta);
    }
}
