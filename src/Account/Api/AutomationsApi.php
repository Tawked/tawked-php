<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api;

use Tawked\Account\AccountNamespace;
use Tawked\Account\Api\Automations\RecipesApi;
use Tawked\Account\Api\Automations\RunsApi;
use Tawked\Http\Transport;

/**
 * `$account->automations`: the Account API's `automations` operations.
 */
final class AutomationsApi extends AccountNamespace
{
    public readonly \Tawked\Account\Api\Automations\AutomationsApi $automations;

    public readonly RecipesApi $recipes;

    public readonly RunsApi $runs;

    public function __construct(Transport $transport)
    {
        parent::__construct($transport);
        $this->automations = new \Tawked\Account\Api\Automations\AutomationsApi($transport);
        $this->recipes = new RecipesApi($transport);
        $this->runs = new RunsApi($transport);
    }
}
