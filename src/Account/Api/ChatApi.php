<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Account\Api\Chat\AttendantApi;
use Tawked\Account\Api\Chat\MenusApi;
use Tawked\Account\Api\Chat\TeamApi;
use Tawked\Http\Transport;

/**
 * `$account->chat`: the Account API's `chat` operations.
 */
final class ChatApi extends AccountNamespace
{
    public readonly AttendantApi $attendant;

    public readonly MenusApi $menus;

    public readonly TeamApi $team;

    public function __construct(Transport $transport)
    {
        parent::__construct($transport);
        $this->attendant = new AttendantApi($transport);
        $this->menus = new MenusApi($transport);
        $this->team = new TeamApi($transport);
    }

    /**
     * Where a person opens this application's conversations in Tawked Chat: the web address (they
     * sign in with their own mobile), the phone apps once listed, the inbox's state, and whether
     * this token's person answers this number's conversations. `GET
     * /v1/applications/{application}/chat/link`.
     *
     * Permission `chat:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation chat.open_link
     */
    public function openLink(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/chat/link',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }
}
