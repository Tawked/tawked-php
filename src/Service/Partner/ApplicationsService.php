<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Service\Partner;

use Tawked\Http\Transport;
use Tawked\Model\Partner\Application;
use Tawked\Model\Partner\ApplicationHistory;
use Tawked\Model\Partner\ApplicationList;
use Tawked\Model\Partner\ApplicationStateChange;
use Tawked\Model\Partner\ApplicationSummary;
use Tawked\Model\Partner\BatchResult;
use Tawked\Model\Partner\VerifySettingsResult;
use Tawked\Pagination\Paginator;
use Tawked\Service\Service;

/**
 * `$tawked->partner->applications`: the Partner API (partner keys only).
 */
final class ApplicationsService extends Service
{
    /**
     * Provision an application. `POST /v1/partner/applications`.
     *
     * Creates an application for one of your merchants under your own id for it, screens its names,
     * and either queues it for review or activates it at once, according to your approval mode.
     * Idempotent on `external_id`: provisioning the same id again answers `200` with the
     * application as it stands and applies nothing from the new body, so an install that runs twice
     * is safe.
     *
     * Sent once: retried only when the request never left (or the key is still in progress). After
     * a timeout the exception carries the `Idempotency-Key`: call again with it, never a new one.
     * After a 5xx Tawked released the key: check whether it took effect before calling again.
     *
     * Errors: idempotency_in_progress, idempotency_key_reused, internal_error, invalid_json,
     * invalid_request, ip_not_allowed, key_expired, live_key_required, partner_suspended,
     * too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->partner->applications->create(['external_id' => 'store_88', 'name_ar' => 'متجر الورد', 'name_en' => 'Flower Shop', 'website' => 'https://flowers.example', ...]);
     *
     * @param array{external_id: string, name_ar: string, name_en: string, website: string, industry?: string, merchant?: array<string, mixed>, verify?: array<string, mixed>, metadata?: array<string, mixed>}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint partner-provision
     */
    public function create(array $params, array $options = []): ApplicationSummary
    {
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/partner/applications',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, ['merchant', 'verify', 'metadata']),
        );

        return ApplicationSummary::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Provision in bulk. `POST /v1/partner/applications/batch`.
     *
     * Up to 100 provisioning bodies in one call, for a migration or a sync. Items run in order, one
     * at a time, each exactly like a single provision (screened, queued or activated, its own
     * events); a refused item never stops the rest. Always `200` with the counts and one result per
     * item in the same order: `created`, `existing` (the id was already yours; nothing applied from
     * the item) or `refused` (`fields` names the refused ones, `application` is `null`). The same
     * `external_id` twice in a batch is `created` then `existing`.
     *
     * Sent once: retried only when the request never left (or the key is still in progress). After
     * a timeout the exception carries the `Idempotency-Key`: call again with it, never a new one.
     * After a 5xx Tawked released the key: check whether it took effect before calling again.
     *
     * Errors: idempotency_in_progress, idempotency_key_reused, internal_error, invalid_json,
     * invalid_request, ip_not_allowed, key_expired, live_key_required, partner_suspended,
     * too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->partner->applications->batch([...]);
     *
     * @param array<int, array<string, mixed>> $applications `applications`, one item each (at most 100, refused before sending)
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint partner-batch
     */
    public function batch(array $applications, array $options = []): BatchResult
    {
        self::assertListSize($applications, 'applications', 1, 100);
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/partner/applications/batch',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 120.0,
            json: ['applications' => array_values($applications)],
        );

