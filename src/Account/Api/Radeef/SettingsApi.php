<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Radeef;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->radeef->settings`: the Account API's `radeef.settings` operations.
 */
final class SettingsApi extends AccountNamespace
{
    /**
     * Read Radeef's settings: every section (persona, pace, handoff, hours, limits, store) in the
     * shape radeef.settings.update takes, the mode, the character and the price per conversation.
     * `GET /v1/applications/{application}/radeef/settings`.
     *
     * Permission `radeef:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation radeef.settings.show
     */
    public function show(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/radeef/settings',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }

    /**
     * Save one section of Radeef's settings (`persona`, `pace`, `handoff`, `hours`, `limits` or
     * `store`), the whole section as radeef.settings.show answers it; answers the settings. `PUT
     * /v1/applications/{application}/radeef/settings/{section}`.
     *
     * Permission `radeef:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $application `application` in the path
     * @param string $section `section` in the path
     * @param array{assign_member_id?: string|null, assign_mode: string, csat: bool, custom_enabled?: bool, custom_topics?: list<mixed>, emoji: string, etiquette: bool, idle_minutes: int, intro: string, keywords?: list<mixed>, keywords_enabled?: bool, messages?: array<string, mixed>, mode: string, monthly_cap_halalas?: int|null, order_status?: bool, orders: bool, persona_name: string, persona_name_en?: string|null, products: bool, ramadan?: array<string, mixed>|null, read_receipts: bool, register: string, reply_cap: int, reply_length: string, return_after_hours?: int|null, speed: string, split: bool, strict: bool, topics: array<string, mixed>, typing: bool, week?: array<string, mixed>}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation radeef.settings.update
     */
    public function update(string $application, string $section, array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'PUT',
            path: '/v1/applications/'.self::segment($application, 'application').'/radeef/settings/'.self::segment($section, 'section'),
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, ['messages', 'ramadan', 'topics', 'week']),
        ));
    }

    /**
     * Set when Radeef answers: `hours_mode` (always, outside the team's hours, inside them) with
     * `mode` (live answers customers, draft suggests to the team); answers Radeef. `PUT
     * /v1/applications/{application}/radeef/settings/when`.
     *
     * Permission `radeef:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Sent once: retried only
     * when the request never left (or the key is still in progress). After a timeout the exception
     * carries the `Idempotency-Key`: call again with it, never a new one. After a 5xx Tawked
     * released the key: check whether it took effect before calling again.
     *
     * @param string $application `application` in the path
     * @param array{hours_mode: string, mode: string}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation radeef.settings.when
     */
    public function when(string $application, array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'PUT',
            path: '/v1/applications/'.self::segment($application, 'application').'/radeef/settings/when',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
        ));
    }
}
