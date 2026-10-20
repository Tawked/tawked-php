<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Radeef;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->radeef->notes`: the Account API's `radeef.notes` operations.
 */
final class NotesApi extends AccountNamespace
{
    /**
     * Teach Radeef something new: a line added to a topic (`as: topic`, `topic`, `text`) or a
     * question and its answer (`as: faq`, `question`, `answer`, optional `topic`); answers what was
     * saved. `POST /v1/applications/{application}/radeef/knowledge/notes`.
     *
     * Permission `radeef:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $application `application` in the path
     * @param array{answer?: string|null, as: string, question?: string|null, text?: string|null, topic?: string}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation radeef.notes.create
     */
    public function create(string $application, array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/radeef/knowledge/notes',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
        ));
    }

    /**
     * Before teaching Radeef a line (`text`), read which topic it belongs to; or check a link
     * (`url`) is on the business's own site and can be read. Saves nothing. `POST
     * /v1/applications/{application}/radeef/knowledge/notes/guess`.
     *
     * Permission `radeef:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $application `application` in the path
     * @param array{text?: string|null, url?: string|null}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation radeef.notes.guess
     */
    public function guess(string $application, array $params = [], array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/radeef/knowledge/notes/guess',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
        ));
    }
}
