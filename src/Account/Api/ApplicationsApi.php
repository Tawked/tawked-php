<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api;

use Tawked\Account\AccountNamespace;
use Tawked\Account\Api\Applications\AppealsApi;
use Tawked\Account\Api\Applications\KeysApi;
use Tawked\Account\Api\Applications\LogoApi;
use Tawked\Account\Api\Applications\ProductsApi;
use Tawked\Account\Api\Applications\WebhookDeliveriesApi;
use Tawked\Account\Api\Applications\WebhooksApi;
use Tawked\Http\Transport;

/**
 * `$account->applications`: the Account API's `applications` operations.
 */
final class ApplicationsApi extends AccountNamespace
{
    public readonly AppealsApi $appeals;

    public readonly \Tawked\Account\Api\Applications\ApplicationsApi $applications;

    public readonly KeysApi $keys;

    public readonly LogoApi $logo;

    public readonly ProductsApi $products;

    public readonly WebhookDeliveriesApi $webhookDeliveries;

    public readonly WebhooksApi $webhooks;

    public function __construct(Transport $transport)
    {
        parent::__construct($transport);
        $this->appeals = new AppealsApi($transport);
        $this->applications = new \Tawked\Account\Api\Applications\ApplicationsApi($transport);
        $this->keys = new KeysApi($transport);
        $this->logo = new LogoApi($transport);
        $this->products = new ProductsApi($transport);
        $this->webhookDeliveries = new WebhookDeliveriesApi($transport);
        $this->webhooks = new WebhooksApi($transport);
    }
}
