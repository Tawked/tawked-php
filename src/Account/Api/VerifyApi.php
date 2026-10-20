<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api;

use Tawked\Account\AccountNamespace;
use Tawked\Account\Api\Verify\SettingsApi;
use Tawked\Account\Api\Verify\TestApi;
use Tawked\Account\Api\Verify\UsageApi;
use Tawked\Account\Api\Verify\VerificationsApi;
use Tawked\Http\Transport;

/**
 * `$account->verify`: the Account API's `verify` operations.
 */
final class VerifyApi extends AccountNamespace
{
    public readonly SettingsApi $settings;

    public readonly TestApi $test;

    public readonly UsageApi $usage;

    public readonly VerificationsApi $verifications;

    public function __construct(Transport $transport)
    {
        parent::__construct($transport);
        $this->settings = new SettingsApi($transport);
        $this->test = new TestApi($transport);
        $this->usage = new UsageApi($transport);
        $this->verifications = new VerificationsApi($transport);
    }
}
