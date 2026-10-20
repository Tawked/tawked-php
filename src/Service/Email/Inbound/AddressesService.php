<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Service\Email\Inbound;

use Tawked\Http\Transport;
use Tawked\Model\Email\EmailInboundAddressListResponse;
use Tawked\Service\Service;

/**
 * `$tawked->email->inbound->addresses`: Tawked Email (early access: an account must be granted Email first).
 */
final class AddressesService extends Service
{
    /**
     * List receiving addresses. `GET /v1/email/inbound-addresses`.
     *
     * Lists the application's receiving addresses, oldest first: its kind (`domain`, a subdomain of
     * your domain, or `hosted`, one Tawked gives), the domain where every local part reaches you,
     * an example, its state, a hold, what it does with spam and its automatic forward. Addresses
     * are added in the console.
     *
     * Scope `email:inbound`. Retried after a network failure, a timeout or a 5xx (a read). Early
     * access: the account must be granted it first.
     *
     * Errors: account_not_active, insufficient_scope, internal_error, ip_not_allowed, key_expired,
     * live_key_required, not_found, too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->email->inbound->addresses->list();
     *
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-inbound-addresses
     * @tawked-stability early_access
     */
    public function list(array $options = []): EmailInboundAddressListResponse
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/email/inbound-addresses',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        );

        return EmailInboundAddressListResponse::fromArray($response->body)->withMeta($response->meta);
    }
}
