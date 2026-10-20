<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api;

use Tawked\Account\AccountNamespace;
use Tawked\Account\Api\Campaigns\ImagesApi;
use Tawked\Account\Api\Campaigns\RecipientsApi;
use Tawked\Account\Api\Campaigns\TemplatesApi;
use Tawked\Http\Transport;

/**
 * `$account->campaigns`: the Account API's `campaigns` operations.
 */
final class CampaignsApi extends AccountNamespace
{
    public readonly \Tawked\Account\Api\Campaigns\CampaignsApi $campaigns;

    public readonly ImagesApi $images;

    public readonly RecipientsApi $recipients;

    public readonly TemplatesApi $templates;

    public function __construct(Transport $transport)
    {
        parent::__construct($transport);
        $this->campaigns = new \Tawked\Account\Api\Campaigns\CampaignsApi($transport);
        $this->images = new ImagesApi($transport);
        $this->recipients = new RecipientsApi($transport);
        $this->templates = new TemplatesApi($transport);
    }
}
