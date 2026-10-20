<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api;

use Tawked\Http\Transport;

/**
 * The namespaces of the Account API client, one per area of the API.
 *
 * @internal the client's base; use the client
 */
abstract class AccountNamespaces
{
    public readonly AccountApi $account;

    public readonly ApplicationsApi $applications;

    public readonly ApprovalsApi $approvals;

    public readonly AutomationsApi $automations;

    public readonly CampaignsApi $campaigns;

    public readonly ChatApi $chat;

    public readonly ClientsApi $clients;

    public readonly ContactsApi $contacts;

    public readonly EmailApi $email;

    public readonly IntegrationAppsApi $integrationApps;

    public readonly IntegrationsApi $integrations;

    public readonly MeApi $me;

    public readonly MembersApi $members;

    public readonly OperationsApi $operations;

    public readonly RadeefApi $radeef;

    public readonly StoresApi $stores;

    public readonly VerifyApi $verify;

    public readonly WalletApi $wallet;

    public readonly WhatsAppApi $whatsapp;

    public function __construct(Transport $transport)
    {
        $this->account = new AccountApi($transport);
        $this->applications = new ApplicationsApi($transport);
        $this->approvals = new ApprovalsApi($transport);
        $this->automations = new AutomationsApi($transport);
        $this->campaigns = new CampaignsApi($transport);
        $this->chat = new ChatApi($transport);
        $this->clients = new ClientsApi($transport);
        $this->contacts = new ContactsApi($transport);
        $this->email = new EmailApi($transport);
        $this->integrationApps = new IntegrationAppsApi($transport);
        $this->integrations = new IntegrationsApi($transport);
        $this->me = new MeApi($transport);
        $this->members = new MembersApi($transport);
        $this->operations = new OperationsApi($transport);
        $this->radeef = new RadeefApi($transport);
        $this->stores = new StoresApi($transport);
        $this->verify = new VerifyApi($transport);
        $this->wallet = new WalletApi($transport);
        $this->whatsapp = new WhatsAppApi($transport);
    }
}
