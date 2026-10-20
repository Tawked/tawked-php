<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Email;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountPage;
use Tawked\Account\AccountResponse;
use Tawked\Account\Api\Email\Senders\TrackingApi;
use Tawked\Http\Transport;
use Tawked\Pagination\Paginator;

/**
 * `$account->email->senders`: the Account API's `email.senders` operations.
 */
final class SendersApi extends AccountNamespace
{
    public readonly TrackingApi $tracking;

    public function __construct(Transport $transport)
    {
        parent::__construct($transport);
        $this->tracking = new TrackingApi($transport);
    }

    /**
     * Cancel a DKIM rotation that has not switched (waiting for its new record, or failed): signing
     * stays on the current key, and the new record may be deleted. Answers the domain. `DELETE
     * /v1/applications/{application}/email/senders/{domain}/dkim-rotation`.
     *
     * Permission `email:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $application `application` in the path
     * @param string $domain `domain` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation email.senders.cancel_dkim_rotation
     */
    public function cancelDkimRotation(string $application, string $domain, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'DELETE',
            path: '/v1/applications/'.self::segment($application, 'application').'/email/senders/'.self::segment($domain, 'domain').'/dkim-rotation',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * Check the domain's DNS records now (as the console's Check now button does); answers the
     * domain with its state, each record's state and `next`, and reads the return path's records
     * (custom MAIL FROM, SPF) and the DMARC record again into `authentication`. Poll it every one
     * to two minutes after adding the records until `state` is verified; it is the one way to read
     * DMARC anew. `POST /v1/applications/{application}/email/senders/{domain}/check`.
     *
     * Permission `email:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $application `application` in the path
     * @param string $domain `domain` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation email.senders.check
     */
    public function check(string $application, string $domain, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/email/senders/'.self::segment($domain, 'domain').'/check',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * A link the person opens to add the domain's DNS record at their DNS host automatically
     * (Domain Connect, e.g. Cloudflare): they sign in at the host and approve, then return to the
     * domain's page in the console. Answers `url`, the `human_action` to relay and `next`: once the
     * person says it is done, poll `email.senders.check`. `POST
     * /v1/applications/{application}/email/senders/{domain}/connect`.
     *
     * Permission `email:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $application `application` in the path
     * @param string $domain `domain` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation email.senders.connect
     */
    public function connect(string $application, string $domain, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/email/senders/'.self::segment($domain, 'domain').'/connect',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * Add a sending domain (e.g. shop.example.com) to the application; answers the domain with the
     * DNS records to add at its DNS host and `next`. Add them (with the host's own tools or API),
     * then poll `email.senders.check` every one to two minutes until `state` is `verified`. `POST
     * /v1/applications/{application}/email/senders`.
     *
     * Permission `email:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $application `application` in the path
     * @param array{domain: string}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation email.senders.create
     */
    public function create(string $application, array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/email/senders',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
        ));
    }

    /**
     * Remove a sender domain from the application: its sends stop at once (its messages stay in the
     * log); answers ok. `DELETE /v1/applications/{application}/email/senders/{domain}`.
     *
     * Permission `email:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Sent once: retried only
     * when the request never left (or the key is still in progress). After a timeout the exception
     * carries the `Idempotency-Key`: call again with it, never a new one. After a 5xx Tawked
     * released the key: check whether it took effect before calling again.
     *
     * @param string $application `application` in the path
     * @param string $domain `domain` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation email.senders.delete
     */
    public function delete(string $application, string $domain, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'DELETE',
            path: '/v1/applications/'.self::segment($application, 'application').'/email/senders/'.self::segment($domain, 'domain'),
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * The application's sender domains, newest first, each with its state (`pending`, `verified`,
     * `failed`), the DNS records to add and their state, the DNS host when known, the default from
     * address and the messages of the last 30 days. `GET
     * /v1/applications/{application}/email/senders`.
     *
     * Permission `email:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation email.senders.list
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
            path: '/v1/applications/'.self::segment($application, 'application').'/email/senders',
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
     * Try a failed DKIM rotation again once its new record is in place at the DNS host: answers the
     * domain with the rotation publishing again. `POST
     * /v1/applications/{application}/email/senders/{domain}/dkim-rotation/retry`.
     *
     * Permission `email:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $application `application` in the path
     * @param string $domain `domain` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation email.senders.retry_dkim_rotation
     */
    public function retryDkimRotation(string $application, string $domain, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/email/senders/'.self::segment($domain, 'domain').'/dkim-rotation/retry',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * Start rotating a verified domain's DKIM key: answers the domain with
     * `authentication.dkim.rotation.new_record`, a second TXT record to add beside the current one.
     * Tawked switches signing to it by itself once it reads it, then the old record is deleted at
     * the DNS host (`old_record`). `POST
     * /v1/applications/{application}/email/senders/{domain}/dkim-rotation`.
     *
     * Permission `email:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $application `application` in the path
     * @param string $domain `domain` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation email.senders.rotate_dkim
     */
    public function rotateDkim(string $application, string $domain, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/email/senders/'.self::segment($domain, 'domain').'/dkim-rotation',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * Send a free test e-mail from a verified domain to one of the person's own confirmed addresses
     * (`to`, one of `email.test_address.show`'s `recipients`); answers the message; poll
     * `email.messages.show` for its delivery. `POST
     * /v1/applications/{application}/email/senders/{domain}/test`.
     *
     * Permission `email:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $application `application` in the path
     * @param string $domain `domain` in the path
     * @param array{to: string}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation email.senders.send_test
     */
    public function sendTest(string $application, string $domain, array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/email/senders/'.self::segment($domain, 'domain').'/test',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
        ));
    }

    /**
     * Name the domain's return path (custom MAIL FROM, `label` before the domain: `send` makes
     * send.example.com) or turn it on again with its label; answers the domain with
     * `authentication.mail_from` and its two records (an MX and an SPF TXT) to add at the DNS host.
     * Optional: it makes SPF align with the domain and never affects verification or sending. `PUT
     * /v1/applications/{application}/email/senders/{domain}/mail-from`.
     *
     * Permission `email:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $application `application` in the path
     * @param string $domain `domain` in the path
     * @param array{label: string}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation email.senders.set_mail_from
     */
    public function setMailFrom(string $application, string $domain, array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'PUT',
            path: '/v1/applications/'.self::segment($application, 'application').'/email/senders/'.self::segment($domain, 'domain').'/mail-from',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
        ));
    }

    /**
     * One sender domain: its state, the DNS records to add at its host (name, value, the host's
     * Name field, each record's state), how long a pending domain still waits, the default from
     * address and its messages of the last 30 days, day by day; and its `authentication`: the DKIM
     * key and its rotation (`dkim.rotation.state`), the return path (custom MAIL FROM, SPF
     * alignment) with its records, the DMARC record and policy as last read, and the Feedback-IDs.
     * `GET /v1/applications/{application}/email/senders/{domain}`.
     *
     * Permission `email:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param string $domain `domain` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation email.senders.show
     */
    public function show(string $application, string $domain, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/email/senders/'.self::segment($domain, 'domain'),
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }

    /**
     * Turn the domain's return path off: its mail goes out with the default return path again (SPF
     * no longer aligns with the domain); the label is kept for email.senders.set_mail_from. Answers
     * the domain. `DELETE /v1/applications/{application}/email/senders/{domain}/mail-from`.
     *
     * Permission `email:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $application `application` in the path
     * @param string $domain `domain` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation email.senders.unset_mail_from
     */
    public function unsetMailFrom(string $application, string $domain, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'DELETE',
            path: '/v1/applications/'.self::segment($application, 'application').'/email/senders/'.self::segment($domain, 'domain').'/mail-from',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * Set a verified domain's defaults: the name a message is from (`from_name`, may be empty) and
     * the part before the @ (`from_local`, e.g. no-reply) used when a send names no `from`; answers
     * the domain. `PUT /v1/applications/{application}/email/senders/{domain}`.
     *
     * Permission `email:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $application `application` in the path
     * @param string $domain `domain` in the path
     * @param array{from_local: string, from_name?: string|null}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation email.senders.update
     */
    public function update(string $application, string $domain, array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'PUT',
            path: '/v1/applications/'.self::segment($application, 'application').'/email/senders/'.self::segment($domain, 'domain'),
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
        ));
    }
}
