<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Chat;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountPage;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;
use Tawked\Pagination\Paginator;

/**
 * `$account->chat->menus`: the Account API's `chat.menus` operations.
 */
final class MenusApi extends AccountNamespace
{
    /**
     * Check a link before adding it as a menu: whether Tawked Chat can open it inside the
     * conversation (`frame`: `opens`, `window` or `unknown`), and the link's own problem if any
     * (`problem`: `length`, `http`, `host`, or null). `POST
     * /v1/applications/{application}/chat/menus/check`.
     *
     * Permission `chat:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $application `application` in the path
     * @param array{url: string}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation chat.menus.check
     */
    public function check(string $application, array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/chat/menus/check',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
        ));
    }

    /**
     * Add a chat menu: a page of your business (`title`, an https `url`) shown beside this
     * application's conversations in Tawked Chat; `icon_mode` takes the site's icon (`site`, the
     * default), an uploaded `icon` (`upload`) or none. Answers the menu. `POST
     * /v1/applications/{application}/chat/menus`.
     *
     * Permission `chat:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $application `application` in the path
     * @param array{icon?: \Tawked\Attachment|\SplFileInfo|\Psr\Http\Message\StreamInterface|resource|string|null, icon_mode?: string, title: string, url: string}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation chat.menus.create
     */
    public function create(string $application, array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/chat/menus',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            multipart: $params,
            files: ['icon'],
        ));
    }

    /**
     * Remove a chat menu from Tawked Chat and from this application; answers ok. `DELETE
     * /v1/applications/{application}/chat/menus/{menu}`.
     *
     * Permission `chat:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Sent once: retried only
     * when the request never left (or the key is still in progress). After a timeout the exception
     * carries the `Idempotency-Key`: call again with it, never a new one. After a 5xx Tawked
     * released the key: check whether it took effect before calling again.
     *
     * @param string $application `application` in the path
     * @param string $menu `menu` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation chat.menus.delete
     */
    public function delete(string $application, string $menu, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'DELETE',
            path: '/v1/applications/'.self::segment($application, 'application').'/chat/menus/'.self::segment($menu, 'menu'),
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * List the chat menus that show beside this application's conversations in Tawked Chat (its
     * own, the ones of every conversation, and the Salla store's orders tab), in Tawked Chat's
     * order. `GET /v1/applications/{application}/chat/menus`.
     *
     * Permission `chat:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation chat.menus.list
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
            path: '/v1/applications/'.self::segment($application, 'application').'/chat/menus',
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
     * One chat menu of this application: its title, link, icon and whether it opens inside Tawked
     * Chat. `GET /v1/applications/{application}/chat/menus/{menu}`.
     *
     * Permission `chat:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param string $menu `menu` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation chat.menus.show
     */
    public function show(string $application, string $menu, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/chat/menus/'.self::segment($menu, 'menu'),
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }

    /**
     * Change a chat menu: send its `title` and `url` (both, as they should be), and `icon_mode`
     * only to change the icon. Answers the menu. `PATCH
     * /v1/applications/{application}/chat/menus/{menu}`.
     *
     * Permission `chat:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $application `application` in the path
     * @param string $menu `menu` in the path
     * @param array{icon?: \Tawked\Attachment|\SplFileInfo|\Psr\Http\Message\StreamInterface|resource|string|null, icon_mode?: string, title: string, url: string}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation chat.menus.update
     */
    public function update(string $application, string $menu, array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'PATCH',
            path: '/v1/applications/'.self::segment($application, 'application').'/chat/menus/'.self::segment($menu, 'menu'),
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            multipart: $params,
            files: ['icon'],
        ));
    }
}
