<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Email;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->email->settings`: the Account API's `email.settings` operations.
 */
final class SettingsApi extends AccountNamespace
{
    /**
     * The application's Email settings: `keep_previews`, whether a copy (subject, HTML, text) of
     * each message sent from now on is kept for its 30 days, and when and by whom it last changed.
     * `GET /v1/applications/{application}/email/settings`.
     *
     * Permission `email:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation email.settings.show
     */
    public function show(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/email/settings',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }

    /**
     * Turn on or off keeping a copy of each message (`keep_previews`); answers the settings. On,
     * every message sent from then on keeps its subject, HTML and text for its 30 days, read with
     * email.messages.preview; off, every copy is deleted at once. `PUT
     * /v1/applications/{application}/email/settings`.
     *
     * Permission `email:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $application `application` in the path
     * @param array{keep_previews: bool}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation email.settings.update
     */
    public function update(string $application, array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'PUT',
            path: '/v1/applications/'.self::segment($application, 'application').'/email/settings',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
        ));
    }
}
