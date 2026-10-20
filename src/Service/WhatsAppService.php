<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Service;

use Tawked\Http\Transport;
use Tawked\Service\WhatsApp\MessagesService;
use Tawked\Service\WhatsApp\TemplatesService;

/**
 * `$tawked->whatsapp`: WhatsApp template messages and templates (Tawked Notifications).
 */
final class WhatsAppService extends Service
{
    public readonly MessagesService $messages;

    public readonly TemplatesService $templates;

    public function __construct(Transport $transport)
    {
        parent::__construct($transport);
        $this->messages = new MessagesService($transport);
        $this->templates = new TemplatesService($transport);
    }
}
