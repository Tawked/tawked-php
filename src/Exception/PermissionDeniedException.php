<?php

declare(strict_types=1);

namespace Tawked\Exception;

/**
 * 403: account_not_active, application_paused, ip_not_allowed, live_key_required, partner_suspended, whatsapp_not_enabled (and insufficient_scope, its own subclass).
 */
class PermissionDeniedException extends ApiException {}
