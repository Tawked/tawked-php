<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Service\Chat;

use Tawked\Http\Transport;
use Tawked\Model\Chat\Attendant;
use Tawked\Model\Chat\ChatAttendantDraftSaveResponse;
use Tawked\Model\Chat\ChatAttendantPublishResponse;
use Tawked\Service\Service;

/**
 * `$tawked->chat->attendant`: Tawked Chat for connected apps (a connected app's key only).
 */
final class AttendantService extends Service
{
    /**
     * Get the auto-attendant. `GET /v1/chat/attendant`.
     *
     * For a connected app's key alone, with `chat:attendant` in its connection's grant: an
     * application's own key, full access included, answers `403 insufficient_scope`, and builds its
     * auto-attendant in the console. Reads the application's auto-attendant: its state, its draft
     * and the published menu. The client sees and may change the same one in the console. The
     * attendant object: `state` is `first` (nothing saved yet: `draft` is Tawked's starter menu),
     * `draft` (saved, never published), `off` (published, switched off), `live` (published and on),
     * `no_number` (no connected WhatsApp number), `no_chat` (the number is not in Tawked Chat) or
     * `blocked` (the number cannot send: `lock` is `payment`, an unpaid balance, or `blocked`);
     * `enabled`; `mode`; `language`; `draft`, the menu being edited, with `draft_saved_at` and its
     * `problems`; `published`, `{ version, published_at, menu }`, null before the first publish;
     * `changed`, whether the draft differs from the published menu.
     *
     * Scope `chat:attendant`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * Errors: account_not_active, insufficient_scope, internal_error, ip_not_allowed, key_expired,
     * live_key_required, too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->chat->attendant->get();
     *
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint chat-attendant
     */
    public function get(array $options = []): Attendant
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/chat/attendant',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        );

        return Attendant::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Save the auto-attendant draft. `PUT /v1/chat/attendant/draft`.
     *
     * For a connected app's key alone, with `chat:attendant` in its connection's grant: an
     * application's own key, full access included, answers `403 insufficient_scope`, and builds its
     * auto-attendant in the console. Replaces the whole draft with `menu` and answers it as stored,
     * every choice with its `id`, and its `problems`. The draft reaches no customer until it is
     * published. The console and the API share this one draft, and the last save wins. A draft may
     * hold problems; `POST /v1/chat/attendant/publish` refuses them. Draft saves are not recorded
     * in the client's account record, as in the console.
     *
     * Scope `chat:attendant`. Retried after a network failure, a timeout or a 5xx under the same
     * `Idempotency-Key` (minted when you pass none): the server keeps the key through a failure.
     *
     * Errors: account_not_active, idempotency_in_progress, idempotency_key_reused,
     * insufficient_scope, internal_error, invalid_json, invalid_request, ip_not_allowed,
     * key_expired, live_key_required, too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->chat->attendant->saveDraft(['menu' => [...]]);
     *
     * @param array{menu: array<string, mixed>}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint chat-attendant-draft
     */
    public function saveDraft(array $params, array $options = []): ChatAttendantDraftSaveResponse
    {
        $response = $this->transport->send(
            method: 'PUT',
            path: '/v1/chat/attendant/draft',
            options: $options,
            retry: Transport::IDEMPOTENT,
            timeout: 30.0,
            json: self::body($params, ['menu']),
        );

        return ChatAttendantDraftSaveResponse::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Publish the auto-attendant. `POST /v1/chat/attendant/publish`.
     *
     * For a connected app's key alone, with `chat:attendant` in its connection's grant: an
     * application's own key, full access included, answers `403 insufficient_scope`, and builds its
     * auto-attendant in the console. Takes no body. The saved draft becomes the next version and
     * the live menu: while the auto-attendant is on, customers get it from their next new
     * conversation. A draft with problems is refused with `422 attendant_not_ready` and its
     * `problems`; before any draft was saved, `409 attendant_no_draft`. Recorded in the client's
     * account record under the app's name.
     *
     * Scope `chat:attendant`. Retried after a network failure, a timeout or a 5xx under the same
     * `Idempotency-Key` (minted when you pass none): the server keeps the key through a failure.
     *
     * Errors: account_not_active, attendant_no_draft, attendant_not_ready, idempotency_in_progress,
     * idempotency_key_reused, insufficient_scope, internal_error, invalid_request, ip_not_allowed,
     * key_expired, live_key_required, too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->chat->attendant->publish();
     *
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint chat-attendant-publish
     */
    public function publish(array $options = []): ChatAttendantPublishResponse
    {
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/chat/attendant/publish',
            options: $options,
            retry: Transport::IDEMPOTENT,
            timeout: 30.0,
        );

        return ChatAttendantPublishResponse::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Switch the auto-attendant on or off. `PUT /v1/chat/attendant/state`.
     *
     * For a connected app's key alone, with `chat:attendant` in its connection's grant: an
     * application's own key, full access included, answers `403 insufficient_scope`, and builds its
     * auto-attendant in the console. Switches the auto-attendant on or off and answers the
     * attendant object. On needs a published menu (`409 attendant_unpublished`), the number in
     * Tawked Chat (`409 chat_not_ready` with `reason` `number`) and a number that can send (`409
     * sending_blocked` with `reason` `payment` or `blocked`). Off hands the conversations it holds
     * to the team. Recorded in the client's account record under the app's name.
     *
     * Scope `chat:attendant`. Retried after a network failure, a timeout or a 5xx under the same
     * `Idempotency-Key` (minted when you pass none): the server keeps the key through a failure.
     *
     * Errors: account_not_active, attendant_unpublished, chat_not_ready, chat_unavailable,
     * idempotency_in_progress, idempotency_key_reused, insufficient_scope, internal_error,
     * invalid_json, invalid_request, ip_not_allowed, key_expired, live_key_required,
     * sending_blocked, too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->chat->attendant->setState(['enabled' => true]);
     *
     * @param array{enabled: bool}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint chat-attendant-state
     */
    public function setState(array $params, array $options = []): Attendant
    {
        $response = $this->transport->send(
            method: 'PUT',
            path: '/v1/chat/attendant/state',
            options: $options,
            retry: Transport::IDEMPOTENT,
            timeout: 30.0,
            json: self::body($params, []),
        );

        return Attendant::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Update the auto-attendant settings. `PATCH /v1/chat/attendant/settings`.
     *
     * For a connected app's key alone, with `chat:attendant` in its connection's grant: an
     * application's own key, full access included, answers `403 insufficient_scope`, and builds its
     * auto-attendant in the console. Changes any of `mode` and `language`, at least one; what is
     * left out stays. Answers the attendant object. Recorded in the client's account record under
     * the app's name.
     *
     * Scope `chat:attendant`. Retried after a network failure, a timeout or a 5xx under the same
     * `Idempotency-Key` (minted when you pass none): the server keeps the key through a failure.
     *
     * Errors: account_not_active, idempotency_in_progress, idempotency_key_reused,
     * insufficient_scope, internal_error, invalid_json, invalid_request, ip_not_allowed,
     * key_expired, live_key_required, too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->chat->attendant->updateSettings(['mode' => 'menu_only', 'language' => 'ar']);
     *
     * @param array{mode?: string, language?: string}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint chat-attendant-settings
     */
    public function updateSettings(array $params = [], array $options = []): Attendant
    {
        $response = $this->transport->send(
            method: 'PATCH',
            path: '/v1/chat/attendant/settings',
            options: $options,
            retry: Transport::IDEMPOTENT,
            timeout: 30.0,
            json: self::body($params, []),
        );

        return Attendant::fromArray($response->body)->withMeta($response->meta);
    }
}
