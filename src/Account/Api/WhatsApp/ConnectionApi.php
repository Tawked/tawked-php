<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\WhatsApp;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->whatsapp->connection`: the Account API's `whatsapp.connection` operations.
 */
final class ConnectionApi extends AccountNamespace
{
    /**
     * Disconnect the application's WhatsApp number from Tawked: its Tawked Chat inbox goes, sending
     * stops until a number is connected again (whatsapp.connection.link). Answers ok. `DELETE
     * /v1/applications/{application}/whatsapp/connection`.
     *
     * Permission `whatsapp:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Sent once: retried only
     * when the request never left (or the key is still in progress). After a timeout the exception
     * carries the `Idempotency-Key`: call again with it, never a new one. After a 5xx Tawked
     * released the key: check whether it took effect before calling again.
     *
     * @param string $application `application` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation whatsapp.connection.delete
     */
    public function delete(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'DELETE',
            path: '/v1/applications/'.self::segment($application, 'application').'/whatsapp/connection',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * Where the person connects the application's WhatsApp number (the console address that opens
     * Meta's window), and how far it got: connected or not, the stage, the person's signup in
     * flight. `GET /v1/applications/{application}/whatsapp/connection`.
     *
     * Permission `whatsapp:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation whatsapp.connection.link
     */
    public function link(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/whatsapp/connection',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }
}
