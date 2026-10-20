<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Account;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountPage;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;
use Tawked\Pagination\Paginator;

/**
 * `$account->account->tokens`: the Account API's `account.tokens` operations.
 */
final class TokensApi extends AccountNamespace
{
    /**
     * The last calls made with an Account API token, newest first: the operation, method and path,
     * the status and error code, the address, the client (user agent), the assistant when it came
     * through the MCP server, and how long it took. Kept 90 days. Your own tokens, or any of the
     * account's for the owner. `GET /v1/account/tokens/{token}/activity`.
     *
     * Permission `account:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $token `token` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation account.tokens.activity
     */
    public function activity(string $token, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/account/tokens/'.self::segment($token, 'token').'/activity',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }

    /**
     * The Account API tokens this person may see (their own; the owner sees every token of the
     * account), newest first: name, prefix, permissions, applications, who made it, last use,
     * expiry and revocation. Never the secret. `GET /v1/account/tokens`.
     *
     * Permission `account:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation account.tokens.list
     */
    public function list(array $options = []): AccountPage
    {
        return $this->listPage([], $options);
    }

    /**
     * @param array<string, mixed> $query
     * @param array<string, mixed> $options
     */
    private function listPage(array $query, array $options): AccountPage
    {
        return AccountPage::from($this->transport->send(
            method: 'GET',
            path: '/v1/account/tokens',
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
    public function iterate(array $params = [], array $options = []): \Generator
    {
        return Paginator::cursor(fn(array $page): AccountPage => $this->listPage($page, $options), $params, 'cursor');
    }

    /**
     * Revoke an Account API token: the next call made with it answers 401. Your own, or any of the
     * account's for the owner. Answers the token, revoked. `DELETE /v1/account/tokens/{token}`.
     *
     * Permission `account:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Sent once: retried only
     * when the request never left (or the key is still in progress). After a timeout the exception
     * carries the `Idempotency-Key`: call again with it, never a new one. After a 5xx Tawked
     * released the key: check whether it took effect before calling again.
     *
     * @param string $token `token` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation account.tokens.revoke
     */
    public function revoke(string $token, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'DELETE',
            path: '/v1/account/tokens/'.self::segment($token, 'token'),
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }
}
