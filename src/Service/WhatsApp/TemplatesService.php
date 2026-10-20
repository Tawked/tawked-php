<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Service\WhatsApp;

use Tawked\Http\Transport;
use Tawked\Model\WhatsApp\CreatedTemplate;
use Tawked\Model\WhatsApp\DeletedTemplate;
use Tawked\Model\WhatsApp\Template;
use Tawked\Model\WhatsApp\TemplateList;
use Tawked\Service\Service;

/**
 * `$tawked->whatsapp->templates`: WhatsApp template messages and templates (Tawked Notifications).
 */
final class TemplatesService extends Service
{
    /**
     * List templates. `GET /v1/whatsapp/templates`.
     *
     * Lists the templates of the number's WhatsApp Business Account as Meta holds them, with the
     * placeholders each one expects, so your code can discover what to send without WhatsApp
     * Manager. Each row carries Meta's `id`, which the template operations take, and `managed`:
     * true only when the calling key is a connected app's and that app created the template through
     * this application. Cached for 5 minutes.
     *
     * Scope `notifications:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * Errors: account_not_active, insufficient_scope, internal_error, ip_not_allowed, key_expired,
     * live_key_required, no_whatsapp_number, service_not_found, service_required,
     * templates_unavailable, too_many_requests, unauthorized, whatsapp_not_enabled
     * (Tawked\ErrorCode).
     *
     * @example $tawked->whatsapp->templates->list([]);
     *
     * @param array{application?: string}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint whatsapp-templates
     */
    public function list(array $params = [], array $options = []): TemplateList
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/whatsapp/templates',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
            query: $params,
        );

        return TemplateList::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Get a template. `GET /v1/whatsapp/templates/{template_id}`.
     *
     * Reads one template by its Meta `id`, in full: its status, quality and rejection reason, its
     * components as Meta holds them, the sample values, the placeholders a send must fill, and
     * `managed`. An id that is not on the number's WhatsApp Business Account answers `404`.
     *
     * Scope `notifications:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * Errors: account_not_active, insufficient_scope, internal_error, ip_not_allowed, key_expired,
     * live_key_required, no_whatsapp_number, not_found, templates_unavailable, too_many_requests,
     * unauthorized, whatsapp_not_enabled (Tawked\ErrorCode).
     *
     * @example $tawked->whatsapp->templates->get('7000');
     *
     * @param string $templateId `template_id` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint whatsapp-template
     */
    public function get(string $templateId, array $options = []): Template
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/whatsapp/templates/'.self::segment($templateId, 'template_id'),
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        );

        return Template::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Create a template. `POST /v1/whatsapp/templates`.
     *
     * For a connected app's key alone: an application's own key, full access included, answers `403
     * insufficient_scope`, and writes its templates in the console. Submits a template to Meta's
     * review under the rules of the console's editor, and answers Meta's `id`, the category as Meta
     * filed it and the `status` (`PENDING` at first, usually). The template opens with a text
     * header or none, or, with `header_format` and `header_sample_url`, with an image or a PDF
     * document; the example is an invoice sent when a customer pays. Any field outside the ones
     * below is ignored. A refused field answers `400 invalid_request` with `fields` naming it and
     * `errors` giving each field's reasons; what Meta refuses after that answers `422
     * template_refused` with Meta's own sentence.
     *
     * Scope `notifications:templates`. Sent once: retried only when the request never left (or the
     * key is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * Errors: account_not_active, idempotency_in_progress, idempotency_key_reused,
     * insufficient_scope, internal_error, invalid_json, invalid_request, ip_not_allowed,
     * key_expired, live_key_required, no_whatsapp_number, template_category_not_allowed,
     * template_refused, templates_unavailable, too_many_requests, unauthorized,
     * whatsapp_not_enabled (Tawked\ErrorCode).
     *
     * @example $tawked->whatsapp->templates->create(['name' => 'order_shipped_v1', 'language' => 'ar', 'category' => 'UTILITY', 'body' => 'شُحن طلبك رقم {{1}}، ويصلك خلال {{2}} أيام.', ...]);
     *
     * @param array{name: string, language: string, category: string, header?: string|null, body: string, footer?: string|null, buttons?: list<mixed>, examples?: array<string, mixed>}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint whatsapp-template-create
     */
    public function create(array $params, array $options = []): CreatedTemplate
    {
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/whatsapp/templates',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, ['examples']),
        );

        return CreatedTemplate::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Update a template. `PATCH /v1/whatsapp/templates/{template_id}`.
     *
     * For a connected app's key alone: an application's own key, full access included, answers `403
     * insufficient_scope`, and writes its templates in the console. Replaces a template's content
     * on Meta. Send the whole new content: the body of a creation without `name` and `language`,
     * which Meta fixed at creation and which are ignored here. A template that opens with an image
     * or a document is edited like a text one, `header_format` included (without it the edit
     * answers `409 template_not_editable` with `MEDIA_HEADER`, so an edit written for text never
     * drops the file), and without `header_sample_url` the sample Meta holds is kept. Meta reviews
     * the change again. Only an `APPROVED`, `REJECTED` or `PAUSED` template can be edited; anything
     * else answers `409 template_not_editable` with a `status`: the template's own, `META_SAMPLE`
     * for one of Meta's samples, or `MEDIA_HEADER` for a template that opens with a video or a
     * location, which is edited in the console, or with an image or a document when the edit leaves
     * out `header_format`. An authentication template answers `422 template_category_not_allowed`.
     * Answers the template as it stands after the edit.
     *
     * Scope `notifications:templates`. Sent once: retried only when the request never left (or the
     * key is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * Errors: account_not_active, idempotency_in_progress, idempotency_key_reused,
     * insufficient_scope, internal_error, invalid_json, invalid_request, ip_not_allowed,
     * key_expired, live_key_required, no_whatsapp_number, not_found, template_category_not_allowed,
     * template_in_use, template_not_editable, template_not_managed, template_refused,
     * templates_unavailable, too_many_requests, unauthorized, whatsapp_not_enabled
     * (Tawked\ErrorCode).
     *
     * @example $tawked->whatsapp->templates->update('7000', ['category' => 'UTILITY', 'body' => 'استلمنا طلبك رقم {{1}} بقيمة {{2}} ريال.', 'examples' => [...]]);
     *
     * @param string $templateId `template_id` in the path
     * @param array{category: string, header?: string|null, body: string, footer?: string|null, buttons?: list<mixed>, examples?: array<string, mixed>}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint whatsapp-template-update
     */
    public function update(string $templateId, array $params, array $options = []): Template
    {
        $response = $this->transport->send(
            method: 'PATCH',
            path: '/v1/whatsapp/templates/'.self::segment($templateId, 'template_id'),
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, ['examples']),
        );

        return Template::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Delete a template. `DELETE /v1/whatsapp/templates/{template_id}`.
     *
     * For a connected app's key alone: an application's own key, full access included, answers `403
     * insufficient_scope`, and writes its templates in the console. Deletes that one language of
     * the template on Meta. Refused while something still sends it: `409 template_in_use`, with
     * `used_by` saying what. Meta's own samples cannot be deleted (`409 template_not_editable` with
     * `status` `META_SAMPLE`).
     *
     * Scope `notifications:templates`. Sent once: retried only when the request never left (or the
     * key is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * Errors: account_not_active, idempotency_in_progress, idempotency_key_reused,
     * insufficient_scope, internal_error, invalid_request, ip_not_allowed, key_expired,
     * live_key_required, no_whatsapp_number, not_found, template_in_use, template_not_editable,
     * template_not_managed, template_refused, templates_unavailable, too_many_requests,
     * unauthorized, whatsapp_not_enabled (Tawked\ErrorCode).
     *
     * @example $tawked->whatsapp->templates->delete('7000');
     *
     * @param string $templateId `template_id` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint whatsapp-template-delete
     */
    public function delete(string $templateId, array $options = []): DeletedTemplate
    {
        $response = $this->transport->send(
            method: 'DELETE',
            path: '/v1/whatsapp/templates/'.self::segment($templateId, 'template_id'),
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        );

        return DeletedTemplate::fromArray($response->body)->withMeta($response->meta);
    }
}
