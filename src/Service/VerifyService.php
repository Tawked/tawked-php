<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Service;

use Tawked\Http\Transport;
use Tawked\Model\Verify\CheckResult;
use Tawked\Model\Verify\Verification;
use Tawked\Model\Verify\VerificationCanceled;
use Tawked\Model\Verify\VerificationResent;
use Tawked\Model\Verify\VerificationStarted;

/**
 * `$tawked->verify`: Tawked Verify, one-time codes by SMS.
 */
final class VerifyService extends Service
{
    /**
     * Start a verification. `POST /v1/verify/start`.
     *
     * Sends a one-time code to a destination and returns an id to check it against.
     *
     * Scope `verify:send`. Sent once: retried only when the request never left (or the key is still
     * in progress). After a timeout the exception carries the `Idempotency-Key`: call again with
     * it, never a new one. After a 5xx Tawked released the key: check whether it took effect before
     * calling again.
     *
     * Errors: account_not_active, application_paused, insufficient_credits, insufficient_scope,
     * internal_error, invalid_destination, invalid_json, invalid_request, ip_not_allowed,
     * ip_rate_limited, key_expired, rate_limited, sandbox_quota_exceeded,
     * sandbox_unsupported_channel, sandbox_unverified_destination, send_failed, service_not_found,
     * service_required, spend_cap_reached, too_many_requests, unauthorized, unsupported_channel
     * (Tawked\ErrorCode).
     *
     * @example $tawked->verify->start(['to' => '0551234567', 'channel' => 'sms', 'lang' => 'ar', 'reference' => 'order-1042', ...]);
     *
     * @param array{to: string, phone?: string, channel?: string, lang?: string, application?: string, reference?: string, client_ip?: string, autofill?: string}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint start
     */
    public function start(array $params, array $options = []): VerificationStarted
    {
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/verify/start',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
        );

        return VerificationStarted::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Check a code. `POST /v1/verify/check`.
     *
     * Verifies the code the end user typed against the id from /verify/start.
     *
     * Scope `verify:check`. Never retried once it may have reached the server: each try spends
     * something.
     *
     * Errors: account_not_active, insufficient_scope, internal_error, invalid_json,
     * invalid_request, ip_not_allowed, key_expired, not_found, service_not_found, service_required,
     * too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->verify->check(['id' => 'b7e5c2b0-9c1a-4e2f-8f2a-3a6b0e9d1c44', 'code' => '123456']);
     *
     * @param array{id: string, code: string, application?: string}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint check
     */
    public function check(array $params, array $options = []): CheckResult
    {
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/verify/check',
            options: $options,
            retry: Transport::NEVER,
            timeout: 30.0,
            json: self::body($params, []),
        );

        return CheckResult::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Get a verification. `GET /v1/verify/{id}`.
     *
     * Reads the current state of a verification, meaning its status, channel, destination, and the
     * attempts and resends left, without consuming an attempt.
     *
     * Scope `verify:check`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * Errors: account_not_active, insufficient_scope, internal_error, ip_not_allowed, key_expired,
     * not_found, service_not_found, service_required, too_many_requests, unauthorized
     * (Tawked\ErrorCode).
     *
     * @example $tawked->verify->get('b7e5c2b0-9c1a-4e2f-8f2a-3a6b0e9d1c44', []);
     *
     * @param string $id `id` in the path
     * @param array{application?: string}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint status
     */
    public function get(string $id, array $params = [], array $options = []): Verification
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/verify/'.self::segment($id, 'id'),
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
            query: $params,
        );

        return Verification::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Resend a code. `POST /v1/verify/{id}/resend`.
     *
     * Sends a new code on the same id: the previous code stops working, attempts and the expiry
     * reset, and the send is charged and rate-limited like any other, up to 3 resends per
     * verification.
     *
     * Scope `verify:send`. Never retried once it may have reached the server: each try spends
     * something.
     *
     * Errors: account_not_active, application_paused, insufficient_credits, insufficient_scope,
     * internal_error, invalid_json, ip_not_allowed, ip_rate_limited, key_expired, not_found,
     * not_resendable, rate_limited, resend_limit_reached, send_failed, service_not_found,
     * service_required, spend_cap_reached, too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->verify->resend('b7e5c2b0-9c1a-4e2f-8f2a-3a6b0e9d1c44', []);
     *
     * @param string $id `id` in the path
     * @param array{application?: string}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint resend
     */
    public function resend(string $id, array $params = [], array $options = []): VerificationResent
    {
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/verify/'.self::segment($id, 'id').'/resend',
            options: $options,
            retry: Transport::NEVER,
            timeout: 30.0,
            json: self::body($params, []),
        );

        return VerificationResent::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Cancel a verification. `POST /v1/verify/{id}/cancel`.
     *
     * Cancels a pending verification. A later `check` on it answers `{ "verified": false, "status":
     * "canceled" }`.
     *
     * Scope `verify:send`. Never retried once it may have reached the server: each try spends
     * something.
     *
     * Errors: account_not_active, insufficient_scope, internal_error, invalid_json, ip_not_allowed,
     * key_expired, not_cancelable, not_found, service_not_found, service_required,
     * too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->verify->cancel('b7e5c2b0-9c1a-4e2f-8f2a-3a6b0e9d1c44', []);
     *
     * @param string $id `id` in the path
     * @param array{application?: string}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint cancel
     */
    public function cancel(string $id, array $params = [], array $options = []): VerificationCanceled
    {
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/verify/'.self::segment($id, 'id').'/cancel',
            options: $options,
            retry: Transport::NEVER,
            timeout: 30.0,
            json: self::body($params, []),
        );

        return VerificationCanceled::fromArray($response->body)->withMeta($response->meta);
    }
}
