<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Service\WhatsApp;

use Tawked\Http\Transport;
use Tawked\Model\WhatsApp\Message;
use Tawked\Model\WhatsApp\SentMessage;
use Tawked\Service\Service;

/**
 * `$tawked->whatsapp->messages`: WhatsApp template messages and templates (Tawked Notifications).
 */
final class MessagesService extends Service
{
    /**
     * Send a template message. `POST /v1/whatsapp/messages`.
     *
     * Sends an approved template to a Saudi mobile through the number attached to the application,
     * and answers `202` with an id to track it. The fee, if any, is for the send; a failed send
     * refunds it.
     *
     * Scope `notifications:send`. Sent once: retried only when the request never left (or the key
     * is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * Errors: account_not_active, consent_opted_out, insufficient_credits, insufficient_scope,
     * internal_error, invalid_destination, invalid_json, invalid_params, invalid_request,
     * ip_not_allowed, key_expired, live_key_required, no_whatsapp_number, number_disconnected,
     * send_failed, service_not_found, service_required, template_category_not_allowed,
     * template_not_approved, templates_unavailable, too_many_requests, unauthorized,
     * unknown_template, unsupported_template, whatsapp_not_enabled (Tawked\ErrorCode).
     *
     * @example $tawked->whatsapp->messages->send(['to' => '0551234567', 'template' => 'order_received_v1', 'lang' => 'ar', 'params' => [...], ...]);
     *
     * @param array{to: string, template: string, lang?: string|null, params?: array<string, mixed>, header?: array<string, mixed>, buttons?: list<mixed>, reference?: string, application?: string}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint whatsapp-send
     */
    public function send(array $params, array $options = []): SentMessage
    {
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/whatsapp/messages',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, ['params', 'header']),
        );

        return SentMessage::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Get a message. `GET /v1/whatsapp/messages/{id}`.
     *
     * Reads a message's current status and timestamps. Statuses from Meta are followed for 48 hours
     * after the send; `error` is set when the message failed. A message belongs to the key's
     * application; any other id answers `404`.
     *
     * Scope `notifications:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * Errors: account_not_active, insufficient_scope, internal_error, ip_not_allowed, key_expired,
     * live_key_required, not_found, service_not_found, service_required, too_many_requests,
     * unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->whatsapp->messages->get('0f4c9c0e-6d2b-4b8a-9c3e-7a1d2e3f4a5b', []);
     *
     * @param string $id `id` in the path
     * @param array{application?: string}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint whatsapp-message
     */
    public function get(string $id, array $params = [], array $options = []): Message
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/whatsapp/messages/'.self::segment($id, 'id'),
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
            query: $params,
        );

        return Message::fromArray($response->body)->withMeta($response->meta);
    }
}
