<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Service\Email\Suppressions;

use Tawked\Http\Transport;
use Tawked\Model\Email\EmailSuppressionImport;
use Tawked\Service\Service;

/**
 * `$tawked->email->suppressions->imports`: Tawked Email (early access: an account must be granted Email first).
 */
final class ImportsService extends Service
{
    /**
     * Import suppressed addresses. `POST /v1/email/suppressions/imports`.
     *
     * Adds the addresses of a CSV file (one address a line, or a column named `email`) to the
     * account's list in the background. `202` with the import; read its progress with `GET
     * /v1/email/suppressions/imports/{id}`. One import runs at a time for an application, at most
     * ten a day (Tawked's limits). A live key of the application with the explicit `email:manage`:
     * a full-access key does not hold it.
     *
     * Scope `email:manage`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again. Early access: the account must be granted it first.
     *
     * Errors: account_not_active, application_key_required, email_access_pending,
     * email_access_required, email_product_off, insufficient_scope, internal_error, invalid_json,
     * invalid_request, ip_not_allowed, key_expired, live_key_required, suppression_import_daily,
     * suppression_import_invalid_file, suppression_import_running, suppression_import_too_large,
     * too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->email->suppressions->imports->create([]);
     *
     * @param array{file: \Tawked\Attachment|\SplFileInfo|\Psr\Http\Message\StreamInterface|resource|string}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-suppressions-import
     * @tawked-stability early_access
     */
    public function create(array $params, array $options = []): EmailSuppressionImport
    {
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/email/suppressions/imports',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            multipart: $params,
            files: ['file'],
            takesKey: false,
        );

        return EmailSuppressionImport::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Get an import. `GET /v1/email/suppressions/imports/{id}`.
     *
     * Reads an import's progress: `queued`, `running`, `done` or `failed`, with how many rows were
     * added, already there or invalid, and the first invalid lines. A live key of the application
     * with `email:read` (or `email:send`, or `email:manage`).
     *
     * Scope `email:read`. Retried after a network failure, a timeout or a 5xx (a read). Early
     * access: the account must be granted it first.
     *
     * Errors: account_not_active, application_key_required, email_access_pending,
     * email_access_required, email_product_off, insufficient_scope, internal_error, ip_not_allowed,
     * key_expired, live_key_required, not_found, too_many_requests, unauthorized
     * (Tawked\ErrorCode).
     *
     * @example $tawked->email->suppressions->imports->get('0199f1e0-2c3d-7e4f-9a5b-6c7d8e9f0a12');
     *
     * @param string $id `id` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-suppressions-import-status
     * @tawked-stability early_access
     */
    public function get(string $id, array $options = []): EmailSuppressionImport
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/email/suppressions/imports/'.self::segment($id, 'id'),
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        );

        return EmailSuppressionImport::fromArray($response->body)->withMeta($response->meta);
    }
}
