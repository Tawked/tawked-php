<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Chat;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountPage;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;
use Tawked\Pagination\Paginator;

/**
 * `$account->chat->team`: the Account API's `chat.team` operations.
 */
final class TeamApi extends AccountNamespace
{
    /**
     * Add a person to this application's support team: someone of the account by `member_id`, or
     * someone new by `name` with a `phone` (a Saudi mobile, 05… or +9665…), an `email`, or
     * both, and a `role` (`agent` or `administrator`). Someone new with an email signs in with it
     * and Tawked e-mails them the invitation (`invitation_sent_to`); with a mobile, answers
     * `sign_in_message` for whoever added them to send, written in your person's language or
     * `language` (ar or en). `POST /v1/applications/{application}/chat/team`.
     *
     * Permission `chat:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Sent once: retried only
     * when the request never left (or the key is still in progress). After a timeout the exception
     * carries the `Idempotency-Key`: call again with it, never a new one. After a 5xx Tawked
     * released the key: check whether it took effect before calling again.
     *
     * @param string $application `application` in the path
     * @param array{email?: string|null, language?: string|null, member_id?: string|null, name?: string, phone?: string|null, role: string}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation chat.team.add
     */
    public function add(string $application, array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/chat/team',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
        ));
    }

    /**
     * Who could join this support team without a new seat (the account's people not on it yet,
     * mobiles masked), and the account's seats: the limit, how many are used, how many are left.
     * `GET /v1/applications/{application}/chat/team/candidates`.
     *
     * Permission `chat:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation chat.team.candidates
     */
    public function candidates(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/chat/team/candidates',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }

    /**
     * E-mail the invitation again to a person of this support team who signs in with an email: one
     * a minute and five a day per person, the one sent at the add included. Answers ok. `POST
     * /v1/applications/{application}/chat/team/{member}/invite`.
     *
     * Permission `chat:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $application `application` in the path
     * @param string $member `member` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation chat.team.invite
     */
    public function invite(string $application, string $member, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/chat/team/'.self::segment($member, 'member').'/invite',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * List who answers this application's customers in Tawked Chat: the owner, the supervisors,
     * then the agents of this number, each with their role, mobile (never the owner's) and whether
     * you may change them. `GET /v1/applications/{application}/chat/team`.
     *
     * Permission `chat:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation chat.team.list
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
            path: '/v1/applications/'.self::segment($application, 'application').'/chat/team',
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
     * Take a person off this application's support team: an agent loses this number's conversations
     * and keeps the others; a supervisor leaves Tawked Chat as a whole. Someone left with no role
     * at all is removed from the account. Answers ok. `DELETE
     * /v1/applications/{application}/chat/team/{member}`.
     *
     * Permission `chat:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Sent once: retried only
     * when the request never left (or the key is still in progress). After a timeout the exception
     * carries the `Idempotency-Key`: call again with it, never a new one. After a 5xx Tawked
     * released the key: check whether it took effect before calling again.
     *
     * @param string $application `application` in the path
     * @param string $member `member` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation chat.team.remove
     */
    public function remove(string $application, string $member, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'DELETE',
            path: '/v1/applications/'.self::segment($application, 'application').'/chat/team/'.self::segment($member, 'member'),
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * One person of this support team: their role, mobile, last sign-in, and what they are beyond
     * this number (the other inboxes they answer, their console role). `GET
     * /v1/applications/{application}/chat/team/{member}`.
     *
     * Permission `chat:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param string $member `member` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation chat.team.show
     */
    public function show(string $application, string $member, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/chat/team/'.self::segment($member, 'member'),
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }

    /**
     * Change a support team person's name and role (`agent` or `administrator`); the mobile never
     * changes (it is how they sign in). Answers the person. `PATCH
     * /v1/applications/{application}/chat/team/{member}`.
     *
     * Permission `chat:write`. Sent once: retried only when the request never left (or the key is
     * still in progress). After a timeout the exception carries the `Idempotency-Key`: call again
     * with it, never a new one. After a 5xx Tawked released the key: check whether it took effect
     * before calling again.
     *
     * @param string $application `application` in the path
     * @param string $member `member` in the path
     * @param array{name: string, role: string}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation chat.team.update
     */
    public function update(string $application, string $member, array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'PATCH',
            path: '/v1/applications/'.self::segment($application, 'application').'/chat/team/'.self::segment($member, 'member'),
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
        ));
    }
}
