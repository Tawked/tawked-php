<?php

declare(strict_types=1);

namespace Tawked\Exception;

/**
 * A call was refused before it was sent: a missing key, a malformed Idempotency-Key, an
 * unreadable logo file.
 */
class InvalidArgumentException extends \InvalidArgumentException implements TawkedException {}
