<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Service\Email;

use Tawked\Http\Transport;
use Tawked\Model\Email\EmailDomain;
use Tawked\Model\Email\EmailDomainDeleteResponse;
use Tawked\Model\Email\EmailDomainListResponse;
use Tawked\Pagination\Paginator;
use Tawked\Service\Service;

/**
 * `$tawked->email->domains`: Tawked Email (early access: an account must be granted Email first).
 */
final class DomainsService extends Service
{
    /**
     * List domains. `GET /v1/email/domains`.
     *
     * Lists the application's sending domains, newest first, a page at a time: follow `next_cursor`
     * while `has_more` is true. A live key of the application with `email:read` (or `email:send`,
     * or `email:manage`).
     *
     * Scope `email:read`. Retried after a network failure, a timeout or a 5xx (a read). Early
     * access: the account must be granted it first.
     *
     * Errors: account_not_active, application_key_required, email_access_pending,
     * email_access_required, email_product_off, insufficient_scope, internal_error,
     * invalid_request, ip_not_allowed, key_expired, live_key_required, too_many_requests,
     * unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->email->domains->list(['state' => 'verified', 'limit' => 20]);
     *
     * @param array{state?: string, limit?: int, cursor?: string}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-domains
     * @tawked-stability early_access
     */
    public function list(array $params = [], array $options = []): EmailDomainListResponse
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/email/domains',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
            query: $params,
        );

        return EmailDomainListResponse::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Every item of `list()`, page after page (following `next_cursor`): the same filters, and `limit` for the page size.
     *
     * @param array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @return \Generator<int, \Tawked\Model\Email\EmailDomain>
     */
    public function iterate(array $params = [], array $options = []): \Generator
    {
        return Paginator::cursor(fn(array $page): EmailDomainListResponse => $this->list($page, $options), $params, 'cursor');
    }

    /**
     * Add a domain. `POST /v1/email/domains`.
     *
     * Adds a sending domain to the application and answers its records to put at your DNS host: the
     * DKIM key's TXT record, and the return path's MX and TXT records. Tawked checks them every ten
     * minutes, or at once with `POST /v1/email/domains/{id}/verify`. `201` with the new domain;
     * `200` with the one the application already has. A live key of the application with the
     * explicit `email:manage`: a full-access key does not hold it. At most ten a minute for an
     * application.
     *
     * Scope `email:manage`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again. Early access: the account must be granted it first.
     *
     * Errors: account_not_active, application_key_required, domain_invalid, domain_limit,
     * domain_public, domain_taken, domain_tawked, email_access_pending, email_access_required,
     * email_product_off, email_unavailable, insufficient_scope, internal_error, invalid_json,
     * invalid_request, ip_not_allowed, key_expired, live_key_required, rate_limited,
     * too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->email->domains->create(['name' => 'acme.sa']);
     *
     * @param array{name: string}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-domain-create
     * @tawked-stability early_access
     */
    public function create(array $params, array $options = []): EmailDomain
    {
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/email/domains',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
        );

        return EmailDomain::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Get a domain. `GET /v1/email/domains/{id}`.
     *
     * Reads one sending domain of the application: its state, its records and each one's state, its
     * defaults, how its mail authenticates and, while it is on, its open and click tracking. A
     * removed domain, or another application's, answers `404`. A live key of the application with
     * `email:read` (or `email:send`, or `email:manage`).
     *
     * Scope `email:read`. Retried after a network failure, a timeout or a 5xx (a read). Early
     * access: the account must be granted it first.
     *
     * Errors: account_not_active, application_key_required, email_access_pending,
     * email_access_required, email_product_off, insufficient_scope, internal_error, ip_not_allowed,
     * key_expired, live_key_required, not_found, too_many_requests, unauthorized
     * (Tawked\ErrorCode).
     *
     * @example $tawked->email->domains->get('0199f1c2-7a41-7c3e-9d2b-5e8f1a2b3c4d');
     *
     * @param string $id `id` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-domain
     * @tawked-stability early_access
     */
    public function get(string $id, array $options = []): EmailDomain
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/email/domains/'.self::segment($id, 'id'),
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        );

        return EmailDomain::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Update a domain. `PATCH /v1/email/domains/{id}`.
     *
     * Changes a verified domain's defaults (the sender's name and the address's local part a send
     * uses when it names none), and switches its open and click tracking on its own subdomain.
     * Every field is optional. Tracking needs one CNAME record at your DNS host (`links.<domain>`
     * to `track.tawked.com`); the domain's `tracking.state` moves from `awaiting_dns` to
     * `awaiting_tls` to `active`. A live key of the application with the explicit `email:manage`: a
     * full-access key does not hold it.
     *
     * Scope `email:manage`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again. Early access: the account must be granted it first.
     *
     * Errors: account_not_active, application_key_required, domain_not_verified,
     * email_access_pending, email_access_required, email_product_off, email_unavailable,
     * insufficient_scope, internal_error, invalid_json, invalid_request, ip_not_allowed,
     * key_expired, live_key_required, not_found, too_many_requests, tracking_subdomain_invalid,
     * tracking_subdomain_taken, tracking_unavailable, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->email->domains->update('0199f1c2-7a41-7c3e-9d2b-5e8f1a2b3c4d', ['from_name' => 'متجر أكمي', 'from_local' => 'orders']);
     *
     * @param string $id `id` in the path
     * @param array{from_name?: string, from_local?: string, tracking?: array<string, mixed>}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-domain-update
     * @tawked-stability early_access
     */
    public function update(string $id, array $params = [], array $options = []): EmailDomain
    {
        $response = $this->transport->send(
            method: 'PATCH',
            path: '/v1/email/domains/'.self::segment($id, 'id'),
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, ['tracking']),
        );

        return EmailDomain::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Check a domain now. `POST /v1/email/domains/{id}/verify`.
     *
     * Checks the domain's records now instead of at the next ten-minute check, its tracking
     * subdomain's too, and answers the domain as it stands after the check. At most six a minute
     * for an application (`429 rate_limited`). A live key of the application with the explicit
     * `email:manage`: a full-access key does not hold it.
     *
     * Scope `email:manage`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again. Early access: the account must be granted it first.
     *
     * Errors: account_not_active, application_key_required, email_access_pending,
     * email_access_required, email_product_off, email_unavailable, insufficient_scope,
     * internal_error, invalid_json, invalid_request, ip_not_allowed, key_expired,
     * live_key_required, not_found, rate_limited, too_many_requests, unauthorized
     * (Tawked\ErrorCode).
     *
     * @example $tawked->email->domains->verify('0199f1c2-7a41-7c3e-9d2b-5e8f1a2b3c4d');
     *
     * @param string $id `id` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-domain-verify
     * @tawked-stability early_access
     */
    public function verify(string $id, array $options = []): EmailDomain
    {
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/email/domains/'.self::segment($id, 'id').'/verify',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        );

        return EmailDomain::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Remove a domain. `DELETE /v1/email/domains/{id}`.
     *
     * Removes a sending domain from the application: nothing sends from it any more, and a
     * `domain.removed` event goes to the endpoints that receive it. Its messages stay in the log
     * for their days. A live key of the application with the explicit `email:manage`: a full-access
     * key does not hold it.
     *
     * Scope `email:manage`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again. Early access: the account must be granted it first.
     *
     * Errors: account_not_active, application_key_required, email_access_pending,
     * email_access_required, email_product_off, email_unavailable, insufficient_scope,
     * internal_error, invalid_json, invalid_request, ip_not_allowed, key_expired,
     * live_key_required, not_found, too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->email->domains->delete('0199f1c2-7a41-7c3e-9d2b-5e8f1a2b3c4d');
     *
     * @param string $id `id` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-domain-delete
     * @tawked-stability early_access
     */
    public function delete(string $id, array $options = []): EmailDomainDeleteResponse
    {
        $response = $this->transport->send(
            method: 'DELETE',
            path: '/v1/email/domains/'.self::segment($id, 'id'),
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        );

        return EmailDomainDeleteResponse::fromArray($response->body)->withMeta($response->meta);
    }
}
