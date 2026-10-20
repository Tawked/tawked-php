<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Campaigns;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountPage;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;
use Tawked\Pagination\Paginator;

/**
 * `$account->campaigns->campaigns`: the Account API's `campaigns.campaigns` operations.
 */
final class CampaignsApi extends AccountNamespace
{
    /**
     * Cancel a campaign for good: whoever has not got it yet never will (they are skipped). Answers
     * the campaign, cancelled. `POST /v1/applications/{application}/campaigns/{campaign}/cancel`.
     *
     * Permission `campaigns:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Sent once: retried only
     * when the request never left (or the key is still in progress). After a timeout the exception
     * carries the `Idempotency-Key`: call again with it, never a new one. After a 5xx Tawked
     * released the key: check whether it took effect before calling again.
     *
     * @param string $application `application` in the path
     * @param string $campaign `campaign` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation campaigns.campaigns.cancel
     */
    public function cancel(string $application, string $campaign, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/campaigns/'.self::segment($campaign, 'campaign').'/cancel',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * Save a new campaign draft (name, audience, template, mapping, schedule): nothing is checked
     * beyond its shape and nothing is sent. Answers the draft; then read
     * campaigns.campaigns.estimate and schedule it with campaigns.campaigns.schedule. `POST
     * /v1/applications/{application}/campaigns`.
     *
     * Permission `campaigns:write`. Sent once: retried only when the request never left (or the key
     * is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * @param string $application `application` in the path
     * @param array{audience?: array<string, mixed>|null, campaign?: string|null, date?: string|null, header_image_path?: string|null, intent?: string, mapping?: array<string, mixed>|null, name?: string|null, send_mode?: string, template_language?: string|null, template_name?: string|null, time?: string|null}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation campaigns.campaigns.create
     */
    public function create(string $application, array $params = [], array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/campaigns',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, ['audience', 'mapping']),
        ));
    }

