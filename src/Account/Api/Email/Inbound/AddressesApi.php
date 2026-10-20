<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Email\Inbound;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->email->inbound->addresses`: the Account API's `email.inbound.addresses` operations.
 */
final class AddressesApi extends AccountNamespace
{
    /**
     * Read the address's MX record now (as the console's Check now button does); answers the
     * address with its state and `next`. Poll it every few minutes after adding the record until
     * `state` is active. `POST
     * /v1/applications/{application}/email/inbound-addresses/{address}/check`.
     *
     * Permission `email:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $application `application` in the path
     * @param string $address `address` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation email.inbound.addresses.check
     */
    public function check(string $application, string $address, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/email/inbound-addresses/'.self::segment($address, 'address').'/check',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * Remove a receiving address: email sent to it is refused from then on; its received emails
     * stay until their 30 days end. Answers ok. `DELETE
     * /v1/applications/{application}/email/inbound-addresses/{address}`.
     *
     * Permission `email:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Sent once: retried only
     * when the request never left (or the key is still in progress). After a timeout the exception
     * carries the `Idempotency-Key`: call again with it, never a new one. After a 5xx Tawked
     * released the key: check whether it took effect before calling again.
     *
     * @param string $application `application` in the path
     * @param string $address `address` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation email.inbound.addresses.destroy
     */
    public function destroy(string $application, string $address, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'DELETE',
            path: '/v1/applications/'.self::segment($application, 'application').'/email/inbound-addresses/'.self::segment($address, 'address'),
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * The application's receiving addresses: each one's kind (`hosted`, a Tawked address ready at
     * once; `domain`, on a verified sending domain), its domain (every local part at it reaches the
     * application, e.g. orders@ and support@), its state, its hold, today's count and where it
     * forwards. `GET /v1/applications/{application}/email/inbound-addresses`.
     *
     * Permission `email:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation email.inbound.addresses.index
     */
    public function index(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/email/inbound-addresses',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }

    /**
     * One receiving address: its state, the MX record to add for an address on your domain
     * (`record`), the other MX hosts found, its hold, spam policy, automatic forward, test message
     * and `next`. `GET /v1/applications/{application}/email/inbound-addresses/{address}`.
     *
     * Permission `email:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param string $address `address` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation email.inbound.addresses.show
     */
    public function show(string $application, string $address, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/email/inbound-addresses/'.self::segment($address, 'address'),
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }

    /**
     * Create a receiving address: `kind` hosted (a Tawked address, `slug` optional:
     * `<slug>.inbound.tawked.com`, ready at once) or domain (`domain_id` a verified sending domain,
     * `label` optional, default inbound: one MX record to add). Answers the address and `next`.
     * `POST /v1/applications/{application}/email/inbound-addresses`.
     *
     * Permission `email:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Sent once: retried only
     * when the request never left (or the key is still in progress). After a timeout the exception
     * carries the `Idempotency-Key`: call again with it, never a new one. After a 5xx Tawked
     * released the key: check whether it took effect before calling again.
     *
     * @param string $application `application` in the path
     * @param array{domain_id?: string|null, kind: string, label?: string|null, slug?: string|null}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation email.inbound.addresses.store
     */
    public function store(string $application, array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/email/inbound-addresses',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
        ));
    }

    /**
     * Send a free test email from Tawked to the address, to walk the whole path (the MX record, the
     * receiving, the webhook); it shows in email.received.index within a minute. Answers ok and
     * `next`. `POST /v1/applications/{application}/email/inbound-addresses/{address}/test`.
     *
     * Permission `email:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $application `application` in the path
     * @param string $address `address` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation email.inbound.addresses.test
     */
    public function test(string $application, string $address, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/email/inbound-addresses/'.self::segment($address, 'address').'/test',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * Change one thing of a receiving address: `state` on or off, `spam_policy` keep or drop, or
     * its automatic forward (`forward_to` one of your confirmed addresses or any address on a
     * verified domain of the application, `forward_from_local`; `forward_to` null stops it).
     * Answers the address. `PUT /v1/applications/{application}/email/inbound-addresses/{address}`.
     *
     * Permission `email:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Sent once: retried only
     * when the request never left (or the key is still in progress). After a timeout the exception
     * carries the `Idempotency-Key`: call again with it, never a new one. After a 5xx Tawked
     * released the key: check whether it took effect before calling again.
     *
     * @param string $application `application` in the path
     * @param string $address `address` in the path
     * @param array{forward_from_local?: string|null, forward_to?: string|null, spam_policy?: string, state?: string}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation email.inbound.addresses.update
     */
    public function update(string $application, string $address, array $params = [], array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'PUT',
            path: '/v1/applications/'.self::segment($application, 'application').'/email/inbound-addresses/'.self::segment($address, 'address'),
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
        ));
    }
}
