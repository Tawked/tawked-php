<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Applications;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountPage;
use Tawked\Account\AccountResponse;
use Tawked\Account\Api\Applications\Webhooks\ReplaysApi;
use Tawked\Http\Transport;
use Tawked\Pagination\Paginator;

/**
 * `$account->applications->webhooks`: the Account API's `applications.webhooks` operations.
 */
final class WebhooksApi extends AccountNamespace
{
    public readonly ReplaysApi $replays;

    public function __construct(Transport $transport)
    {
        parent::__construct($transport);
        $this->replays = new ReplaysApi($transport);
    }

    /**
     * Add a webhook endpoint (`url` https on a public host, `events`, optionally `receives_test`);
     * answers it with its signing `secret` and its Standard Webhooks `standard_secret`, shown this
     * once. `POST /v1/applications/{application}/webhooks`.
     *
     * Permission `applications:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Never retried: its
     * answer holds a secret shown once (120 s timeout). After a failure, find what it made by its
     * id, then rotate or revoke it.
     *
     * @param string $application `application` in the path
     * @param array{enabled?: bool, events: list<mixed>, receives_test?: bool, signing?: array<string, mixed>, standard_webhooks?: bool, url: string}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation applications.webhooks.create
     */
    public function create(string $application, array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/webhooks',
            options: $options,
            retry: Transport::NEVER,
            timeout: 120.0,
            json: self::body($params, ['signing']),
            secret: true,
        ));
    }

    /**
     * Delete a webhook endpoint: nothing more is sent to it, and a delivery still waiting stops.
     * Its past deliveries stay in the log (applications.webhook_deliveries.list). Answers ok.
     * `DELETE /v1/applications/{application}/webhooks/{endpoint}`.
     *
     * Permission `applications:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Sent once: retried only
     * when the request never left (or the key is still in progress). After a timeout the exception
     * carries the `Idempotency-Key`: call again with it, never a new one. After a 5xx Tawked
     * released the key: check whether it took effect before calling again.
     *
     * @param string $application `application` in the path
     * @param string $endpoint `endpoint` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation applications.webhooks.delete
     */
    public function delete(string $application, string $endpoint, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'DELETE',
            path: '/v1/applications/'.self::segment($application, 'application').'/webhooks/'.self::segment($endpoint, 'endpoint'),
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * List the application's webhook endpoints (address, events, on or off, `state`
     * receiving/failing/off, last delivery), with the events it may subscribe to and the most
     * endpoints it may hold. `GET /v1/applications/{application}/webhooks`.
     *
     * Permission `applications:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation applications.webhooks.list
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
            path: '/v1/applications/'.self::segment($application, 'application').'/webhooks',
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
     * Send an endpoint's deliveries of a window again (`since`, `until` at most 7 days later and
     * within the last 30 days, `status` failed or all, optional `events`), at most 1,000 oldest
     * first, each with its own event id; answers the replay, queued. `POST
     * /v1/applications/{application}/webhooks/{endpoint}/replay`.
     *
     * Permission `applications:write`. Sent once: retried only when the request never left (or the
     * key is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * @param string $application `application` in the path
     * @param string $endpoint `endpoint` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation applications.webhooks.replay
     */
    public function replay(string $application, string $endpoint, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/webhooks/'.self::segment($endpoint, 'endpoint').'/replay',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * Answer a webhook endpoint's current signing secret (`secret`); every reveal is recorded.
     * `POST /v1/applications/{application}/webhooks/{endpoint}/reveal-secret`.
     *
     * Permission `applications:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Never retried: its
     * answer holds a secret shown once (120 s timeout). After a failure, find what it made by its
     * id, then rotate or revoke it.
     *
     * @param string $application `application` in the path
     * @param string $endpoint `endpoint` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation applications.webhooks.reveal_secret
     */
    public function revealSecret(string $application, string $endpoint, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/webhooks/'.self::segment($endpoint, 'endpoint').'/reveal-secret',
            options: $options,
            retry: Transport::NEVER,
            timeout: 120.0,
            secret: true,
        ));
    }

    /**
     * Give a webhook endpoint a new signing secret by `scheme`: `tawked` (the default) a new
     * `tawked-signature` secret, the old one stopping at once, answered as `secret`; `standard` a
     * new Standard Webhooks secret, the old one still signing beside it for 24 hours; or `both`.
     * Answers the new `secrets` once. `POST
     * /v1/applications/{application}/webhooks/{endpoint}/rotate-secret`.
     *
     * Permission `applications:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Never retried: its
     * answer holds a secret shown once (120 s timeout). After a failure, find what it made by its
     * id, then rotate or revoke it.
     *
     * @param string $application `application` in the path
     * @param string $endpoint `endpoint` in the path
     * @param array{scheme?: string|null}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation applications.webhooks.rotate_secret
     */
    public function rotateSecret(string $application, string $endpoint, array $params = [], array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/webhooks/'.self::segment($endpoint, 'endpoint').'/rotate-secret',
            options: $options,
            retry: Transport::NEVER,
            timeout: 120.0,
            json: self::body($params, []),
            secret: true,
        ));
    }

    /**
     * One webhook endpoint: its address, events, whether it receives, its state and last delivery.
     * Its secret only through applications.webhooks.reveal_secret. `GET
     * /v1/applications/{application}/webhooks/{endpoint}`.
     *
     * Permission `applications:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param string $endpoint `endpoint` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation applications.webhooks.show
     */
    public function show(string $application, string $endpoint, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/webhooks/'.self::segment($endpoint, 'endpoint'),
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }

    /**
     * Send a test delivery of one of the endpoint's events (`event`, else its first) to it, marked
     * `"test": true`, with sample data; answers the queued delivery. Read its outcome in
     * applications.webhook_deliveries.list. `POST
     * /v1/applications/{application}/webhooks/{endpoint}/test`.
     *
     * Permission `applications:write`. Sent once: retried only when the request never left (or the
     * key is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * @param string $application `application` in the path
     * @param string $endpoint `endpoint` in the path
     * @param array{event?: string|null}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation applications.webhooks.test
     */
    public function test(string $application, string $endpoint, array $params = [], array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/webhooks/'.self::segment($endpoint, 'endpoint').'/test',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
        ));
    }

    /**
     * Change a webhook endpoint: its `url` and `events` (both sent, as the form does) and,
     * optionally, `enabled` to switch it off or on, `receives_test` and
     * `signing.standard_webhooks`; the secrets stay. Answers the endpoint, with its new
     * `standard_secret` once when Standard signing is turned on for the first time. `PATCH
     * /v1/applications/{application}/webhooks/{endpoint}`.
     *
     * Permission `applications:write`. Never retried: its answer holds a secret shown once (120 s
     * timeout). After a failure, find what it made by its id, then rotate or revoke it.
     *
     * @param string $application `application` in the path
     * @param string $endpoint `endpoint` in the path
     * @param array{enabled?: bool, events: list<mixed>, receives_test?: bool, signing?: array<string, mixed>, standard_webhooks?: bool, url: string}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation applications.webhooks.update
     */
    public function update(string $application, string $endpoint, array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'PATCH',
            path: '/v1/applications/'.self::segment($application, 'application').'/webhooks/'.self::segment($endpoint, 'endpoint'),
            options: $options,
            retry: Transport::NEVER,
            timeout: 120.0,
            json: self::body($params, ['signing']),
            secret: true,
        ));
    }
}
