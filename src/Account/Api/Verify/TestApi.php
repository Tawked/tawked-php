<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Verify;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->verify->test`: the Account API's `verify.test` operations.
 */
final class TestApi extends AccountNamespace
{
    /**
     * Check the code the owner's phone received for a test verification (`id` from
     * verify.test.send, `code`), as the public API would (POST /v1/verify/check); answers the API's
     * `response` (its `status`: verified or not). `POST
     * /v1/applications/{application}/verify/test/check`.
     *
     * Permission `verify:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $application `application` in the path
     * @param array{code: string, id: string}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation verify.test.check
     */
    public function check(string $application, array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/verify/test/check',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
        ));
    }

    /**
     * Send a test code to the account owner's own phone through the application's test key, as the
     * public API would (POST /v1/verify/start); answers the API's `response`, the key used (its
     * `plaintext` only when this call created it) and `seconds_left`. Then call verify.test.check.
     * `POST /v1/applications/{application}/verify/test/send`.
     *
     * Permission `verify:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Never retried: its
     * answer holds a secret shown once (120 s timeout). After a failure, find what it made by its
     * id, then rotate or revoke it.
     *
     * @param string $application `application` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation verify.test.send
     */
    public function send(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/verify/test/send',
            options: $options,
            retry: Transport::NEVER,
            timeout: 120.0,
            secret: true,
        ));
    }
}
