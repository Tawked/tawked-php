<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Service\Chat;

use Tawked\Http\Transport;
use Tawked\Model\Chat\ChatMenu;
use Tawked\Model\Chat\ChatMenuDeleteResponse;
use Tawked\Model\Chat\ChatMenuListResponse;
use Tawked\Service\Service;

/**
 * `$tawked->chat->menus`: Tawked Chat for connected apps (a connected app's key only).
 */
final class MenusService extends Service
{
    /**
     * List menus. `GET /v1/chat/menus`.
     *
     * For a connected app's key alone, with `chat:menus` in its connection's grant: an
     * application's own key, full access included, answers `403 insufficient_scope`, and adds its
     * menus in the console. Lists the conversation menus this connection made on its application,
     * oldest first. Never the client's own menus or another app's.
     *
     * Scope `chat:menus`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * Errors: account_not_active, insufficient_scope, internal_error, ip_not_allowed, key_expired,
     * live_key_required, too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->chat->menus->list();
     *
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint chat-menus
     */
    public function list(array $options = []): ChatMenuListResponse
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/chat/menus',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        );

        return ChatMenuListResponse::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Add a menu. `POST /v1/chat/menus`.
     *
     * For a connected app's key alone, with `chat:menus` in its connection's grant: an
     * application's own key, full access included, answers `403 insufficient_scope`, and adds its
     * menus in the console. Adds a conversation menu to the application, under the console's own
     * rules, and answers it. Tawked checks whether the site may be framed inside Tawked Chat and
     * answers it in `frame`: `opens` (framed in the conversation), `window` (the site refuses
     * frames: agents get "Open in a new window") or `unknown` (Tawked could not tell). The client's
     * team sees the tab after reloading Tawked Chat. Put a key made for this one client in the
     * link, never your Tawked key or client secret. A refused field answers `400 invalid_request`
     * with `fields` naming it and `errors` giving its codes.
     *
     * Scope `chat:menus`. Retried after a network failure, a timeout or a 5xx under the same
     * `Idempotency-Key` (minted when you pass none): the server keeps the key through a failure.
     *
     * Errors: account_not_active, chat_not_ready, chat_unavailable, idempotency_in_progress,
     * idempotency_key_reused, insufficient_scope, internal_error, invalid_json, invalid_request,
     * ip_not_allowed, key_expired, live_key_required, menu_limit_reached, too_many_requests,
     * unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->chat->menus->create(['title' => 'الحجوزات', 'url' => 'https://app.yourplatform.example/tawked/bookings?phone={customer_phone}']);
     *
     * @param array{title: string, url: string, icon?: string|array<string, mixed>}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint chat-menu-create
     */
    public function create(array $params, array $options = []): ChatMenu
    {
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/chat/menus',
            options: $options,
            retry: Transport::IDEMPOTENT,
            timeout: 30.0,
            json: self::body($params, []),
        );

        return ChatMenu::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Get a menu. `GET /v1/chat/menus/{id}`.
     *
     * For a connected app's key alone, with `chat:menus` in its connection's grant: an
     * application's own key, full access included, answers `403 insufficient_scope`, and adds its
     * menus in the console. Reads one menu this connection made: its link as saved, its icon and
     * the last frame check.
     *
     * Scope `chat:menus`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * Errors: account_not_active, insufficient_scope, internal_error, ip_not_allowed, key_expired,
     * live_key_required, not_found, too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->chat->menus->get('5b1d2c3e-4f5a-4b6c-8d7e-9f0a1b2c3d4e');
     *
     * @param string $id `id` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint chat-menu
     */
    public function get(string $id, array $options = []): ChatMenu
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/chat/menus/'.self::segment($id, 'id'),
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        );

        return ChatMenu::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Update a menu. `PATCH /v1/chat/menus/{id}`.
     *
     * For a connected app's key alone, with `chat:menus` in its connection's grant: an
     * application's own key, full access included, answers `403 insufficient_scope`, and adds its
     * menus in the console. Changes any of `title`, `url` and `icon` of a menu this connection
     * made; what is left out stays, and at least one is sent. A new `url` is checked for framing
     * again. Use it to rotate the key in the link: keep the new key on your server beside the old
     * one, send the new link, then drop the old key once the client's team has reloaded Tawked
     * Chat. Answers the menu as it stands after the change.
     *
     * Scope `chat:menus`. Retried after a network failure, a timeout or a 5xx under the same
     * `Idempotency-Key` (minted when you pass none): the server keeps the key through a failure.
     *
     * Errors: account_not_active, chat_not_ready, chat_unavailable, idempotency_in_progress,
     * idempotency_key_reused, insufficient_scope, internal_error, invalid_json, invalid_request,
     * ip_not_allowed, key_expired, live_key_required, not_found, too_many_requests, unauthorized
     * (Tawked\ErrorCode).
     *
     * @example $tawked->chat->menus->update('5b1d2c3e-4f5a-4b6c-8d7e-9f0a1b2c3d4e', ['title' => 'حجوزاتي']);
     *
     * @param string $id `id` in the path
     * @param array{title?: string, url?: string, icon?: string|array<string, mixed>}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint chat-menu-update
     */
    public function update(string $id, array $params = [], array $options = []): ChatMenu
    {
        $response = $this->transport->send(
            method: 'PATCH',
            path: '/v1/chat/menus/'.self::segment($id, 'id'),
            options: $options,
            retry: Transport::IDEMPOTENT,
            timeout: 30.0,
            json: self::body($params, []),
        );

        return ChatMenu::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Delete a menu. `DELETE /v1/chat/menus/{id}`.
     *
     * For a connected app's key alone, with `chat:menus` in its connection's grant: an
     * application's own key, full access included, answers `403 insufficient_scope`, and adds its
     * menus in the console. Removes a menu this connection made, from Tawked Chat and from the
     * client's console.
     *
     * Scope `chat:menus`. Retried after a network failure, a timeout or a 5xx under the same
     * `Idempotency-Key` (minted when you pass none): the server keeps the key through a failure.
     *
     * Errors: account_not_active, chat_not_ready, chat_unavailable, idempotency_in_progress,
     * idempotency_key_reused, insufficient_scope, internal_error, invalid_request, ip_not_allowed,
     * key_expired, live_key_required, not_found, too_many_requests, unauthorized
     * (Tawked\ErrorCode).
     *
     * @example $tawked->chat->menus->delete('5b1d2c3e-4f5a-4b6c-8d7e-9f0a1b2c3d4e');
     *
     * @param string $id `id` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint chat-menu-delete
     */
    public function delete(string $id, array $options = []): ChatMenuDeleteResponse
    {
        $response = $this->transport->send(
            method: 'DELETE',
            path: '/v1/chat/menus/'.self::segment($id, 'id'),
            options: $options,
            retry: Transport::IDEMPOTENT,
            timeout: 30.0,
        );

        return ChatMenuDeleteResponse::fromArray($response->body)->withMeta($response->meta);
    }
}
