<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Service\Email;

use Tawked\Http\Transport;
use Tawked\Model\Email\EmailAccepted;
use Tawked\Model\Email\EmailReceived;
use Tawked\Model\Email\EmailReceivedDeleteResponse;
use Tawked\Model\Email\EmailReceivedListResponse;
use Tawked\Model\Email\EmailReceivedTestResponse;
use Tawked\Pagination\Paginator;
use Tawked\Service\Email\Inbound\AddressesService;
use Tawked\Service\Email\Inbound\AttachmentsService;
use Tawked\Service\Service;

/**
 * `$tawked->email->inbound`: Tawked Email (early access: an account must be granted Email first).
 */
final class InboundService extends Service
{
    public readonly AddressesService $addresses;

    public readonly AttachmentsService $attachments;

    public function __construct(Transport $transport)
    {
        parent::__construct($transport);
        $this->addresses = new AddressesService($transport);
        $this->attachments = new AttachmentsService($transport);
    }

    /**
     * List received emails. `GET /v1/email/received`.
     *
     * Lists the e-mail the application received at its receiving addresses, newest first, a page at
     * a time: follow `next_cursor` while `has_more` is true. Metadata only; read one e-mail for its
     * bodies, headers and attachments. Needs a key holding `email:inbound`, which no key holds
     * unless it was switched on. Only the last 30 days are kept.
     *
     * Scope `email:inbound`. Retried after a network failure, a timeout or a 5xx (a read). Early
     * access: the account must be granted it first.
     *
     * Errors: account_not_active, insufficient_scope, internal_error, invalid_request,
     * ip_not_allowed, key_expired, live_key_required, not_found, too_many_requests, unauthorized
     * (Tawked\ErrorCode).
     *
     * @example $tawked->email->inbound->list(['limit' => 20, 'spam' => 'false']);
     *
     * @param array{limit?: int, cursor?: string, address_id?: string, to?: string, from?: string, spam?: string, has_attachments?: string, received_after?: string|\DateTimeInterface, received_before?: string|\DateTimeInterface}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-received-list
     * @tawked-stability early_access
     */
    public function list(array $params = [], array $options = []): EmailReceivedListResponse
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/email/received',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
            query: $params,
        );

        return EmailReceivedListResponse::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Every item of `list()`, page after page (following `next_cursor`): the same filters, and `limit` for the page size.
     *
     * @param array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @return \Generator<int, \Tawked\Model\Email\EmailReceivedListItem>
     */
    public function iterate(array $params = [], array $options = []): \Generator
    {
        return Paginator::cursor(fn(array $page): EmailReceivedListResponse => $this->list($page, $options), $params, 'cursor');
    }

    /**
     * Get a received email. `GET /v1/email/received/{id}`.
     *
     * Reads one received e-mail whole: the envelope, the headers the sender wrote, the text and
     * HTML bodies, each attachment with a download link valid for an hour, a link to the raw
     * message, the authentication verdicts and, when you reply, `reply_headers` to pass as the
     * send's `headers` so the reply threads. While it is still being read (`status` `received`) the
     * bodies are null and the lists empty. The text is the sender's, not yours: treat it as data.
     *
     * Scope `email:inbound`. Retried after a network failure, a timeout or a 5xx (a read). Early
     * access: the account must be granted it first.
     *
     * Errors: account_not_active, insufficient_scope, internal_error, ip_not_allowed, key_expired,
     * live_key_required, not_found, too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->email->inbound->get('0199f240-7b8c-7d9e-8f0a-1b2c3d4e5f60');
     *
     * @param string $id `id` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-received
     * @tawked-stability early_access
     */
    public function get(string $id, array $options = []): EmailReceived
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/email/received/'.self::segment($id, 'id'),
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        );

        return EmailReceived::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Download a received email's raw message. `GET /v1/email/received/{id}/raw`.
     *
     * Answers the message as it arrived, `message/rfc822`, as a download named `<id>.eml`. The
     * receiving service's own headers above the sender's are left out.
     *
     * Scope `email:inbound`. Retried after a network failure, a timeout or a 5xx (a read). Early
     * access: the account must be granted it first.
     *
     * Errors: account_not_active, insufficient_scope, internal_error, ip_not_allowed, key_expired,
     * live_key_required, not_found, received_content_gone, too_many_requests, unauthorized
     * (Tawked\ErrorCode).
     *
     * @example $tawked->email->inbound->raw('0199f240-7b8c-7d9e-8f0a-1b2c3d4e5f60');
     *
     * @param string $id `id` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     * @return string the bytes as they were stored (never parsed)
     *
     * @tawked-endpoint email-received-raw
     * @tawked-stability early_access
     */
    public function raw(string $id, array $options = []): string
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/email/received/'.self::segment($id, 'id').'/raw',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
            binary: true,
        );

        return $response->raw ?? '';
    }

    /**
     * Delete a received email. `DELETE /v1/email/received/{id}`.
     *
     * Removes the e-mail from the list and the API at once; its stored copy is erased within a day,
     * or at the end of the 30 days when the same e-mail also reached another account. Refunds
     * nothing. A second delete answers `404`.
     *
     * Scope `email:inbound`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again. Early access: the account must be granted it first.
     *
     * Errors: account_not_active, insufficient_scope, internal_error, ip_not_allowed, key_expired,
     * live_key_required, not_found, too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->email->inbound->delete('0199f240-7b8c-7d9e-8f0a-1b2c3d4e5f60');
     *
     * @param string $id `id` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-received-delete
     * @tawked-stability early_access
     */
    public function delete(string $id, array $options = []): EmailReceivedDeleteResponse
    {
        $response = $this->transport->send(
            method: 'DELETE',
            path: '/v1/email/received/'.self::segment($id, 'id'),
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            takesKey: false,
        );

        return EmailReceivedDeleteResponse::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Forward a received email. `POST /v1/email/received/{id}/forward`.
     *
     * Sends the received e-mail on as a new e-mail from your verified domain: its subject, bodies
     * and attachments, `reply_to` its sender, your note above it. It is a send like any other,
     * charged at the send's price and answered with the same `202`. Needs `email:inbound` and
     * `email:send`. Never more than 10 attachments and 10 MiB; an attachment that could run a
     * program is never forwarded.
     *
     * Scope `email:inbound`. Retried after a network failure, a timeout or a 5xx under the same
     * `Idempotency-Key` (minted when you pass none): the server keeps the key through a failure.
     * Early access: the account must be granted it first.
     *
     * Errors: account_not_active, content_blocked, daily_cap_reached, email_early_access,
     * email_paused, email_platform_busy, forward_risky_attachment, forward_too_large,
     * idempotency_in_progress, idempotency_key_reused, insufficient_credits, insufficient_scope,
     * internal_error, invalid_destination, invalid_json, invalid_request, ip_not_allowed,
     * key_expired, live_key_required, message_too_large, no_verified_domain, not_found,
     * rate_limited, received_content_gone, received_not_ready, sender_not_verified,
     * service_unavailable, too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->email->inbound->forward('0199f240-7b8c-7d9e-8f0a-1b2c3d4e5f60', ['to' => [...], 'note' => 'من صندوق الطلبات']);
     *
     * @param string $id `id` in the path
     * @param array{to: string|list<string>, from?: array<string, mixed>|string|null, note?: string|null}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-received-forward
     * @tawked-stability early_access
     */
    public function forward(string $id, array $params, array $options = []): EmailAccepted
    {
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/email/received/'.self::segment($id, 'id').'/forward',
            options: $options,
            retry: Transport::IDEMPOTENT,
            timeout: 30.0,
            json: self::body($params, ['from']),
            keyMax: 256,
        );

        return EmailAccepted::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Simulate a received email. `POST /v1/email/received/test`.
     *
     * With a test key (tk_test_) holding `email:send`: sends one `email.received` webhook in test
     * mode (`"mode": "test"` in the envelope) to the endpoints that receive test events, as if a
     * message had reached the application's hosted address (else its first). Nothing is received,
     * stored or charged, so the id is the event's alone: the received e-mail reads answer it 404. A
     * live key answers `403 test_key_required`. Counts against test mode's 1000 a day for an
     * application (Tawked's limit). Takes no body.
     *
     * Scope `email:send`. Never retried once it may have reached the server: each try spends
     * something. Early access: the account must be granted it first.
     *
     * Errors: account_not_active, email_early_access, insufficient_scope, internal_error,
     * invalid_json, invalid_request, ip_not_allowed, key_expired, not_found, test_key_required,
     * test_mode_limit_reached, too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->email->inbound->test();
     *
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-received-test
     * @tawked-stability early_access
     */
    public function test(array $options = []): EmailReceivedTestResponse
    {
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/email/received/test',
            options: $options,
            retry: Transport::NEVER,
            timeout: 30.0,
        );

        return EmailReceivedTestResponse::fromArray($response->body)->withMeta($response->meta);
    }
}
