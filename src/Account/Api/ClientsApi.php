<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api;

use Tawked\Account\AccountNamespace;
use Tawked\Account\Api\Clients\ApiApi;
use Tawked\Account\Api\Clients\ApiKeyApi;
use Tawked\Account\Api\Clients\AppealsApi;
use Tawked\Account\Api\Clients\ApplicationsApi;
use Tawked\Account\Api\Clients\DeliveriesApi;
use Tawked\Account\Api\Clients\OverviewApi;
use Tawked\Account\Api\Clients\ReviewsApi;
use Tawked\Account\Api\Clients\SandboxKeyApi;
use Tawked\Account\Api\Clients\UsageApi;
use Tawked\Account\Api\Clients\VerifySettingsApi;
use Tawked\Account\Api\Clients\WebhookApi;
use Tawked\Http\Transport;

/**
 * `$account->clients`: the Account API's `clients` operations.
 */
final class ClientsApi extends AccountNamespace
{
    public readonly ApiApi $api;

    public readonly ApiKeyApi $apiKey;

    public readonly AppealsApi $appeals;

    public readonly ApplicationsApi $applications;

    public readonly DeliveriesApi $deliveries;

    public readonly OverviewApi $overview;

    public readonly ReviewsApi $reviews;

    public readonly SandboxKeyApi $sandboxKey;

    public readonly UsageApi $usage;

    public readonly VerifySettingsApi $verifySettings;

    public readonly WebhookApi $webhook;

    public function __construct(Transport $transport)
    {
        parent::__construct($transport);
        $this->api = new ApiApi($transport);
        $this->apiKey = new ApiKeyApi($transport);
        $this->appeals = new AppealsApi($transport);
        $this->applications = new ApplicationsApi($transport);
        $this->deliveries = new DeliveriesApi($transport);
        $this->overview = new OverviewApi($transport);
        $this->reviews = new ReviewsApi($transport);
        $this->sandboxKey = new SandboxKeyApi($transport);
        $this->usage = new UsageApi($transport);
        $this->verifySettings = new VerifySettingsApi($transport);
        $this->webhook = new WebhookApi($transport);
    }
}