        return BatchResult::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * List applications. `GET /v1/partner/applications`.
     *
     * Lists the applications you provisioned, newest first, each with the same fields as a single
     * read. Filter by review status and by your own off switch, and page with `limit` and `offset`;
     * `total` counts the matches before paging.
     *
     * Retried after a network failure, a timeout or a 5xx (a read).
     *
     * Errors: internal_error, invalid_request, ip_not_allowed, key_expired, too_many_requests,
     * unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->partner->applications->list(['status' => 'active', 'limit' => 50, 'offset' => 0]);
     *
     * @param array{status?: string, disabled?: bool, archived?: bool, metadata?: array<string, mixed>, limit?: int, offset?: int}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint partner-list
     */
    public function list(array $params = [], array $options = []): ApplicationList
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/partner/applications',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
            query: $params,
        );

        return ApplicationList::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Every item of `list()`, page after page (by `offset` until `total`): the same filters, and `limit` for the page size.
     *
     * @param array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @return \Generator<int, \Tawked\Model\Partner\Application>
     */
    public function iterate(array $params = [], array $options = []): \Generator
    {
        return Paginator::offset(fn(array $page): ApplicationList => $this->list($page, $options), $params, 'offset');
    }

    /**
     * Get an application. `GET /v1/partner/applications/{external_id}`.
     *
     * Reads one application by your own id for it: its review status, the screening verdict, the
     * verify settings in force, your off switch (`disabled`) and the protection guard's pause
     * (`paused`).
     *
     * Retried after a network failure, a timeout or a 5xx (a read).
     *
     * Errors: internal_error, ip_not_allowed, key_expired, not_found, too_many_requests,
     * unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->partner->applications->get('store_88');
     *
     * @param string $externalId `external_id` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint partner-show
     */
    public function get(string $externalId, array $options = []): Application
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/partner/applications/'.self::segment($externalId, 'external_id'),
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        );

        return Application::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Get the history. `GET /v1/partner/applications/{external_id}/history`.
     *
     * The review history the console shows, newest first, up to 100 events: provisioned, submitted,
     * approve, reject, suspend, resume, brand_changed, settings_changed, renamed. `actor` is a kind
     * (`partner`, `tawked`, `system`, `client`), never a person; `reason` carries a rejection
     * reason or what a brand change replaced; `screening` is the verdict at that moment.
     *
     * Retried after a network failure, a timeout or a 5xx (a read).
     *
     * Errors: internal_error, ip_not_allowed, key_expired, not_found, too_many_requests,
     * unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->partner->applications->history('store_88');
     *
     * @param string $externalId `external_id` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint partner-history
     */
    public function history(string $externalId, array $options = []): ApplicationHistory
    {
        $response = $this->transport->send(
            method: 'GET',
            path: '/v1/partner/applications/'.self::segment($externalId, 'external_id').'/history',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        );

        return ApplicationHistory::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Update the brand. `PATCH /v1/partner/applications/{external_id}`.
     *
     * Replaces the brand in full: `name_ar`, `name_en` and `website` are required on every call;
     * `industry`, `merchant` and `verify` are optional. The names are screened again, and a change
     * to a name or the website returns an active application to review until it is approved again,
     * while a call that changes none of the three keeps the status. Codes refuse with `403
     * account_not_active` while it is in review.
     *
     * Retried after a network failure, a timeout or a 5xx under the same `Idempotency-Key` (minted
     * when you pass none): the server keeps the key through a failure.
     *
     * Errors: application_archived, idempotency_in_progress, idempotency_key_reused,
     * internal_error, invalid_json, invalid_request, ip_not_allowed, key_expired,
     * live_key_required, not_found, partner_suspended, too_many_requests, unauthorized
     * (Tawked\ErrorCode).
     *
     * @example $tawked->partner->applications->update('store_88', ['name_ar' => 'متجر الورد', 'name_en' => 'Flower Shop', 'website' => 'https://flowers.example']);
     *
     * @param string $externalId `external_id` in the path
     * @param array{name_ar: string, name_en: string, website: string, industry?: string, merchant?: array<string, mixed>, verify?: array<string, mixed>, metadata?: array<string, mixed>}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint partner-update
     */
    public function update(string $externalId, array $params, array $options = []): ApplicationSummary
    {
        $response = $this->transport->send(
            method: 'PATCH',
            path: '/v1/partner/applications/'.self::segment($externalId, 'external_id'),
            options: $options,
            retry: Transport::IDEMPOTENT,
            timeout: 30.0,
            json: self::body($params, ['merchant', 'verify', 'metadata']),
        );

        return ApplicationSummary::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Upload the logo. `POST /v1/partner/applications/{external_id}/logo`.
     *
     * Sets the brand logo from a multipart upload: a PNG or a JPEG of at most 262144 bytes,
     * recognised by its bytes and never by its name. The logo never reaches a message, so a new one
     * is not a review: a live application keeps its status (a rejected one returns to review, since
     * its rejection may be the logo). The answer is the full row with the new `logo_url`.
     *
     * Retried after a network failure, a timeout or a 5xx under the same `Idempotency-Key` (minted
     * when you pass none): the server keeps the key through a failure.
     *
     * Errors: application_archived, idempotency_in_progress, idempotency_key_reused,
     * internal_error, invalid_request, ip_not_allowed, key_expired, live_key_required, not_found,
     * partner_suspended, storage_unavailable, too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->partner->applications->uploadLogo('store_88', []);
     *
     * @param string $externalId `external_id` in the path
     * @param array{logo: \Tawked\Attachment|\SplFileInfo|\Psr\Http\Message\StreamInterface|resource|string}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint partner-logo-upload
     */
    public function uploadLogo(string $externalId, array $params, array $options = []): Application
    {
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/partner/applications/'.self::segment($externalId, 'external_id').'/logo',
            options: $options,
            retry: Transport::IDEMPOTENT,
            timeout: 30.0,
            multipart: $params,
            files: ['logo'],
        );

        return Application::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Remove the logo. `DELETE /v1/partner/applications/{external_id}/logo`.
     *
     * Drops the brand logo. A removal is not a brand change: the status stays as it is. The answer
     * is the full row with `logo_url` null.
     *
     * Retried after a network failure, a timeout or a 5xx under the same `Idempotency-Key` (minted
     * when you pass none): the server keeps the key through a failure.
     *
     * Errors: application_archived, idempotency_in_progress, idempotency_key_reused,
     * internal_error, ip_not_allowed, key_expired, live_key_required, not_found, partner_suspended,
     * too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->partner->applications->removeLogo('store_88');
     *
     * @param string $externalId `external_id` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint partner-logo-remove
     */
    public function removeLogo(string $externalId, array $options = []): Application
    {
        $response = $this->transport->send(
            method: 'DELETE',
            path: '/v1/partner/applications/'.self::segment($externalId, 'external_id').'/logo',
            options: $options,
            retry: Transport::IDEMPOTENT,
            timeout: 30.0,
        );

        return Application::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Update the verify settings. `PATCH /v1/partner/applications/{external_id}/verify-settings`.
     *
     * Changes any subset of the application's verify settings. Send the fields at the top level of
     * the body or nested under `verify`; omitted fields stay as they are, a value out of range
     * refuses the whole request with the offending names in `fields`, an empty or unrecognised
     * patch is refused with `fields: ["verify"]`, and nothing here re-screens the names or changes
     * the status. The answer is every setting in force after the change.
     *
     * Retried after a network failure, a timeout or a 5xx under the same `Idempotency-Key` (minted
     * when you pass none): the server keeps the key through a failure.
     *
     * Errors: application_archived, idempotency_in_progress, idempotency_key_reused,
     * internal_error, invalid_json, invalid_request, ip_not_allowed, key_expired,
     * live_key_required, not_found, partner_suspended, too_many_requests, unauthorized
     * (Tawked\ErrorCode).
     *
     * @example $tawked->partner->applications->updateVerifySettings('store_88', ['code_length' => 6, 'ttl_seconds' => 300]);
     *
     * @param string $externalId `external_id` in the path
     * @param array{code_length?: int, ttl_seconds?: int, max_attempts?: int, max_per_hour?: int, autofill_domain_enabled?: bool, android_app_hash?: string|null, daily_spend_cap_halalas?: int|null, max_per_ip_per_hour?: int, max_new_destinations_per_hour?: int}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint partner-verify-settings
     */
    public function updateVerifySettings(string $externalId, array $params = [], array $options = []): VerifySettingsResult
    {
        $response = $this->transport->send(
            method: 'PATCH',
            path: '/v1/partner/applications/'.self::segment($externalId, 'external_id').'/verify-settings',
            options: $options,
            retry: Transport::IDEMPOTENT,
            timeout: 30.0,
            json: self::body($params, []),
        );

        return VerifySettingsResult::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Suspend an application. `POST /v1/partner/applications/{external_id}/suspend`.
     *
     * Your own off switch: every send for the application answers `403 account_not_active` until
     * you resume it, and the list reports it as `disabled: true`. The review status is untouched,
     * so resuming needs no new review. Fires `service.suspended`; answers the same when it was
     * already suspended.
     *
     * Retried after a network failure, a timeout or a 5xx under the same `Idempotency-Key` (minted
     * when you pass none): the server keeps the key through a failure.
     *
     * Errors: application_archived, idempotency_in_progress, idempotency_key_reused,
     * internal_error, ip_not_allowed, key_expired, live_key_required, not_found, partner_suspended,
     * too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->partner->applications->suspend('store_88');
     *
     * @param string $externalId `external_id` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint partner-suspend
     */
    public function suspend(string $externalId, array $options = []): ApplicationStateChange
    {
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/partner/applications/'.self::segment($externalId, 'external_id').'/suspend',
            options: $options,
            retry: Transport::IDEMPOTENT,
            timeout: 30.0,
        );

        return ApplicationStateChange::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Resume an application. `POST /v1/partner/applications/{external_id}/resume`.
     *
     * Lifts your own suspension. An application that was approved before comes back sending at
     * once; one still in review stays in review. Nothing fires on resume.
     *
     * Retried after a network failure, a timeout or a 5xx under the same `Idempotency-Key` (minted
     * when you pass none): the server keeps the key through a failure.
     *
     * Errors: application_archived, idempotency_in_progress, idempotency_key_reused,
     * internal_error, ip_not_allowed, key_expired, live_key_required, not_found, partner_suspended,
     * too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->partner->applications->resume('store_88');
     *
     * @param string $externalId `external_id` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint partner-resume
     */
    public function resume(string $externalId, array $options = []): ApplicationStateChange
    {
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/partner/applications/'.self::segment($externalId, 'external_id').'/resume',
            options: $options,
            retry: Transport::IDEMPOTENT,
            timeout: 30.0,
        );

        return ApplicationStateChange::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Unpause an application. `POST /v1/partner/applications/{external_id}/unpause`.
     *
     * Lifts an automatic pause by the protection guard after a spike in distinct new destinations
     * (`service.paused`, `paused: true` on the application, `403 application_paused` on every
     * code). Distinct from suspend and resume, which are your own switch. Answers the same when
     * nothing was paused.
     *
     * Retried after a network failure, a timeout or a 5xx under the same `Idempotency-Key` (minted
     * when you pass none): the server keeps the key through a failure.
     *
     * Errors: application_archived, idempotency_in_progress, idempotency_key_reused,
     * internal_error, ip_not_allowed, key_expired, live_key_required, not_found, partner_suspended,
     * too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->partner->applications->unpause('store_88');
     *
     * @param string $externalId `external_id` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint partner-unpause
     */
    public function unpause(string $externalId, array $options = []): ApplicationStateChange
    {
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/partner/applications/'.self::segment($externalId, 'external_id').'/unpause',
            options: $options,
            retry: Transport::IDEMPOTENT,
            timeout: 30.0,
        );

        return ApplicationStateChange::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Archive an application. `POST /v1/partner/applications/{external_id}/archive`.
     *
     * Your delete, reversible: the application leaves the list, the counts and the review queues,
     * every send answers `403 account_not_active`, every other write answers `409
     * application_archived`, and the history, the usage and the ledger stay. Refused while a
     * WhatsApp number row exists in any state (`409 number_connected`). Provisioning the same
     * `external_id` again restores it. Answers the same when already archived.
     *
     * Retried after a network failure, a timeout or a 5xx under the same `Idempotency-Key` (minted
     * when you pass none): the server keeps the key through a failure.
     *
     * Errors: idempotency_in_progress, idempotency_key_reused, internal_error, ip_not_allowed,
     * key_expired, live_key_required, not_found, number_connected, partner_suspended,
     * too_many_requests, unauthorized (Tawked\ErrorCode).
     *
     * @example $tawked->partner->applications->archive('store_88');
     *
     * @param string $externalId `external_id` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint partner-archive
     */
    public function archive(string $externalId, array $options = []): Application
    {
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/partner/applications/'.self::segment($externalId, 'external_id').'/archive',
            options: $options,
            retry: Transport::IDEMPOTENT,
            timeout: 30.0,
        );

        return Application::fromArray($response->body)->withMeta($response->meta);
    }

    /**
     * Restore an application. `POST /v1/partner/applications/{external_id}/restore`.
     *
     * Back from the archive exactly as it was: the status, your switch, the pause and the settings
     * are untouched, and sends work again if they did before. Answers the same when it was not
     * archived.
     *
     * Retried after a network failure, a timeout or a 5xx under the same `Idempotency-Key` (minted
     * when you pass none): the server keeps the key through a failure.
     *
     * Errors: idempotency_in_progress, idempotency_key_reused, internal_error, ip_not_allowed,
     * key_expired, live_key_required, not_found, partner_suspended, too_many_requests, unauthorized
     * (Tawked\ErrorCode).
     *
     * @example $tawked->partner->applications->restore('store_88');
     *
     * @param string $externalId `external_id` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-endpoint partner-restore
     */
    public function restore(string $externalId, array $options = []): Application
    {
        $response = $this->transport->send(
            method: 'POST',
            path: '/v1/partner/applications/'.self::segment($externalId, 'external_id').'/restore',
            options: $options,
            retry: Transport::IDEMPOTENT,
            timeout: 30.0,
        );

        return Application::fromArray($response->body)->withMeta($response->meta);
    }
}
