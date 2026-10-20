<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Service\Partner;

use Tawked\Http\Transport;
use Tawked\Model\Partner\Profile;
use Tawked\Service\Service;

/**
 * `$tawked->partner->profile`: the Partner API (partner keys only).
 */
final class ProfileService extends Service
{
    /**
     * Get the profile. `GET /v1/partner/profile`.
     *
     * Who the key belongs to and what it may expect: the partner name, the approval mode (whether a
     * provision waits for a human, and whose), the price of a code for this account, the balance,
     * the rate limit, the key in use with its expiry and allowlist, and whether a webhook is
     * configured. Read it first.
     *
     * Retried after a network failure, a timeout or a 5xx (a read).
     *
     * Errors: internal_error, ip_not_allowed, key_expired, too_many_requests, unauthorized
     * (Tawked\ErrorCode).
     *
     * @example $tawked->partner->profile->get();
     *
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint partner-profile
     */
    public function get(array $options = []): Profile
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/partner/profile',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        );

        return Profile::fromArray($response->body)->withMeta($response->meta);
    }
}
