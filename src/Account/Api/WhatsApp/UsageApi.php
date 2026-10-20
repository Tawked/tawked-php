<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\WhatsApp;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->whatsapp->usage`: the Account API's `whatsapp.usage` operations.
 */
final class UsageApi extends AccountNamespace
{
    /**
     * The application's WhatsApp template messages for a `period` (today, 7d, month, 30d; Riyadh
     * time) against the same span before: sent, delivered, read, failed, in flight, the delivered
     * and read rates, and Tawked's fees charged (`charged_halalas`, for who sees the money; Meta
     * bills its own charges separately). `GET /v1/applications/{application}/whatsapp/usage`.
     *
     * Permission `whatsapp:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param array{period?: string|null}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation whatsapp.usage.show
     */
    public function show(string $application, array $params = [], array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/whatsapp/usage',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
            query: $params,
        ));
    }
}
