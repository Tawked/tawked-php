<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Account\Api\Account\AlertsApi;
use Tawked\Account\Api\Account\DocumentApi;
use Tawked\Account\Api\Account\EmailApi;
use Tawked\Account\Api\Account\MeApi;
use Tawked\Account\Api\Account\ProfileApi;
use Tawked\Account\Api\Account\SessionsApi;
use Tawked\Account\Api\Account\TokensApi;
use Tawked\Http\Transport;

/**
 * `$account->account`: the Account API's `account` operations.
 */
final class AccountApi extends AccountNamespace
{
    public readonly AlertsApi $alerts;

    public readonly DocumentApi $document;

    public readonly EmailApi $email;

    public readonly MeApi $me;

    public readonly ProfileApi $profile;

    public readonly SessionsApi $sessions;

    public readonly TokensApi $tokens;

    public function __construct(Transport $transport)
    {
        parent::__construct($transport);
        $this->alerts = new AlertsApi($transport);
        $this->document = new DocumentApi($transport);
        $this->email = new EmailApi($transport);
        $this->me = new MeApi($transport);
        $this->profile = new ProfileApi($transport);
        $this->sessions = new SessionsApi($transport);
        $this->tokens = new TokensApi($transport);
    }

    /**
     * The account as Settings shows it to this person: the profile (owner only), their own name,
     * e-mail and phone, their role, and the business e-mail's state (confirmed, or a code pending).
     * `GET /v1/account`.
     *
     * Permission `account:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation account.show
     */
    public function show(array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/account',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }
}
