<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\WhatsApp;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->whatsapp->firstMessage`: the Account API's `whatsapp.first_message` operations.
 */
final class FirstMessageApi extends AccountNamespace
{
    /**
     * Make a live application key for the sending API (named "First message"); answers the key's
     * plaintext once, and its prefix. `POST
     * /v1/applications/{application}/whatsapp/first-message/key`.
     *
     * Permission `whatsapp:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Never retried: its
     * answer holds a secret shown once (120 s timeout). After a failure, find what it made by its
     * id, then rotate or revoke it.
     *
     * @param string $application `application` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation whatsapp.first_message.create_key
     */
    public function createKey(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/whatsapp/first-message/key',
            options: $options,
            retry: Transport::NEVER,
            timeout: 120.0,
            secret: true,
        ));
    }

    /**
     * Send the guide's template to the token's person's own phone, as the application (Meta bills
     * it); answers the message with its status. Poll whatsapp.first_message.show for delivery.
     * `POST /v1/applications/{application}/whatsapp/first-message/test`.
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
     * @tawked-operation whatsapp.first_message.send_test
     */
    public function sendTest(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/whatsapp/first-message/test',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * Where the number stands on the way to its first message: the phase (setting up, payment,
     * blocked, ready…), the template a test sends and the request that sends it from your
     * servers, and the last test with its delivery status. `GET
     * /v1/applications/{application}/whatsapp/first-message`.
     *
     * Permission `whatsapp:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation whatsapp.first_message.show
     */
    public function show(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/whatsapp/first-message',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }
}
