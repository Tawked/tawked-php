<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Wallet;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountPage;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;
use Tawked\Pagination\Paginator;

/**
 * `$account->wallet->invoices`: the Account API's `wallet.invoices` operations.
 */
final class InvoicesApi extends AccountNamespace
{
    /**
     * The account's billing documents, newest first: invoices, credit notes and proformas, each
     * with its number, kind, status, totals and VAT in halalas, and its PDF's address. `GET
     * /v1/account/wallet/invoices`.
     *
     * Permission `wallet:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param array{document_kind?: string, document_month?: string, number?: string, page?: int, q?: string}|array<string, mixed> $params
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation wallet.invoices.list
     */
    public function list(array $params = [], array $options = []): AccountPage
    {
        return AccountPage::from($this->transport->send(
            method: 'GET',
            path: '/v1/account/wallet/invoices',
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

    /**
     * One billing document: its number, kind, status, totals, VAT and discount in halalas, who it
     * is addressed to, what a credit note corrects, what a settled proforma led to, and its PDF's
     * address. `GET /v1/account/wallet/invoices/{document}`.
     *
     * Permission `wallet:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $document `document` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation wallet.invoices.show
     */
    public function show(string $document, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/account/wallet/invoices/'.self::segment($document, 'document'),
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }
}
