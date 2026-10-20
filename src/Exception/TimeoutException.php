<?php

declare(strict_types=1);

namespace Tawked\Exception;

/**
 * No answer within the call's timeout (30 s an attempt; 120 s for a batch, a secret or the OAuth
 * exchange). The request may have been applied: retry with the same `Idempotency-Key`.
 */
class TimeoutException extends ConnectionException {}
