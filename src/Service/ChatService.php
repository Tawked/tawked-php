<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Service;

use Tawked\Http\Transport;
use Tawked\Service\Chat\AttendantService;
use Tawked\Service\Chat\MenusService;

/**
 * `$tawked->chat`: Tawked Chat for connected apps (a connected app's key only).
 */
final class ChatService extends Service
{
    public readonly AttendantService $attendant;

    public readonly MenusService $menus;

    public function __construct(Transport $transport)
    {
        parent::__construct($transport);
        $this->attendant = new AttendantService($transport);
        $this->menus = new MenusService($transport);
    }
}
