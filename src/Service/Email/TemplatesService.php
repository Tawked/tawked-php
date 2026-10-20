<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Service\Email;

use Tawked\Http\Transport;
use Tawked\Model\Email\EmailTemplate;
use Tawked\Model\Email\EmailTemplateDeleteResponse;
use Tawked\Model\Email\EmailTemplateListResponse;
use Tawked\Pagination\Paginator;
use Tawked\Service\Email\Templates\VersionsService;
use Tawked\Service\Service;

/**
 * `$tawked->email->templates`: Tawked Email (early access: an account must be granted Email first).
 */
final class TemplatesService extends Service
{
    public readonly VersionsService $versions;

    public function __construct(Transport $transport)
    {
        parent::__construct($transport);
        $this->versions = new VersionsService($transport);
    }

    /**
     * List templates. `GET /v1/email/templates`.
     *
     * Lists the application's hosted templates, newest change first, without their HTML and text. A
     * live key of the application with `email:read` (or `email:send`, or `email:manage`).
     *
     * Scope `email:read`. Retried after a network failure, a timeout or a 5xx (a read). Early
     * access: the account must be granted it first.
     *
     * Errors: account_not_active, application_key_required, email_access_pending,
     * email_access_required, email_product_off, insufficient_scope, internal_error,
     * invalid_request, ip_not_allowed, key_expired, live_key_required, too_many_requests,
     * unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->email->templates->list(['limit' => 20]);
     *
     * @param array{limit?: int, cursor?: string}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-templates
     * @tawked-stability early_access
     */
    public function list(array $params = [], array $options = []): EmailTemplateListResponse
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/email/templates',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
            query: $params,
        );

        return EmailTemplateListResponse::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Every item of `list()`, page after page (following `next_cursor`): the same filters, and `limit` for the page size.
     *
     * @param array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @return \Generator<int, \Tawked\Model\Email\EmailTemplate>
     */
    public function iterate(array $params = [], array $options = []): \Generator
    {
        return Paginator::cursor(fn(array $page): EmailTemplateListResponse => $this->list($page, $options), $params, 'cursor');
    }

    /**
     * Create a template. `POST /v1/email/templates`.
     *
     * Creates a template as a draft, or published at once with `"publish": true`. `{{ KEY }}`
     * places a variable's value, HTML-escaped in `html`; `{{{ KEY }}}` places it as is (HTML only).
     * The variables are taken from the placeholders when not given; given, each placeholder must be
     * declared. At most 100 templates an application (Tawked's limit). A live key of the
     * application with the explicit `email:manage`: a full-access key does not hold it.
     *
     * Scope `email:manage`. Retried after a network failure, a timeout or a 5xx under the same
     * `Idempotency-Key` (minted when you pass none): the server keeps the key through a failure.
     * Early access: the account must be granted it first.
     *
     * Errors: account_not_active, application_key_required, email_access_pending,
     * email_access_required, email_product_off, insufficient_scope, internal_error, invalid_json,
     * invalid_request, ip_not_allowed, key_expired, live_key_required, template_alias_invalid,
     * template_alias_taken, template_invalid, template_limit, too_many_requests, unauthorized
     * (Tawked\ErrorCode).
     *
     * @example $tawked->email->templates->create(['name' => 'تم شحن الطلب', 'alias' => 'order-shipped', 'subject' => 'طلبك {{ ORDER }} في الطريق', 'html' => '<p>مرحبًا {{ NAME }}، طلبك {{ ORDER }} في الطريق.</p>', ...]);
     *
     * @param array{name: string, alias?: string, subject: string, html?: string, text?: string, from?: array<string, mixed>, reply_to?: string|list<string>, variables?: list<mixed>, publish?: bool}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-template-create
     * @tawked-stability early_access
     */
    public function create(array $params, array $options = []): EmailTemplate
    {
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/email/templates',
            options: $options,
            retry: Transport::IDEMPOTENT,
            timeout: 30.0,
            json: self::body($params, ['from']),
        );

        return EmailTemplate::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Get a template. `GET /v1/email/templates/{id}`.
     *
     * Reads a template with its published version and its draft, each with its placeholders and
     * problems. `state` is `draft` (never published), `published` or `published_with_draft`. A live
     * key of the application with `email:read` (or `email:send`, or `email:manage`).
     *
     * Scope `email:read`. Retried after a network failure, a timeout or a 5xx (a read). Early
     * access: the account must be granted it first.
     *
     * Errors: account_not_active, application_key_required, email_access_pending,
     * email_access_required, email_product_off, insufficient_scope, internal_error, ip_not_allowed,
     * key_expired, live_key_required, not_found, too_many_requests, unauthorized
     * (Tawked\ErrorCode).
     *
     * @example $tawked->email->templates->get('0199f1f0-2d3e-7f4a-8b5c-6d7e8f9a0b12');
     *
     * @param string $id `id` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-template
     * @tawked-stability early_access
     */
    public function get(string $id, array $options = []): EmailTemplate
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/email/templates/'.self::segment($id, 'id'),
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        );

        return EmailTemplate::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Update a template. `PATCH /v1/email/templates/{id}`.
     *
     * Changes the template's draft (made from the published version when there is none): sends keep
     * using the published version until the draft is published. Every field is optional. A live key
     * of the application with the explicit `email:manage`: a full-access key does not hold it.
     *
     * Scope `email:manage`. Retried after a network failure, a timeout or a 5xx under the same
     * `Idempotency-Key` (minted when you pass none): the server keeps the key through a failure.
     * Early access: the account must be granted it first.
     *
     * Errors: account_not_active, application_key_required, email_access_pending,
     * email_access_required, email_product_off, insufficient_scope, internal_error, invalid_json,
     * invalid_request, ip_not_allowed, key_expired, live_key_required, not_found,
     * template_alias_invalid, template_alias_taken, too_many_requests, unauthorized
     * (Tawked\ErrorCode).
     *
     * @example $tawked->email->templates->update('0199f1f0-2d3e-7f4a-8b5c-6d7e8f9a0b12', ['subject' => 'طلبك {{ ORDER }} شُحن']);
     *
     * @param string $id `id` in the path
     * @param array{name?: string, alias?: string, subject?: string, html?: string, text?: string, from?: array<string, mixed>, reply_to?: string|list<string>, variables?: list<mixed>, publish?: bool}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-template-update
     * @tawked-stability early_access
     */
    public function update(string $id, array $params = [], array $options = []): EmailTemplate
    {
        $response = $this->transport->send(
            method: 'PATCH',
            path: '/v1/email/templates/'.self::segment($id, 'id'),
            options: $options,
            retry: Transport::IDEMPOTENT,
            timeout: 30.0,
            json: self::body($params, ['from']),
        );

        return EmailTemplate::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Delete a template. `DELETE /v1/email/templates/{id}`.
     *
     * Deletes a template: its alias is free again, and a send that names it answers `422
     * template_not_found`. Messages already sent keep their copy. A live key of the application
     * with the explicit `email:manage`: a full-access key does not hold it.
     *
     * Scope `email:manage`. Retried after a network failure, a timeout or a 5xx under the same
     * `Idempotency-Key` (minted when you pass none): the server keeps the key through a failure.
     * Early access: the account must be granted it first.
     *
     * Errors: account_not_active, application_key_required, email_access_pending,
     * email_access_required, email_product_off, insufficient_scope, internal_error, invalid_json,
     * invalid_request, ip_not_allowed, key_expired, live_key_required, not_found,
     * too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->email->templates->delete('0199f1f0-2d3e-7f4a-8b5c-6d7e8f9a0b12');
     *
     * @param string $id `id` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-template-delete
     * @tawked-stability early_access
     */
    public function delete(string $id, array $options = []): EmailTemplateDeleteResponse
    {
        $response = $this->transport->send(
            method: 'DELETE',
            path: '/v1/email/templates/'.self::segment($id, 'id'),
            options: $options,
            retry: Transport::IDEMPOTENT,
            timeout: 30.0,
        );

        return EmailTemplateDeleteResponse::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Publish a template. `POST /v1/email/templates/{id}/publish`.
     *
     * Publishes the draft as the next numbered version: every send by this template from now uses
     * it. Refused with `422 template_invalid` and its `problems` while one is an error (an
     * undeclared placeholder, no subject, no content, HTML over 512 KiB, a from address off the
     * application's domains); `409 template_no_draft` when there is nothing to publish. A live key
     * of the application with the explicit `email:manage`: a full-access key does not hold it.
     *
     * Scope `email:manage`. Retried after a network failure, a timeout or a 5xx under the same
     * `Idempotency-Key` (minted when you pass none): the server keeps the key through a failure.
     * Early access: the account must be granted it first.
     *
     * Errors: account_not_active, application_key_required, email_access_pending,
     * email_access_required, email_product_off, insufficient_scope, internal_error, invalid_json,
     * invalid_request, ip_not_allowed, key_expired, live_key_required, not_found, template_invalid,
     * template_no_draft, too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->email->templates->publish('0199f1f0-2d3e-7f4a-8b5c-6d7e8f9a0b12');
     *
     * @param string $id `id` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-template-publish
     * @tawked-stability early_access
     */
    public function publish(string $id, array $options = []): EmailTemplate
    {
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/email/templates/'.self::segment($id, 'id').'/publish',
            options: $options,
            retry: Transport::IDEMPOTENT,
            timeout: 30.0,
        );

        return EmailTemplate::fromArray($response->body)->withMeta($response->meta);
    }
}
