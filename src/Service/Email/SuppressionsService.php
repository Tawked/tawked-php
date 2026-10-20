<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Service\Email;

use Tawked\Http\Transport;
use Tawked\Model\Email\EmailSuppressionAddResponse;
use Tawked\Model\Email\EmailSuppressionListResponse;
use Tawked\Model\Email\EmailSuppressionLookup;
use Tawked\Model\Email\EmailSuppressionRemoveResponse;
use Tawked\Pagination\Paginator;
use Tawked\Service\Email\Suppressions\ImportsService;
use Tawked\Service\Service;

/**
 * `$tawked->email->suppressions`: Tawked Email (early access: an account must be granted Email first).
 */
final class SuppressionsService extends Service
{
    public readonly ImportsService $imports;

    public function __construct(Transport $transport)
    {
        parent::__construct($transport);
        $this->imports = new ImportsService($transport);
    }

    /**
     * List suppressed addresses. `GET /v1/email/suppressions`.
     *
     * Lists the addresses the account holds back that this application may see: those it
     * suppressed, and those its messages caused. The list blocks sending account-wide; an address
     * another application or Tawked's team suppressed is not listed here, and `GET
     * /v1/email/suppressions/{address}` still says it is suppressed. A live key of the application
     * with `email:read` (or `email:send`, or `email:manage`).
     *
     * Scope `email:read`. Retried after a network failure, a timeout or a 5xx (a read). Early
     * access: the account must be granted it first.
     *
     * Errors: account_not_active, application_key_required, email_access_pending,
     * email_access_required, email_product_off, insufficient_scope, internal_error,
     * invalid_request, ip_not_allowed, key_expired, live_key_required, too_many_requests,
     * unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->email->suppressions->list(['reason' => 'bounce', 'limit' => 20]);
     *
     * @param array{reason?: string, source?: string, address?: string, created_after?: string|\DateTimeInterface, created_before?: string|\DateTimeInterface, limit?: int, cursor?: string}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-suppressions
     * @tawked-stability early_access
     */
    public function list(array $params = [], array $options = []): EmailSuppressionListResponse
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/email/suppressions',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
            query: $params,
        );

        return EmailSuppressionListResponse::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Every item of `list()`, page after page (following `next_cursor`): the same filters, and `limit` for the page size.
     *
     * @param array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @return \Generator<int, \Tawked\Model\Email\EmailSuppression>
     */
    public function iterate(array $params = [], array $options = []): \Generator
    {
        return Paginator::cursor(fn(array $page): EmailSuppressionListResponse => $this->list($page, $options), $params, 'cursor');
    }

    /**
     * Suppress addresses. `POST /v1/email/suppressions`.
     *
     * Adds up to 1000 addresses to the account's list (Tawked's limit): no application of the
     * account sends to them until they are lifted. Answers how many were added, how many were there
     * already, and which were not addresses, by their place in the list. A live key of the
     * application with the explicit `email:manage`: a full-access key does not hold it.
     *
     * Scope `email:manage`. Retried after a network failure, a timeout or a 5xx under the same
     * `Idempotency-Key` (minted when you pass none): the server keeps the key through a failure.
     * Early access: the account must be granted it first.
     *
     * Errors: account_not_active, application_key_required, email_access_pending,
     * email_access_required, email_product_off, insufficient_scope, internal_error, invalid_json,
     * invalid_request, ip_not_allowed, key_expired, live_key_required,
     * suppression_invalid_addresses, suppression_too_many, too_many_requests, unauthorized
     * (Tawked\ErrorCode).
     *
     * @example $tawked->email->suppressions->add(['addresses' => [...]]);
     *
     * @param array{addresses?: string|list<string>, address?: string}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-suppressions-add
     * @tawked-stability early_access
     */
    public function add(array $params = [], array $options = []): EmailSuppressionAddResponse
    {
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/email/suppressions',
            options: $options,
            retry: Transport::IDEMPOTENT,
            timeout: 30.0,
            json: self::body($params, []),
        );

        return EmailSuppressionAddResponse::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Look up an address. `GET /v1/email/suppressions/{address}`.
     *
     * Says whether the account holds an address back. An entry this application may see answers in
     * full; one it may not (another application's, or the team's) answers `{"suppressed": true,
     * "visible": false, "lift": "team_only"}` when looked up by its address, since it still blocks
     * this application's sends. `404 not_found` when no entry of the account names the address, or
     * for the id of an entry this application may not see. A live key of the application with
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
     * @example $tawked->email->suppressions->get('sara@example.com');
     *
     * @param string $address `address` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-suppression
     * @tawked-stability early_access
     */
    public function get(string $address, array $options = []): EmailSuppressionLookup
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/email/suppressions/'.self::segment($address, 'address'),
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        );

        return EmailSuppressionLookup::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Lift an address. `DELETE /v1/email/suppressions/{address}`.
     *
     * Takes an address off the account's list, so the account's applications may send to it again.
     * A complaint is lifted only with a reason (the recipient asked, or it was reported by
     * mistake); an address that complained again after a lift, or that Tawked's team suppressed, is
     * lifted by the team alone (`403 suppression_team_only`). A live key of the application with
     * the explicit `email:manage`: a full-access key does not hold it.
     *
     * Scope `email:manage`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again. Early access: the account must be granted it first.
     *
     * Errors: account_not_active, application_key_required, email_access_pending,
     * email_access_required, email_product_off, insufficient_scope, internal_error, invalid_json,
     * invalid_request, ip_not_allowed, key_expired, live_key_required, not_found,
     * suppression_lift_reason_required, suppression_team_only, too_many_requests, unauthorized
     * (Tawked\ErrorCode).
     *
     * @example $tawked->email->suppressions->remove('sara@example.com', ['reason' => 'recipient_asked']);
     *
     * @param string $address `address` in the path
     * @param array{reason?: string, note?: string}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-suppression-delete
     * @tawked-stability early_access
     */
    public function remove(string $address, array $params = [], array $options = []): EmailSuppressionRemoveResponse
    {
        $response = $this->transport->send(
            method: 'DELETE',
            path: '/v1/email/suppressions/'.self::segment($address, 'address'),
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
        );

        return EmailSuppressionRemoveResponse::fromArray($response->body)->withMeta($response->meta);
    }
}
