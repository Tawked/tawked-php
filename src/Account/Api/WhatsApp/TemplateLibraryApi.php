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
 * `$account->whatsapp->templateLibrary`: the Account API's `whatsapp.template_library` operations.
 */
final class TemplateLibraryApi extends AccountNamespace
{
    /**
     * Create a template from Meta's library: the library row's name and language, the new
     * template's name, one input per library button in its order (a link's base, a phone number).
     * Answers the new template's id, status and `next` (poll whatsapp.templates.show while it is
     * PENDING). `POST /v1/applications/{application}/whatsapp/template-library`.
     *
     * Permission `whatsapp:write`. Sent once: retried only when the request never left (or the key
     * is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * @param string $application `application` in the path
     * @param array{buttons?: list<mixed>, language: string, library_id?: string|null, library_name: string, name: string}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation whatsapp.template_library.create
     */
    public function create(string $application, array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/whatsapp/template-library',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
        ));
    }

    /**
     * Browse Meta's template library (ready-made utility templates Meta usually approves at once),
     * filtered by topic, use case, industry, language or words (`q`); each with the name and
     * buttons whatsapp.template_library.create takes. `GET
     * /v1/applications/{application}/whatsapp/template-library`.
     *
     * Permission `whatsapp:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param array{industry?: string, language?: string, page?: int, q?: string, topic?: string, usecase?: string}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation whatsapp.template_library.list
     */
    public function list(string $application, array $params = [], array $options = []): AccountPage
    {
        return AccountPage::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/whatsapp/template-library',
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
}
