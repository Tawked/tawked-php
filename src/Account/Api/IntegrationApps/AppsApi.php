<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\IntegrationApps;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountPage;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;
use Tawked\Pagination\Paginator;

/**
 * `$account->integrationApps->apps`: the Account API's `integration_apps.apps` operations.
 */
final class AppsApi extends AccountNamespace
{
    /**
     * Make an integration app, in testing (open to this account's own applications only); answers
     * it with its client id and its client secret, the secret this once. `POST
     * /v1/integration-apps`.
     *
     * Permission `integration_apps:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Never retried: its
     * answer holds a secret shown once (120 s timeout). After a failure, find what it made by its
     * id, then rotate or revoke it.
     *
     * @param array{connect_url?: string|null, description?: string|null, logo?: \Tawked\Attachment|\SplFileInfo|\Psr\Http\Message\StreamInterface|resource|string|null, name_ar: string, name_en: string, redirect_uris: list<mixed>, remove_logo?: bool, site_url: string}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation integration_apps.apps.create
     */
    public function create(array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/integration-apps',
            options: $options,
            retry: Transport::NEVER,
            timeout: 120.0,
            multipart: $params,
            files: ['logo'],
            secret: true,
        ));
    }

    /**
     * Delete an integration app that was never approved, with its test connections (their keys
     * revoked), its requests and its client. Cannot be undone. `DELETE /v1/integration-apps/{app}`.
     *
     * Permission `integration_apps:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Sent once: retried only
     * when the request never left (or the key is still in progress). After a timeout the exception
     * carries the `Idempotency-Key`: call again with it, never a new one. After a 5xx Tawked
     * released the key: check whether it took effect before calling again.
     *
     * @param string $app `app` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation integration_apps.apps.delete
     */
    public function delete(string $app, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'DELETE',
            path: '/v1/integration-apps/'.self::segment($app, 'app'),
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * List the integration apps this partner account made, newest first: each with its status
     * (testing, in_review, approved, rejected), whether a change waits for Tawked's team, and how
     * many applications it is connected to. `GET /v1/integration-apps`.
     *
     * Permission `integration_apps:read`. Retried after a network failure, a timeout or a 5xx (a
     * read).
     *
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation integration_apps.apps.list
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
            path: '/v1/integration-apps',
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
     * Read one integration app: its status, client id, facts, return addresses, the change that
     * waits for Tawked's team, why it was sent back, what the review still needs (`checklist`) and
     * what may be done now (`can`). Never its secret. `GET /v1/integration-apps/{app}`.
     *
     * Permission `integration_apps:read`. Retried after a network failure, a timeout or a 5xx (a
     * read).
     *
     * @param string $app `app` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation integration_apps.apps.show
     */
    public function show(string $app, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/integration-apps/'.self::segment($app, 'app'),
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }

    /**
     * Send an integration app in testing (or sent back) to Tawked's review; answers it in_review.
     * Tawked's team approves it or sends it back with a reason: `next` says how often to read
     * integration_apps.apps.show. `POST /v1/integration-apps/{app}/submit`.
     *
     * Permission `integration_apps:write`. Sent once: retried only when the request never left (or
     * the key is still in progress). After a timeout the exception carries the `Idempotency-Key`:
     * call again with it, never a new one. After a 5xx Tawked released the key: check whether it
     * took effect before calling again.
     *
     * @param string $app `app` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation integration_apps.apps.submit
     */
    public function submit(string $app, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/integration-apps/'.self::segment($app, 'app').'/submit',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * Replace an integration app's facts and return addresses; answers the app and `outcome`: saved
     * (at once, while testing or sent back), proposed (approved: the reviewed facts wait for
     * Tawked's team, the approved ones keep working), cancelled (the change that waited was
     * dropped) or unchanged. `PUT /v1/integration-apps/{app}`.
     *
     * Permission `integration_apps:write`. Sent once: retried only when the request never left (or
     * the key is still in progress). After a timeout the exception carries the `Idempotency-Key`:
     * call again with it, never a new one. After a 5xx Tawked released the key: check whether it
     * took effect before calling again.
     *
     * @param string $app `app` in the path
     * @param array{connect_url?: string|null, description?: string|null, logo?: \Tawked\Attachment|\SplFileInfo|\Psr\Http\Message\StreamInterface|resource|string|null, name_ar: string, name_en: string, redirect_uris: list<mixed>, remove_logo?: bool, site_url: string}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation integration_apps.apps.update
     */
    public function update(string $app, array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'PUT',
            path: '/v1/integration-apps/'.self::segment($app, 'app'),
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            multipart: $params,
            files: ['logo'],
        ));
    }

    /**
     * Take an integration app back from Tawked's review, to testing, so it can be edited; answers
     * the app. `POST /v1/integration-apps/{app}/withdraw`.
     *
     * Permission `integration_apps:write`. Sent once: retried only when the request never left (or
     * the key is still in progress). After a timeout the exception carries the `Idempotency-Key`:
     * call again with it, never a new one. After a 5xx Tawked released the key: check whether it
     * took effect before calling again.
     *
     * @param string $app `app` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation integration_apps.apps.withdraw
     */
    public function withdraw(string $app, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/integration-apps/'.self::segment($app, 'app').'/withdraw',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }
}
