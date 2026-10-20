<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Integrations;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->integrations->supabase`: the Account API's `integrations.supabase` operations.
 */
final class SupabaseApi extends AccountNamespace
{
    /**
     * Connect Supabase's Send SMS hook to this application (once; again answers the same
     * connection): answers the hook URL and its secret, and the `human_action` that has the person
     * paste them into Supabase › Auth › Hooks (only they reach their Supabase dashboard). `POST
     * /v1/applications/{application}/integrations/supabase`.
     *
     * Permission `integrations:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Never retried: its
     * answer holds a secret shown once (120 s timeout). After a failure, find what it made by its
     * id, then rotate or revoke it.
     *
     * @param string $application `application` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation integrations.supabase.connect
     */
    public function connect(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/integrations/supabase',
            options: $options,
            retry: Transport::NEVER,
            timeout: 120.0,
            secret: true,
        ));
    }

    /**
     * Disconnect Supabase: the hook stops sending codes at once and the keys it sent with are
     * revoked; the codes it sent stay in the Verify log. Answers ok. `DELETE
     * /v1/applications/{application}/integrations/supabase`.
     *
     * Permission `integrations:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Sent once: retried only
     * when the request never left (or the key is still in progress). After a timeout the exception
     * carries the `Idempotency-Key`: call again with it, never a new one. After a 5xx Tawked
     * released the key: check whether it took effect before calling again.
     *
     * @param string $application `application` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation integrations.supabase.disconnect
     */
    public function disconnect(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'DELETE',
            path: '/v1/applications/'.self::segment($application, 'application').'/integrations/supabase',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * Replace the hook's secret: a new one made by Tawked (no body), or the one Supabase's hook
     * settings generated (`secret`, `v1,whsec_…`); answers the hook URL and the secret, once.
     * `POST /v1/applications/{application}/integrations/supabase/secret`.
     *
     * Permission `integrations:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Never retried: its
     * answer holds a secret shown once (120 s timeout). After a failure, find what it made by its
     * id, then rotate or revoke it.
     *
     * @param string $application `application` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation integrations.supabase.secret
     */
    public function secret(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/integrations/supabase/secret',
            options: $options,
            retry: Transport::NEVER,
            timeout: 120.0,
            secret: true,
        ));
    }

    /**
     * Read the application's Supabase phone sign-in: how codes go out (live, review: the owner's
     * phone only, blocked), the connection's hook URL, language, last call and outcome, and the
     * last codes it sent. `GET /v1/applications/{application}/integrations/supabase`.
     *
     * Permission `integrations:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation integrations.supabase.show
     */
    public function show(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/integrations/supabase',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }

    /**
     * Run the hook as Supabase would for a sign-in on the account owner's own phone: a real code is
     * sent there with the test key; answers the outcome, the hook's status and body, and the
     * request it was given. `POST /v1/applications/{application}/integrations/supabase/test`.
     *
     * Permission `integrations:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Sent once: retried only
     * when the request never left (or the key is still in progress). After a timeout the exception
     * carries the `Idempotency-Key`: call again with it, never a new one. After a 5xx Tawked
     * released the key: check whether it took effect before calling again.
     *
     * @param string $application `application` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation integrations.supabase.test
     */
    public function test(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/integrations/supabase/test',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * Set the language of the sign-in codes Supabase's hook sends (`lang`: ar or en); answers ok.
     * `PATCH /v1/applications/{application}/integrations/supabase`.
     *
     * Permission `integrations:write`. Sent once: retried only when the request never left (or the
     * key is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * @param string $application `application` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation integrations.supabase.update
     */
    public function update(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'PATCH',
            path: '/v1/applications/'.self::segment($application, 'application').'/integrations/supabase',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }
}
