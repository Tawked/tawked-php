<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Service\Email;

use Tawked\Http\Transport;
use Tawked\Model\Email\EmailBatchSendResponse;
use Tawked\Model\Email\EmailMessageContentResponse;
use Tawked\Model\Email\EmailMessageExtended;
use Tawked\Model\Email\EmailMessageGetResponse;
use Tawked\Model\Email\EmailMessageListResponse;
use Tawked\Model\Email\EmailSendResponse;
use Tawked\Pagination\Paginator;
use Tawked\Service\Service;

/**
 * `$tawked->email->messages`: Tawked Email (early access: an account must be granted Email first).
 */
final class MessagesService extends Service
{
    /**
     * Send an email. `POST /v1/email/messages`.
     *
     * Sends one message to up to 50 recipients (`to`, `cc`, `bcc`) from an address on a verified
     * domain of the key's application, and answers `202` with an id to track it. The message is
     * charged and queued at once, and sent in its turn, or at `scheduled_at`. A recipient on the
     * account's suppression list is skipped and not charged. Each recipient is refunded once if it
     * bounces, and the whole message if it fails or is canceled; a complaint keeps it. Two shapes:
     * one `to` string and none of `cc`, `bcc`, `tags` or `scheduled_at` answers the original shape
     * (`to` a string, no `recipients`; the first answer's `status` is `accepted`, and a replay of
     * the same `Idempotency-Key` answers the message as it stands, `delivered`, `bounced` or
     * `failed` once it settled); any other send answers the extended shape, where `to`, `cc`,
     * `bcc`, `reply_to`, `recipients` and `tags` are always arrays and `cost` counts the e-mails.
     * `cc`, `bcc`, `headers` and `tags` are honoured. Any other field is ignored, not refused.
     *
     * Scope `email:send`. Retried after a network failure, a timeout or a 5xx under the same
     * `Idempotency-Key` (minted when you pass none): the server keeps the key through a failure.
     * Early access: the account must be granted it first.
     *
     * Errors: account_not_active, attachment_invalid, attachment_type_blocked,
     * attachments_too_large, content_blocked, daily_cap_reached, email_early_access, email_paused,
     * email_platform_busy, header_not_allowed, idempotency_in_progress, idempotency_key_reused,
     * insufficient_credits, insufficient_scope, internal_error, invalid_destination, invalid_json,
     * invalid_request, ip_not_allowed, key_domain_mismatch, key_expired, live_key_required,
     * message_too_large, no_verified_domain, rate_limited, recipient_suppressed, request_too_large,
     * sender_not_verified, service_not_found, service_required, service_unavailable,
     * template_not_found, template_not_published, template_variable_invalid,
     * template_variable_missing, template_variable_unknown, template_variables_invalid,
     * test_mode_limit_reached, test_mode_recipient, too_many_requests, unauthorized
     * (Tawked\ErrorCode).
     *
     * @example $tawked->email->messages->send(['from' => [...], 'to' => [...], 'subject' => 'طلبك رقم 1042 قيد التجهيز', 'html' => '<p>استلمنا طلبك رقم 1042، وسنرسل رابط التتبع حين يُشحن.</p>', ...]);
     *
     * @param array{from?: array<string, mixed>|string|null, to?: string|list<string>|null, cc?: string|list<string>|null, bcc?: string|list<string>|null, reply_to?: string|list<string>|null, subject: string, html?: string|null, text?: string|null, template?: array<string, mixed>, reference?: string|null, headers?: array<string, mixed>|null, tags?: list<mixed>|null, scheduled_at?: string|\DateTimeInterface|null, attachments?: list<mixed>|null, application?: string}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-send
     * @tawked-stability early_access
     */
    public function send(array $params, array $options = []): EmailSendResponse
    {
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/email/messages',
            options: $options,
            retry: Transport::IDEMPOTENT,
            timeout: 30.0,
            json: self::body($params, ['from', 'template', 'headers']),
            keyMax: 256,
        );

