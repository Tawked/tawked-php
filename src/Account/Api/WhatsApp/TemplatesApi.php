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
 * `$account->whatsapp->templates`: the Account API's `whatsapp.templates` operations.
 */
final class TemplatesApi extends AccountNamespace
{
    /**
     * Check a proposed template with whatsapp.templates.create's own rules before creating it,
     * saving nothing: answers `ready` (no error blocks it) and every problem once (`level` error or
     * warning, `field`, `code`, and `problem`: what is wrong in words). The body is exactly
     * whatsapp.templates.create's (any category, an IMAGE header with its `header_sample`,
     * AUTHENTICATION with its fields); then call whatsapp.templates.create with it. `POST
     * /v1/applications/{application}/whatsapp/templates/check`.
     *
     * Permission `whatsapp:write`. Sent once: retried only when the request never left (or the key
     * is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * @param string $application `application` in the path
     * @param array{autofill_text?: string|null, body?: string, buttons?: list<mixed>, category: string, code_expiry_minutes?: int, examples?: array<string, mixed>, footer?: string|null, header?: string|null, header_format?: string|null, header_sample?: string|null, language: string, message_send_ttl_seconds?: int|null, name: string, otp_type?: string, package_name?: string, security_recommendation?: bool, signature_hash?: string, zero_tap_terms_accepted?: bool}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation whatsapp.templates.check
     */
    public function check(string $application, array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/whatsapp/templates/check',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, ['examples']),
        ));
    }

    /**
     * Create a WhatsApp template and submit it to Meta for review; answers its id and status
     * (PENDING until Meta decides). Then poll whatsapp.templates.show with the id until the status
     * is APPROVED or REJECTED. `POST /v1/applications/{application}/whatsapp/templates`.
     *
     * Permission `whatsapp:write`. Sent once: retried only when the request never left (or the key
     * is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * @param string $application `application` in the path
     * @param array{autofill_text?: string|null, body?: string, buttons?: list<mixed>, category: string, code_expiry_minutes?: int, examples?: array<string, mixed>, footer?: string|null, header?: string|null, header_format?: string|null, header_sample?: string|null, language: string, message_send_ttl_seconds?: int|null, name: string, otp_type?: string, package_name?: string, security_recommendation?: bool, signature_hash?: string, zero_tap_terms_accepted?: bool}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation whatsapp.templates.create
     */
    public function create(string $application, array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/whatsapp/templates',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, ['examples']),
        ));
    }

    /**
     * Delete one language of a template on Meta; answers ok with its id, name and language. It
     * stops for every application on the same WhatsApp Business Account: read
     * whatsapp.templates.show's `used_by` first. `DELETE
     * /v1/applications/{application}/whatsapp/templates/{template}`.
     *
     * Permission `whatsapp:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Sent once: retried only
     * when the request never left (or the key is still in progress). After a timeout the exception
     * carries the `Idempotency-Key`: call again with it, never a new one. After a 5xx Tawked
     * released the key: check whether it took effect before calling again.
     *
     * @param string $application `application` in the path
     * @param string $template `template` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation whatsapp.templates.delete
     */
    public function delete(string $application, string $template, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'DELETE',
            path: '/v1/applications/'.self::segment($application, 'application').'/whatsapp/templates/'.self::segment($template, 'template'),
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * List the WhatsApp templates on the application's number as Meta holds them, each with its id,
     * status (APPROVED, PENDING, REJECTED…), quality, rejection reason and whether it can be
     * sent; filter by status, category, language or a name (`q`). `GET
     * /v1/applications/{application}/whatsapp/templates`.
     *
     * Permission `whatsapp:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param array{category?: string, language?: string, page?: int, q?: string, status?: string}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation whatsapp.templates.list
     */
    public function list(string $application, array $params = [], array $options = []): AccountPage
    {
        return AccountPage::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/whatsapp/templates',
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
     * Drop Tawked's cached copy of the number's templates; answers ok. The next
     * whatsapp.templates.list or whatsapp.templates.show reads them from Meta afresh. `POST
     * /v1/applications/{application}/whatsapp/templates/refresh`.
     *
     * Permission `whatsapp:write`. Sent once: retried only when the request never left (or the key
     * is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * @param string $application `application` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation whatsapp.templates.refresh
     */
    public function refresh(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/whatsapp/templates/refresh',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * One WhatsApp template by its Meta id: Meta's status (poll this after a create until APPROVED
     * or REJECTED), quality, rejection reason, whether it can be sent and edited, its parts, what
     * sends it today (campaigns, automations), and `next`: what to do in its status. `GET
     * /v1/applications/{application}/whatsapp/templates/{template}`.
     *
     * Permission `whatsapp:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param string $template `template` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation whatsapp.templates.show
     */
    public function show(string $application, string $template, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/whatsapp/templates/'.self::segment($template, 'template'),
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }

    /**
     * Submit Tawked's starter kit (order and shipping updates in Arabic and English) to Meta,
     * skipping names the number already holds; answers how many were created and which failed with
     * Meta's words. Then poll whatsapp.templates.list until Meta approves them. `POST
     * /v1/applications/{application}/whatsapp/templates/starter-kit`.
     *
     * Permission `whatsapp:write`. Sent once: retried only when the request never left (or the key
     * is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * @param string $application `application` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation whatsapp.templates.starter_kit
     */
    public function starterKit(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/whatsapp/templates/starter-kit',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * Resume a template Meta paused for low quality; answers ok, or Meta's refusal when its pause
     * has not ended. `POST /v1/applications/{application}/whatsapp/templates/{template}/unpause`.
     *
     * Permission `whatsapp:write`. Sent once: retried only when the request never left (or the key
     * is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * @param string $application `application` in the path
     * @param string $template `template` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation whatsapp.templates.unpause
     */
    public function unpause(string $application, string $template, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/whatsapp/templates/'.self::segment($template, 'template').'/unpause',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * Edit a template's content on Meta (its name and language stay); Meta reviews it again.
     * Answers the template's id and status; poll whatsapp.templates.show for Meta's decision.
     * `PATCH /v1/applications/{application}/whatsapp/templates/{template}`.
     *
     * Permission `whatsapp:write`. Sent once: retried only when the request never left (or the key
     * is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * @param string $application `application` in the path
     * @param string $template `template` in the path
     * @param array{autofill_text?: string|null, body?: string, buttons?: list<mixed>, category: string, code_expiry_minutes?: int, examples?: array<string, mixed>, footer?: string|null, header?: string|null, header_format?: string|null, header_sample?: string|null, language: string, message_send_ttl_seconds?: int|null, name: string, otp_type?: string, package_name?: string, security_recommendation?: bool, signature_hash?: string, zero_tap_terms_accepted?: bool}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation whatsapp.templates.update
     */
    public function update(string $application, string $template, array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'PATCH',
            path: '/v1/applications/'.self::segment($application, 'application').'/whatsapp/templates/'.self::segment($template, 'template'),
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, ['examples']),
        ));
    }
}