    /**
     * Delete a draft (nothing was ever sent from it); answers ok. A scheduled or sent campaign is
     * cancelled instead (campaigns.campaigns.cancel): 409 not_draft. `DELETE
     * /v1/applications/{application}/campaigns/{campaign}`.
     *
     * Permission `campaigns:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Sent once: retried only
     * when the request never left (or the key is still in progress). After a timeout the exception
     * carries the `Idempotency-Key`: call again with it, never a new one. After a 5xx Tawked
     * released the key: check whether it took effect before calling again.
     *
     * @param string $application `application` in the path
     * @param string $campaign `campaign` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation campaigns.campaigns.delete
     */
    public function delete(string $application, string $campaign, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'DELETE',
            path: '/v1/applications/'.self::segment($application, 'application').'/campaigns/'.self::segment($campaign, 'campaign'),
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * Copy a campaign into a new draft with the same audience, template, mapping and image; answers
     * the new draft. `POST /v1/applications/{application}/campaigns/{campaign}/duplicate`.
     *
     * Permission `campaigns:write`. Sent once: retried only when the request never left (or the key
     * is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * @param string $application `application` in the path
     * @param string $campaign `campaign` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation campaigns.campaigns.duplicate
     */
    public function duplicate(string $application, string $campaign, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/campaigns/'.self::segment($campaign, 'campaign').'/duplicate',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * A saved campaign's estimate, nothing sent: who it reaches and who is left out (and why),
     * Meta's cost, Tawked's fee and the wallet's shortfall (for who may see the money), when it
     * would start (moved out of the quiet hours), and what is still missing. `GET
     * /v1/applications/{application}/campaigns/{campaign}/estimate`.
     *
     * Permission `campaigns:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param string $campaign `campaign` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation campaigns.campaigns.estimate
     */
    public function estimate(string $application, string $campaign, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/campaigns/'.self::segment($campaign, 'campaign').'/estimate',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }

    /**
     * List the application's WhatsApp campaigns, newest first, each with its status, audience,
     * template, when it sends and its figures (sent, delivered, read, replied, unsubscribed,
     * failed). Filter by `status`. `GET /v1/applications/{application}/campaigns`.
     *
     * Permission `campaigns:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation campaigns.campaigns.list
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
            path: '/v1/applications/'.self::segment($application, 'application').'/campaigns',
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
     * Pause a scheduled or sending campaign: nobody else gets it until it is resumed. Answers the
     * campaign. `POST /v1/applications/{application}/campaigns/{campaign}/pause`.
     *
     * Permission `campaigns:write`. Sent once: retried only when the request never left (or the key
     * is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * @param string $application `application` in the path
     * @param string $campaign `campaign` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation campaigns.campaigns.pause
     */
    public function pause(string $application, string $campaign, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/campaigns/'.self::segment($campaign, 'campaign').'/pause',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * The estimate of a campaign form not saved yet (the same fields as create), nothing written:
     * reach, exclusions, cost, start time and what is missing. `POST
     * /v1/applications/{application}/campaigns/estimate`.
     *
     * Permission `campaigns:write`. Sent once: retried only when the request never left (or the key
     * is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * @param string $application `application` in the path
     * @param array{audience?: array<string, mixed>|null, campaign?: string|null, date?: string|null, header_image_path?: string|null, intent?: string, mapping?: array<string, mixed>|null, name?: string|null, send_mode?: string, template_language?: string|null, template_name?: string|null, time?: string|null}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation campaigns.campaigns.preview_estimate
     */
    public function previewEstimate(string $application, array $params = [], array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/campaigns/estimate',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, ['audience', 'mapping']),
        ));
    }

    /**
     * Resume a paused campaign: sending goes on from where it stopped (within the quiet hours and
     * the wallet). Answers the campaign. `POST
     * /v1/applications/{application}/campaigns/{campaign}/resume`.
     *
     * Permission `campaigns:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Sent once: retried only
     * when the request never left (or the key is still in progress). After a timeout the exception
     * carries the `Idempotency-Key`: call again with it, never a new one. After a 5xx Tawked
     * released the key: check whether it took effect before calling again.
     *
     * @param string $application `application` in the path
     * @param string $campaign `campaign` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation campaigns.campaigns.resume
     */
    public function resume(string $application, string $campaign, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/campaigns/'.self::segment($campaign, 'campaign').'/resume',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * Schedule a draft to send: everything is checked, its recipients fixed (subscribed contacts,
     * minus who stopped or already got the day's marketing message), its start moved out of the
     * quiet hours; one due now starts at once. Answers the campaign, scheduled or sending, and
     * `next`: read campaigns.campaigns.show every few minutes for its results. `POST
     * /v1/applications/{application}/campaigns/{campaign}/schedule`.
     *
     * Permission `campaigns:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Sent once: retried only
     * when the request never left (or the key is still in progress). After a timeout the exception
     * carries the `Idempotency-Key`: call again with it, never a new one. After a 5xx Tawked
     * released the key: check whether it took effect before calling again.
     *
     * @param string $application `application` in the path
     * @param string $campaign `campaign` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation campaigns.campaigns.schedule
     */
    public function schedule(string $application, string $campaign, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/campaigns/'.self::segment($campaign, 'campaign').'/schedule',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * Send the campaign's template with its mapping to your own Saudi mobile (the token's person),
     * as a test: not a recipient, no Tawked fee, not the day's marketing message (Meta bills the
     * message). Answers ok; then campaigns.campaigns.estimate and campaigns.campaigns.schedule.
     * `POST /v1/applications/{application}/campaigns/test`.
     *
     * Permission `campaigns:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Sent once: retried only
     * when the request never left (or the key is still in progress). After a timeout the exception
     * carries the `Idempotency-Key`: call again with it, never a new one. After a 5xx Tawked
     * released the key: check whether it took effect before calling again.
     *
     * @param string $application `application` in the path
     * @param array{audience?: array<string, mixed>|null, campaign?: string|null, date?: string|null, header_image_path?: string|null, intent?: string, mapping?: array<string, mixed>|null, name?: string|null, send_mode?: string, template_language?: string|null, template_name?: string|null, time?: string|null}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation campaigns.campaigns.send_test
     */
    public function sendTest(string $application, array $params = [], array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/campaigns/test',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, ['audience', 'mapping']),
        ));
    }

    /**
     * One campaign: a draft as written (audience, template, mapping, image, schedule), or a
     * scheduled or sent one with its results: the funnel (recipients, sent, delivered, read,
     * replied, unsubscribed, failed, skipped, queued), why some failed or were skipped, the buttons
     * tapped; and `next` from its status (none once completed or cancelled). `GET
     * /v1/applications/{application}/campaigns/{campaign}`.
     *
     * Permission `campaigns:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param string $campaign `campaign` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation campaigns.campaigns.show
     */
    public function show(string $application, string $campaign, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/campaigns/'.self::segment($campaign, 'campaign'),
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }

    /**
     * Replace a draft's fields (the whole form, as on create); answers the draft. Only a draft
     * changes: 409 not_draft once it is scheduled. `PUT
     * /v1/applications/{application}/campaigns/{campaign}`.
     *
     * Permission `campaigns:write`. Sent once: retried only when the request never left (or the key
     * is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * @param string $application `application` in the path
     * @param string $campaign `campaign` in the path
     * @param array{audience?: array<string, mixed>|null, campaign?: string|null, date?: string|null, header_image_path?: string|null, intent?: string, mapping?: array<string, mixed>|null, name?: string|null, send_mode?: string, template_language?: string|null, template_name?: string|null, time?: string|null}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation campaigns.campaigns.update
     */
    public function update(string $application, string $campaign, array $params = [], array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'PUT',
            path: '/v1/applications/'.self::segment($application, 'application').'/campaigns/'.self::segment($campaign, 'campaign'),
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, ['audience', 'mapping']),
        ));
    }
}