        return EmailSendResponse::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Send a batch of emails. `POST /v1/email/messages/batch`.
     *
     * Sends up to 100 messages in one call, each with the fields of a send (no attachments), and
     * answers `202` with `data`: one message per item, in order, in the extended shape. The body is
     * a JSON array of the messages, or `{ "emails": [...] }`, at most 10 MiB. By default the batch
     * is all or nothing: every item is judged first, and any refusal answers `422 batch_invalid`
     * naming each refused item by its `index`, with nothing sent or charged. With
     * `Tawked-Batch-Validation: permissive` the valid items are sent and the refused ones listed in
     * `errors` beside `data`. An item whose every recipient is suppressed is not a refusal: it is
     * answered `failed` and not charged. The batch counts as one request against the account's rate
     * and is charged in one go; the daily limit and the balance apply to all its e-mails together.
     *
     * Scope `email:send`. Retried after a network failure, a timeout or a 5xx under the same
     * `Idempotency-Key` (minted when you pass none): the server keeps the key through a failure.
     * Early access: the account must be granted it first.
     *
     * Errors: account_not_active, batch_invalid, content_blocked, daily_cap_reached,
     * email_early_access, email_paused, email_platform_busy, header_not_allowed,
     * idempotency_key_reused, insufficient_credits, insufficient_scope, internal_error,
     * invalid_destination, invalid_json, invalid_request, ip_not_allowed, key_domain_mismatch,
     * key_expired, live_key_required, message_too_large, no_verified_domain, rate_limited,
     * request_too_large, sender_not_verified, service_not_found, service_required,
     * service_unavailable, template_not_found, template_not_published, template_variable_invalid,
     * template_variable_missing, template_variable_unknown, template_variables_invalid,
     * test_mode_limit_reached, test_mode_recipient, too_many_requests, unauthorized
     * (Tawked\ErrorCode).
     *
     * @example $tawked->email->messages->sendBatch([...], []);
     *
     * @param array<int, array<string, mixed>> $emails `emails`, one item each (at most 100, refused before sending)
     * @param array{application?: string}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-batch
     * @tawked-stability early_access
     */
    public function sendBatch(array $emails, array $params = [], array $options = []): EmailBatchSendResponse
    {
        self::assertListSize($emails, 'emails', 1, 100);
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/email/messages/batch',
            options: $options,
            retry: Transport::IDEMPOTENT,
            timeout: 120.0,
            query: self::only($params, ['application']),
            json: [...self::without($params, ['application']), 'emails' => array_values($emails)],
            keyMax: 256,
        );

