<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Applications;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountPage;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;
use Tawked\Pagination\Paginator;

/**
 * `$account->applications->keys`: the Account API's `applications.keys` operations.
 */
final class KeysApi extends AccountNamespace
{
    /**
     * Create a Sending API key for the application (`mode` live or test, `name`, `access` full or
     * custom with `scopes` per product, `expires_in_days`, `ip_allowlist`; Email `manage`,
     * `webhooks` true to manage the application's webhook endpoints (the `webhooks:manage` scope),
     * `email_domain_id` for a sending key limited to one domain, `inbound` true to let it read
     * received email (opt-in, live keys only)); answers the key with its `plaintext`, shown this
     * once. `POST /v1/applications/{application}/keys`.
     *
     * Permission `applications:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Never retried: its
     * answer holds a secret shown once (120 s timeout). After a failure, find what it made by its
     * id, then rotate or revoke it.
     *
     * @param string $application `application` in the path
     * @param array{access?: string, email_domain_id?: string|null, expires_in_days?: int|null, inbound?: bool|null, ip_allowlist?: string|null, mode: string, name?: string|null, scope?: string, scopes?: array<string, mixed>|null, webhooks?: bool|null}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation applications.keys.create
     */
    public function create(string $application, array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/keys',
            options: $options,
            retry: Transport::NEVER,
            timeout: 120.0,
            json: self::body($params, ['scopes']),
            secret: true,
        ));
    }

    /**
     * List the application's Sending API keys, newest first, live and test, revoked ones too:
     * prefix, mode, what each may call, allowlist, last used. Never a secret. `GET
     * /v1/applications/{application}/keys`.
     *
     * Permission `applications:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation applications.keys.list
     */
    public function list(string $application, array $options = []): AccountPage
    {
        return $this->listPage($application, [], $options);
    }

    /**
     * @param array<string, mixed> $query
     * @param array<string, mixed> $options
     */
    private function listPage(string $application, array $query, array $options): AccountPage
    {
        return AccountPage::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/keys',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
            query: $query,
        ));
    }

    /**
     * Every item of `list()`, page after page (following `next_cursor`): the same filters, and `limit` for the page size.
     *
     * @param array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @return \Generator<int, mixed>
     */
    public function iterate(string $application, array $params = [], array $options = []): \Generator
    {
        return Paginator::cursor(fn(array $page): AccountPage => $this->listPage($application, $page, $options), $params, 'cursor');
    }

    /**
     * Revoke one of the application's keys: every call with it is refused from now on (a connected
     * app's key also ends that connection). Cannot be undone. Answers ok; make a new one with
     * applications.keys.create. `DELETE /v1/applications/{application}/keys/{key}`.
     *
     * Permission `applications:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Sent once: retried only
     * when the request never left (or the key is still in progress). After a timeout the exception
     * carries the `Idempotency-Key`: call again with it, never a new one. After a 5xx Tawked
     * released the key: check whether it took effect before calling again.
     *
     * @param string $application `application` in the path
     * @param string $key `key` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation applications.keys.revoke
     */
    public function revoke(string $application, string $key, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'DELETE',
            path: '/v1/applications/'.self::segment($application, 'application').'/keys/'.self::segment($key, 'key'),
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }
}
