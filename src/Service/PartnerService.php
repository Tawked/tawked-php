<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Service;

use Tawked\Http\Transport;
use Tawked\Service\Partner\ApplicationsService;
use Tawked\Service\Partner\EventsService;
use Tawked\Service\Partner\ProfileService;
use Tawked\Service\Partner\ScreeningService;
use Tawked\Service\Partner\UsageService;
use Tawked\Service\Partner\VerificationsService;
use Tawked\Service\Partner\WebhookService;

/**
 * `$tawked->partner`: the Partner API (partner keys only).
 */
final class PartnerService extends Service
{
    public readonly ApplicationsService $applications;

    public readonly EventsService $events;

    public readonly ProfileService $profile;

    public readonly ScreeningService $screening;

    public readonly UsageService $usage;

    public readonly VerificationsService $verifications;

    public readonly WebhookService $webhook;

    public function __construct(Transport $transport)
    {
        parent::__construct($transport);
        $this->applications = new ApplicationsService($transport);
        $this->events = new EventsService($transport);
        $this->profile = new ProfileService($transport);
        $this->screening = new ScreeningService($transport);
        $this->usage = new UsageService($transport);
        $this->verifications = new VerificationsService($transport);
        $this->webhook = new WebhookService($transport);
    }
}
