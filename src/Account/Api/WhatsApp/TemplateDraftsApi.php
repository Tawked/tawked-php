<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\WhatsApp;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountPage;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;
use Tawked\Pagination\Paginator;

/**
 * `$account->whatsapp->templateDrafts`: the Account API's `whatsapp.template_drafts` operations.
 */
final class TemplateDraftsApi extends AccountNamespace
{
    /**
     * One turn of Tawked's template assistant on a draft: send your message (or `retry: true`) with
     * the draft's state and revision; it writes into the draft and answers what it said (`turns`),
     * the draft and its lint. `POST
     * /v1/applications/{application}/whatsapp/template-drafts/{draft}/assistant`.
     *
     * Permission `whatsapp:write`. Sent once: retried only when the request never left (or the key
     * is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * @param string $application `application` in the path
     * @param string $draft `draft` in the path
     * @param array{draft: array<string, mixed>, message: string, retry?: bool, revision: int}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation whatsapp.template_drafts.assistant
     */
    public function assistant(string $application, string $draft, array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/whatsapp/template-drafts/'.self::segment($draft, 'draft').'/assistant',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, ['draft']),
        ));
    }

    /**
     * Start a template draft: blank (`source: blank`), for the template assistant (`source:
     * assistant`), or a copy of a template on the number by its name (`duplicate`). Answers the
     * draft with its `revision`. `POST /v1/applications/{application}/whatsapp/template-drafts`.
     *
     * Permission `whatsapp:write`. Sent once: retried only when the request never left (or the key
     * is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * @param string $application `application` in the path
     * @param array{duplicate?: string, source?: string}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation whatsapp.template_drafts.create
     */
    public function create(string $application, array $params = [], array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/whatsapp/template-drafts',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
        ));
    }

    /**
     * Delete a template draft (only Tawked's working copy; nothing on Meta changes); answers ok.
     * `DELETE /v1/applications/{application}/whatsapp/template-drafts/{draft}`.
     *
     * Permission `whatsapp:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Sent once: retried only
     * when the request never left (or the key is still in progress). After a timeout the exception
     * carries the `Idempotency-Key`: call again with it, never a new one. After a 5xx Tawked
     * released the key: check whether it took effect before calling again.
     *
     * @param string $application `application` in the path
     * @param string $draft `draft` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation whatsapp.template_drafts.delete
     */
    public function delete(string $application, string $draft, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'DELETE',
            path: '/v1/applications/'.self::segment($application, 'application').'/whatsapp/template-drafts/'.self::segment($draft, 'draft'),
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * List the application's template drafts (not yet submitted to Meta), newest change first, each
     * with its languages and variants; filter by name (`q`). `GET
     * /v1/applications/{application}/whatsapp/template-drafts`.
     *
     * Permission `whatsapp:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param array{page?: int, q?: string}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation whatsapp.template_drafts.list
     */
    public function list(string $application, array $params = [], array $options = []): AccountPage
    {
        return AccountPage::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/whatsapp/template-drafts',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
            query: $params,
        ));
    }

    /**
     * Every item of `list()`, page after page (following `next_cursor`): the same filters, and `limit` for the page size.
     *
     * @param array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @return \Generator<int, mixed>
     */
    public function iterate(string $application, array $params = [], array $options = []): \Generator
    {
        return Paginator::cursor(fn(array $page): AccountPage => $this->list($application, $page, $options), $params, 'cursor');
    }

    /**
     * One template draft in full: its variants, `revision` (send it back on update and submit), and
     * `lint`, the checks it must pass before Meta sees it (an `error` blocks submit). `GET
     * /v1/applications/{application}/whatsapp/template-drafts/{draft}`.
     *
     * Permission `whatsapp:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param string $draft `draft` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation whatsapp.template_drafts.show
     */
    public function show(string $application, string $draft, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/whatsapp/template-drafts/'.self::segment($draft, 'draft'),
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }

    /**
     * Save the draft (same body as update) and submit it to Meta, one template per language;
     * answers the templates created (each id and status) and the languages Meta refused, with its
     * words. Then poll whatsapp.templates.show per id. `POST
     * /v1/applications/{application}/whatsapp/template-drafts/{draft}/submit`.
     *
     * Permission `whatsapp:write`. Sent once: retried only when the request never left (or the key
     * is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * @param string $application `application` in the path
     * @param string $draft `draft` in the path
     * @param array{autofill_text?: string|null, category?: string|null, code_expiry_minutes?: int|null, message_send_ttl_seconds?: int|null, name?: string|null, otp_type?: string|null, package_name?: string|null, revision: int, security_recommendation?: bool|null, signature_hash?: string|null, variants?: list<mixed>, zero_tap_terms_accepted?: bool|null}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation whatsapp.template_drafts.submit
     */
    public function submit(string $application, string $draft, array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/whatsapp/template-drafts/'.self::segment($draft, 'draft').'/submit',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
        ));
    }

    /**
     * Save a draft's whole state (name, category, variants per language with header, body, footer,
     * buttons, examples) under the `revision` last read; answers the draft with its new revision
     * and its `lint`; once no `lint` error is left, send it to Meta with
     * whatsapp.template_drafts.submit. `PUT
     * /v1/applications/{application}/whatsapp/template-drafts/{draft}`.
     *
     * Permission `whatsapp:write`. Sent once: retried only when the request never left (or the key
     * is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * @param string $application `application` in the path
     * @param string $draft `draft` in the path
     * @param array{autofill_text?: string|null, category?: string|null, code_expiry_minutes?: int|null, message_send_ttl_seconds?: int|null, name?: string|null, otp_type?: string|null, package_name?: string|null, revision: int, security_recommendation?: bool|null, signature_hash?: string|null, variants?: list<mixed>, zero_tap_terms_accepted?: bool|null}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation whatsapp.template_drafts.update
     */
    public function update(string $application, string $draft, array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'PUT',
            path: '/v1/applications/'.self::segment($application, 'application').'/whatsapp/template-drafts/'.self::segment($draft, 'draft'),
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
        ));
    }
}
