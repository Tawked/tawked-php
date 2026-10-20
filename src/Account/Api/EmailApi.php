<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api;

use Tawked\Account\AccountNamespace;
use Tawked\Account\Api\Email\AccessApi;
use Tawked\Account\Api\Email\InboundApi;
use Tawked\Account\Api\Email\KeysApi;
use Tawked\Account\Api\Email\LimitApi;
use Tawked\Account\Api\Email\MessagesApi;
use Tawked\Account\Api\Email\MetricsApi;
use Tawked\Account\Api\Email\ReceivedApi;
use Tawked\Account\Api\Email\ReviewApi;
use Tawked\Account\Api\Email\SendersApi;
use Tawked\Account\Api\Email\SettingsApi;
use Tawked\Account\Api\Email\SuppressionsApi;
use Tawked\Account\Api\Email\TemplatesApi;
use Tawked\Account\Api\Email\TestAddressApi;
use Tawked\Http\Transport;

/**
 * `$account->email`: the Account API's `email` operations.
 */
final class EmailApi extends AccountNamespace
{
    public readonly AccessApi $access;

    public readonly InboundApi $inbound;

    public readonly KeysApi $keys;

    public readonly LimitApi $limit;

    public readonly MessagesApi $messages;

    public readonly MetricsApi $metrics;

    public readonly ReceivedApi $received;

    public readonly ReviewApi $review;

    public readonly SendersApi $senders;

    public readonly SettingsApi $settings;

    public readonly SuppressionsApi $suppressions;

    public readonly TemplatesApi $templates;

    public readonly TestAddressApi $testAddress;

    public function __construct(Transport $transport)
    {
        parent::__construct($transport);
        $this->access = new AccessApi($transport);
        $this->inbound = new InboundApi($transport);
        $this->keys = new KeysApi($transport);
        $this->limit = new LimitApi($transport);
        $this->messages = new MessagesApi($transport);
        $this->metrics = new MetricsApi($transport);
        $this->received = new ReceivedApi($transport);
        $this->review = new ReviewApi($transport);
        $this->senders = new SendersApi($transport);
        $this->settings = new SettingsApi($transport);
        $this->suppressions = new SuppressionsApi($transport);
        $this->templates = new TemplatesApi($transport);
        $this->testAddress = new TestAddressApi($transport);
    }
}
