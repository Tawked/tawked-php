<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api;

use Tawked\Account\AccountNamespace;
use Tawked\Account\Api\Radeef\AgentApi;
use Tawked\Account\Api\Radeef\ConversationsApi;
use Tawked\Account\Api\Radeef\FaqsApi;
use Tawked\Account\Api\Radeef\FilesApi;
use Tawked\Account\Api\Radeef\HoursApi;
use Tawked\Account\Api\Radeef\InsightsApi;
use Tawked\Account\Api\Radeef\KnowledgeApi;
use Tawked\Account\Api\Radeef\NotesApi;
use Tawked\Account\Api\Radeef\NoticesApi;
use Tawked\Account\Api\Radeef\OwnerApi;
use Tawked\Account\Api\Radeef\OwnerChangesApi;
use Tawked\Account\Api\Radeef\PagesApi;
use Tawked\Account\Api\Radeef\PlaygroundApi;
use Tawked\Account\Api\Radeef\ProductsApi;
use Tawked\Account\Api\Radeef\ProposalsApi;
use Tawked\Account\Api\Radeef\ReviewApi;
use Tawked\Account\Api\Radeef\ScansApi;
use Tawked\Account\Api\Radeef\SettingsApi;
use Tawked\Account\Api\Radeef\SetupApi;
use Tawked\Account\Api\Radeef\SourcesApi;
use Tawked\Account\Api\Radeef\StateApi;
use Tawked\Account\Api\Radeef\StoreApi;
use Tawked\Account\Api\Radeef\TopicsApi;
use Tawked\Account\Api\Radeef\UnansweredApi;
use Tawked\Http\Transport;

/**
 * `$account->radeef`: the Account API's `radeef` operations.
 */
final class RadeefApi extends AccountNamespace
{
    public readonly AgentApi $agent;

    public readonly ConversationsApi $conversations;

    public readonly FaqsApi $faqs;

    public readonly FilesApi $files;

    public readonly HoursApi $hours;

    public readonly InsightsApi $insights;

    public readonly KnowledgeApi $knowledge;

    public readonly NotesApi $notes;

    public readonly NoticesApi $notices;

    public readonly OwnerApi $owner;

    public readonly OwnerChangesApi $ownerChanges;

    public readonly PagesApi $pages;

    public readonly PlaygroundApi $playground;

    public readonly ProductsApi $products;

    public readonly ProposalsApi $proposals;

    public readonly ReviewApi $review;

    public readonly ScansApi $scans;

    public readonly SettingsApi $settings;

    public readonly SetupApi $setup;

    public readonly SourcesApi $sources;

    public readonly StateApi $state;

    public readonly StoreApi $store;

    public readonly TopicsApi $topics;

    public readonly UnansweredApi $unanswered;

    public function __construct(Transport $transport)
    {
        parent::__construct($transport);
        $this->agent = new AgentApi($transport);
        $this->conversations = new ConversationsApi($transport);
        $this->faqs = new FaqsApi($transport);
        $this->files = new FilesApi($transport);
        $this->hours = new HoursApi($transport);
        $this->insights = new InsightsApi($transport);
        $this->knowledge = new KnowledgeApi($transport);
        $this->notes = new NotesApi($transport);
        $this->notices = new NoticesApi($transport);
        $this->owner = new OwnerApi($transport);
        $this->ownerChanges = new OwnerChangesApi($transport);
        $this->pages = new PagesApi($transport);
        $this->playground = new PlaygroundApi($transport);
        $this->products = new ProductsApi($transport);
        $this->proposals = new ProposalsApi($transport);
        $this->review = new ReviewApi($transport);
        $this->scans = new ScansApi($transport);
        $this->settings = new SettingsApi($transport);
        $this->setup = new SetupApi($transport);
        $this->sources = new SourcesApi($transport);
        $this->state = new StateApi($transport);
        $this->store = new StoreApi($transport);
        $this->topics = new TopicsApi($transport);
        $this->unanswered = new UnansweredApi($transport);
    }
}
