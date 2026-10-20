<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Email;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->email->keys`: the Account API's `email.keys` operations.
 */
final class KeysApi extends AccountNamespace
{
    /**
     * Make an application key for Tawked Email: `access` `send` (default: `email:send`, send e-mail
     * and read what it sent) or `manage` (`email:manage`: also the domains, the suppression list
     * and the templates on `/v1`), optionally limited to one domain (`domain_id`, sending keys
     * only), `mode` `live` (default) or `test` (sends only to the simulator addresses, nothing
     * delivered, nothing charged); answers the key once, with its id and prefix. `POST
     * /v1/applications/{application}/email/keys`.
     *
     * Permission `email:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Never retried: its
     * answer holds a secret shown once (120 s timeout). After a failure, find what it made by its
     * id, then rotate or revoke it.
     *
     * @param string $application `application` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation email.keys.create
     */
    public function create(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/email/keys',
            options: $options,
            retry: Transport::NEVER,
            timeout: 120.0,
            secret: true,
        ));
    }
}
