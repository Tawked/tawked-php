<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api;

use Tawked\Account\AccountNamespace;
use Tawked\Account\Api\IntegrationApps\AppsApi;
use Tawked\Account\Api\IntegrationApps\PendingChangeApi;
use Tawked\Account\Api\IntegrationApps\SecretApi;
use Tawked\Http\Transport;

/**
 * `$account->integrationApps`: the Account API's `integration_apps` operations.
 */
final class IntegrationAppsApi extends AccountNamespace
{
    public readonly AppsApi $apps;

    public readonly PendingChangeApi $pendingChange;

    public readonly SecretApi $secret;

    public function __construct(Transport $transport)
    {
        parent::__construct($transport);
        $this->apps = new AppsApi($transport);
        $this->pendingChange = new PendingChangeApi($transport);
        $this->secret = new SecretApi($transport);
    }
}
