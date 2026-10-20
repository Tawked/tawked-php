<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Service\Email\Inbound;

use Tawked\Http\Transport;
use Tawked\Service\Service;

/**
 * `$tawked->email->inbound->attachments`: Tawked Email (early access: an account must be granted Email first).
 */
final class AttachmentsService extends Service
{
    /**
     * Download a received attachment. `GET /v1/email/received/{id}/attachments/{attachment}`.
     *
     * Answers one attachment's bytes as a download, with its content type; an attachment that could
     * run a program (`risky`) comes as `application/octet-stream`.
     *
     * Scope `email:inbound`. Retried after a network failure, a timeout or a 5xx (a read). Early
     * access: the account must be granted it first.
     *
     * Errors: account_not_active, insufficient_scope, internal_error, ip_not_allowed, key_expired,
     * live_key_required, not_found, received_content_gone, too_many_requests, unauthorized
     * (Tawked\ErrorCode).
     *
     * @example $tawked->email->inbound->attachments->get('0199f240-7b8c-7d9e-8f0a-1b2c3d4e5f60', '0199f241-8c9d-7e0f-9a1b-2c3d4e5f6071');
     *
     * @param string $id `id` in the path
     * @param string $attachment `attachment` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     * @return string the bytes as they were stored (never parsed)
     *
     * @tawked-endpoint email-received-attachment
     * @tawked-stability early_access
     */
    public function get(string $id, string $attachment, array $options = []): string
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/email/received/'.self::segment($id, 'id').'/attachments/'.self::segment($attachment, 'attachment'),
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
            binary: true,
        );

        return $response->raw ?? '';
    }
}