        return EmailBatchSendResponse::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * List emails. `GET /v1/email/messages`.
     *
     * Lists the application's messages, newest first, in the extended shape, a page at a time:
     * follow `next_cursor` while `has_more` is true. Only what Tawked keeps is listed: the last 30
     * days.
     *
     * Scope `email:read`. Retried after a network failure, a timeout or a 5xx (a read). Early
     * access: the account must be granted it first.
     *
     * Errors: account_not_active, insufficient_scope, internal_error, invalid_request,
     * ip_not_allowed, key_expired, live_key_required, not_found, service_not_found,
     * service_required, too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->email->messages->list(['limit' => 20, 'status' => 'delivered']);
     *
     * @param array{limit?: int, cursor?: string, status?: string, to?: string, reference?: string, tag?: string, created_after?: string|\DateTimeInterface, created_before?: string|\DateTimeInterface, application?: string}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-list
     * @tawked-stability early_access
     */
    public function list(array $params = [], array $options = []): EmailMessageListResponse
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/email/messages',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
            query: $params,
        );

        return EmailMessageListResponse::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Every item of `list()`, page after page (following `next_cursor`): the same filters, and `limit` for the page size.
     *
     * @param array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @return \Generator<int, \Tawked\Model\Email\EmailMessageExtended>
     */
    public function iterate(array $params = [], array $options = []): \Generator
    {
        return Paginator::cursor(fn(array $page): EmailMessageListResponse => $this->list($page, $options), $params, 'cursor');
    }

    /**
     * Get an email. `GET /v1/email/messages/{id}`.
     *
     * Reads a message's current status and timestamps. A message sent in the original shape (one
     * `to` string) answers that shape, `error` set when it bounced or failed; any other answers the
     * extended shape, with each recipient's own status, timestamps, bounce and error in
     * `recipients`. A message belongs to the key's application; any other id answers `404`.
     *
     * Scope `email:read`. Retried after a network failure, a timeout or a 5xx (a read). Early
     * access: the account must be granted it first.
     *
     * Errors: account_not_active, insufficient_scope, internal_error, ip_not_allowed, key_expired,
     * live_key_required, not_found, service_not_found, service_required, too_many_requests,
     * unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->email->messages->get('0199f1c2-7a41-7c3e-9d2b-5e8f1a2b3c4d', []);
     *
     * @param string $id `id` in the path
     * @param array{application?: string}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-message
     * @tawked-stability early_access
     */
    public function get(string $id, array $params = [], array $options = []): EmailMessageGetResponse
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/email/messages/'.self::segment($id, 'id'),
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
            query: $params,
        );

        return EmailMessageGetResponse::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Reschedule an email. `PATCH /v1/email/messages/{id}`.
     *
     * Moves a scheduled message to another time, within the next 30 days; a time within the next
     * minute sends it now. Only while it is `scheduled`: once it is on its way the answer is `409
     * message_not_scheduled` with its `status`. The new day's limit applies. Answers the message in
     * the extended shape.
     *
     * Scope `email:send`. Sent once: retried only when the request never left (or the key is still
     * in progress). After a timeout the exception carries the `Idempotency-Key`: call again with
     * it, never a new one. After a 5xx Tawked released the key: check whether it took effect before
     * calling again. Early access: the account must be granted it first.
     *
     * Errors: account_not_active, daily_cap_reached, insufficient_scope, internal_error,
     * invalid_json, invalid_request, ip_not_allowed, key_expired, live_key_required,
     * message_not_scheduled, not_found, rate_limited, service_not_found, service_required,
     * too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->email->messages->update('0199f1c2-7a41-7c3e-9d2b-5e8f1a2b3c4d', ['scheduled_at' => '2026-10-23T09:00:00+03:00']);
     *
     * @param string $id `id` in the path
     * @param array{scheduled_at: string|\DateTimeInterface, application?: string}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-update
     * @tawked-stability early_access
     */
    public function update(string $id, array $params, array $options = []): EmailMessageExtended
    {
        $response = $this->transport->send(
            method: 'PATCH',
            path: '/v1/email/messages/'.self::segment($id, 'id'),
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
            takesKey: false,
        );

        return EmailMessageExtended::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Cancel a scheduled email. `POST /v1/email/messages/{id}/cancel`.
     *
     * Cancels a scheduled message before its time: it becomes `canceled`, its recipients too, and
     * its price is refunded (a free e-mail goes back to this month's allowance). Only while it is
     * `scheduled`: once it is on its way the answer is `409 message_not_scheduled`. Answers the
     * message in the extended shape. No webhook announces a cancel.
     *
     * Scope `email:send`. Sent once: retried only when the request never left (or the key is still
     * in progress). After a timeout the exception carries the `Idempotency-Key`: call again with
     * it, never a new one. After a 5xx Tawked released the key: check whether it took effect before
     * calling again. Early access: the account must be granted it first.
     *
     * Errors: account_not_active, insufficient_scope, internal_error, ip_not_allowed, key_expired,
     * live_key_required, message_not_scheduled, not_found, rate_limited, service_not_found,
     * service_required, too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->email->messages->cancel('0199f1c2-7a41-7c3e-9d2b-5e8f1a2b3c4d', []);
     *
     * @param string $id `id` in the path
     * @param array{application?: string}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-cancel
     * @tawked-stability early_access
     */
    public function cancel(string $id, array $params = [], array $options = []): EmailMessageExtended
    {
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/email/messages/'.self::segment($id, 'id').'/cancel',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
            takesKey: false,
        );

        return EmailMessageExtended::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Get an email's content. `GET /v1/email/messages/{id}/content`.
     *
     * Reads the copy the application kept of a message: its subject, HTML and text as sent (a
     * template's rendered output). Only while the application keeps previews (switched on in the
     * console, under Email → Settings) and for the message's 30 days. A live key with the
     * explicit `email:manage` reads live messages; a test key with `email:send` reads its test
     * messages; a sending key limited to one domain never reads it. Every read is recorded on the
     * account's trail, by the key.
     *
     * Scope `email:manage`. Retried after a network failure, a timeout or a 5xx (a read). Early
     * access: the account must be granted it first.
     *
     * Errors: account_not_active, application_key_required, content_not_kept, insufficient_scope,
     * internal_error, ip_not_allowed, key_expired, live_key_required, not_found, too_many_requests,
     * unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->email->messages->content('0199f1c2-7a41-7c3e-9d2b-5e8f1a2b3c4d');
     *
     * @param string $id `id` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-message-content
     * @tawked-stability early_access
     */
    public function content(string $id, array $options = []): EmailMessageContentResponse
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/email/messages/'.self::segment($id, 'id').'/content',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        );

        return EmailMessageContentResponse::fromArray($response->body)->withMeta($response->meta);
    }
}
