<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Service\Email\Templates;

use Tawked\Http\Transport;
use Tawked\Model\Email\EmailTemplate;
use Tawked\Model\Email\EmailTemplateVersion;
use Tawked\Model\Email\EmailTemplateVersionListResponse;
use Tawked\Service\Service;

/**
 * `$tawked->email->templates->versions`: Tawked Email (early access: an account must be granted Email first).
 */
final class VersionsService extends Service
{
    /**
     * List a template's versions. `GET /v1/email/templates/{id}/versions`.
     *
     * Lists a template's published versions, the newest first, without their HTML and text. A live
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
     * @example $tawked->email->templates->versions->list('0199f1f0-2d3e-7f4a-8b5c-6d7e8f9a0b12');
     *
     * @param string $id `id` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-template-versions
     * @tawked-stability early_access
     */
    public function list(string $id, array $options = []): EmailTemplateVersionListResponse
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/email/templates/'.self::segment($id, 'id').'/versions',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        );

        return EmailTemplateVersionListResponse::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Get a template version. `GET /v1/email/templates/{id}/versions/{number}`.
     *
     * Reads one published version whole, its HTML and text included. A live key of the application
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
     * @example $tawked->email->templates->versions->get('0199f1f0-2d3e-7f4a-8b5c-6d7e8f9a0b12', '2');
     *
     * @param string $id `id` in the path
     * @param string $number `number` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-template-version
     * @tawked-stability early_access
     */
    public function get(string $id, string $number, array $options = []): EmailTemplateVersion
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/email/templates/'.self::segment($id, 'id').'/versions/'.self::segment($number, 'number'),
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        );

        return EmailTemplateVersion::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Restore a template version. `POST /v1/email/templates/{id}/versions/{number}/restore`.
     *
     * Copies a published version into the draft, in place of the draft there was; publish it to
     * send with it again. A live key of the application with the explicit `email:manage`: a
     * full-access key does not hold it.
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
     * @example $tawked->email->templates->versions->restore('0199f1f0-2d3e-7f4a-8b5c-6d7e8f9a0b12', '2');
     *
     * @param string $id `id` in the path
     * @param string $number `number` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint email-template-restore
     * @tawked-stability early_access
     */
    public function restore(string $id, string $number, array $options = []): EmailTemplate
    {
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/email/templates/'.self::segment($id, 'id').'/versions/'.self::segment($number, 'number').'/restore',
            options: $options,
            retry: Transport::IDEMPOTENT,
            timeout: 30.0,
        );

        return EmailTemplate::fromArray($response->body)->withMeta($response->meta);
    }
}
