<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api;

use Tawked\Account\AccountNamespace;
use Tawked\Account\Api\WhatsApp\BillingApi;
use Tawked\Account\Api\WhatsApp\ChargesApi;
use Tawked\Account\Api\WhatsApp\ConnectionApi;
use Tawked\Account\Api\WhatsApp\FirstMessageApi;
use Tawked\Account\Api\WhatsApp\GoLiveApi;
use Tawked\Account\Api\WhatsApp\MessagesApi;
use Tawked\Account\Api\WhatsApp\NumberApi;
use Tawked\Account\Api\WhatsApp\TemplateDraftsApi;
use Tawked\Account\Api\WhatsApp\TemplateLibraryApi;
use Tawked\Account\Api\WhatsApp\TemplateSamplesApi;
use Tawked\Account\Api\WhatsApp\TemplatesApi;
use Tawked\Account\Api\WhatsApp\TemplateSuggestionsApi;
use Tawked\Account\Api\WhatsApp\UsageApi;
use Tawked\Http\Transport;

/**
 * `$account->whatsapp`: the Account API's `whatsapp` operations.
 */
final class WhatsAppApi extends AccountNamespace
{
    public readonly BillingApi $billing;

    public readonly ChargesApi $charges;

    public readonly ConnectionApi $connection;

    public readonly FirstMessageApi $firstMessage;

    public readonly GoLiveApi $goLive;

    public readonly MessagesApi $messages;

    public readonly NumberApi $number;

    public readonly TemplateDraftsApi $templateDrafts;

    public readonly TemplateLibraryApi $templateLibrary;

    public readonly TemplateSamplesApi $templateSamples;

    public readonly TemplateSuggestionsApi $templateSuggestions;

    public readonly TemplatesApi $templates;

    public readonly UsageApi $usage;

    public function __construct(Transport $transport)
    {
        parent::__construct($transport);
        $this->billing = new BillingApi($transport);
        $this->charges = new ChargesApi($transport);
        $this->connection = new ConnectionApi($transport);
        $this->firstMessage = new FirstMessageApi($transport);
        $this->goLive = new GoLiveApi($transport);
        $this->messages = new MessagesApi($transport);
        $this->number = new NumberApi($transport);
        $this->templateDrafts = new TemplateDraftsApi($transport);
        $this->templateLibrary = new TemplateLibraryApi($transport);
        $this->templateSamples = new TemplateSamplesApi($transport);
        $this->templateSuggestions = new TemplateSuggestionsApi($transport);
        $this->templates = new TemplatesApi($transport);
        $this->usage = new UsageApi($transport);
    }
}
