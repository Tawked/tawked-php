<?php

declare(strict_types=1);

namespace Tawked\Exception;

/**
 * 409: the resource's state refuses the call (not_resendable, not_cancelable, not_retryable, application_archived, number_connected, no_whatsapp_number, number_disconnected, template_in_use, template_not_editable; idempotency errors are their own subclass). `currentStatus()` reads the state where the API names it, and `usedBy()` what still sends a template.
 */
class ConflictException extends ApiException {}
