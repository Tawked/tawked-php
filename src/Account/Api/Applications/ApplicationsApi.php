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
 * `$account->applications->applications`: the Account API's `applications.applications` operations.
 */
final class ApplicationsApi extends AccountNamespace
{
    /**
     * Create an application (names in Arabic and English, website, industry, products); it enters
     * Tawked's review at once and answers the application with its state and `next` (read
     * applications.applications.show every few hours until the review ends). `POST
     * /v1/applications`.
     *
     * Permission `applications:write`. Sent once: retried only when the request never left (or the
     * key is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * @param array{industry: string, integration?: string, name_ar: string, name_en: string, products: list<mixed>, website_url: string}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation applications.applications.create
     */
    public function create(array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
        ));
    }

    /**
     * Delete the application, its name typed to confirm (`confirm_name`, Arabic or English): every
     * key is revoked, its own WhatsApp number disconnected. Cannot be undone. `DELETE
     * /v1/applications/{application}`.
     *
     * Permission `applications:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Sent once: retried only
     * when the request never left (or the key is still in progress). After a timeout the exception
     * carries the `Idempotency-Key`: call again with it, never a new one. After a 5xx Tawked
     * released the key: check whether it took effect before calling again.
     *
     * @param string $application `application` in the path
     * @param array{confirm_name: string}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation applications.applications.delete
     */
    public function delete(string $application, array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'DELETE',
            path: '/v1/applications/'.self::segment($application, 'application'),
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            query: $params,
        ));
    }

    /**
     * List the applications this token reaches, newest first: names, logo, products, status and
     * state (working, review, review_flagged, rejected, suspended, paused, disabled, withdrawn),
     * the decision's words, what the person may do. `GET /v1/applications`.
     *
     * Permission `applications:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation applications.applications.list
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
            path: '/v1/applications',
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
     * Apply again after withdrawing: the application goes back into Tawked's review queue and the
     * team is told; answers the application and `next` (read applications.applications.show until
     * the review ends). `POST /v1/applications/{application}/reapply`.
     *
     * Permission `applications:write`. Sent once: retried only when the request never left (or the
     * key is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * @param string $application `application` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation applications.applications.reapply
     */
    public function reapply(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/reapply',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * One application as its Details page shows it, with its review history: where the review
     * stands, a rejection's reason and appeal, Verify's pause, its WhatsApp number. `GET
     * /v1/applications/{application}`.
     *
     * Permission `applications:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation applications.applications.show
     */
    public function show(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application'),
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }

    /**
     * Lift the pause Verify's pumping guard put on the application (its `paused`), once the spike
     * was looked at; codes go out again. Answers the application. `POST
     * /v1/applications/{application}/unpause`.
     *
     * Permission `applications:write`. Sent once: retried only when the request never left (or the
     * key is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * @param string $application `application` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation applications.applications.unpause
     */
    public function unpause(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/unpause',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * Save the application's details (both names, website, industry; optionally `remove_logo`):
     * answers the application, whether the change sent it back to Tawked's review, and `next`.
     * `PATCH /v1/applications/{application}`.
     *
     * Permission `applications:write`. Sent once: retried only when the request never left (or the
     * key is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * @param string $application `application` in the path
     * @param array{industry: string, integration?: string, logo?: \Tawked\Attachment|\SplFileInfo|\Psr\Http\Message\StreamInterface|resource|string|null, name_ar: string, name_en: string, remove_logo?: bool, website_url: string}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation applications.applications.update
     */
    public function update(string $application, array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'PATCH',
            path: '/v1/applications/'.self::segment($application, 'application'),
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            multipart: $params,
            files: ['logo'],
        ));
    }

    /**
     * Withdraw the application's review request: it leaves Tawked's queue until
     * applications.applications.reapply; answers the application. `POST
     * /v1/applications/{application}/withdraw`.
     *
     * Permission `applications:write`. Sent once: retried only when the request never left (or the
     * key is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * @param string $application `application` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation applications.applications.withdraw
     */
    public function withdraw(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/withdraw',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }
}
