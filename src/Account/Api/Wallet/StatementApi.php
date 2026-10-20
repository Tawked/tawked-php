<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Wallet;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountPage;
use Tawked\Http\Transport;
use Tawked\Pagination\Paginator;

/**
 * `$account->wallet->statement`: the Account API's `wallet.statement` operations.
 */
final class StatementApi extends AccountNamespace
{
    /**
     * The wallet's ledger lines, newest first: each one's kind (topup, otp_charge,
     * whatsapp_message_charge, email_charge, chat_agent_charge, refund, adjustment,
     * signup_bonus…), product, application, template, a reference the client knows, the signed
     * amount and the balance after, in halalas. `GET /v1/account/wallet/statement`.
     *
     * Permission `wallet:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param array{application?: string, day?: string, kind?: string, month?: string, page?: int, product?: string, q?: string, reference?: string, side?: string}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation wallet.statement.list
     */
    public function list(array $params = [], array $options = []): AccountPage
    {
        return AccountPage::from($this->transport->send(
            method: 'GET',
            path: '/v1/account/wallet/statement',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
            query: $params,
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
    public function iterate(array $params = [], array $options = []): \Generator
    {
        return Paginator::cursor(fn(array $page): AccountPage => $this->list($page, $options), $params, 'cursor');
    }
}
