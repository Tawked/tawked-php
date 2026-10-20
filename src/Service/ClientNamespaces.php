<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Service;

use Tawked\Http\Transport;

/**
 * The namespaces of the product client, one per area of the API.
 *
 * @internal the client's base; use the client
 */
abstract class ClientNamespaces
{
    public readonly ChatService $chat;

    public readonly EmailService $email;

    public readonly PartnerService $partner;

    public readonly VerifyService $verify;

    public readonly WebhookEndpointsService $webhookEndpoints;

    public readonly WhatsAppService $whatsapp;

    public function __construct(Transport $transport)
    {
        $this->chat = new ChatService($transport);
        $this->email = new EmailService($transport);
        $this->partner = new PartnerService($transport);
        $this->verify = new VerifyService($transport);
        $this->webhookEndpoints = new WebhookEndpointsService($transport);
        $this->whatsapp = new WhatsAppService($transport);
    }
}
