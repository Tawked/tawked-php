<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api;

use Tawked\Account\AccountNamespace;
use Tawked\Account\Api\Wallet\AutoTopupApi;
use Tawked\Account\Api\Wallet\BalanceApi;
use Tawked\Account\Api\Wallet\BillingApi;
use Tawked\Account\Api\Wallet\InvoicesApi;
use Tawked\Account\Api\Wallet\PaymentMethodsApi;
use Tawked\Account\Api\Wallet\ProformasApi;
use Tawked\Account\Api\Wallet\SettingsApi;
use Tawked\Account\Api\Wallet\StatementApi;
use Tawked\Account\Api\Wallet\StatementsApi;
use Tawked\Account\Api\Wallet\TopupsApi;
use Tawked\Http\Transport;

/**
 * `$account->wallet`: the Account API's `wallet` operations.
 */
final class WalletApi extends AccountNamespace
{
    public readonly AutoTopupApi $autoTopup;

    public readonly BalanceApi $balance;

    public readonly BillingApi $billing;

    public readonly InvoicesApi $invoices;

    public readonly PaymentMethodsApi $paymentMethods;

    public readonly ProformasApi $proformas;

    public readonly SettingsApi $settings;

    public readonly StatementApi $statement;

    public readonly StatementsApi $statements;

    public readonly TopupsApi $topups;

    public function __construct(Transport $transport)
    {
        parent::__construct($transport);
        $this->autoTopup = new AutoTopupApi($transport);
        $this->balance = new BalanceApi($transport);
        $this->billing = new BillingApi($transport);
        $this->invoices = new InvoicesApi($transport);
        $this->paymentMethods = new PaymentMethodsApi($transport);
        $this->proformas = new ProformasApi($transport);
        $this->settings = new SettingsApi($transport);
        $this->statement = new StatementApi($transport);
        $this->statements = new StatementsApi($transport);
        $this->topups = new TopupsApi($transport);
    }
}
