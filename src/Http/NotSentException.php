<?php

declare(strict_types=1);

namespace Tawked\Http;

use Psr\Http\Client\NetworkExceptionInterface;

/**
 * Thrown by a PSR-18 client (or a test's transport hook) when the request never left: the SDK
 * then retries any call, since the server cannot have acted. Guzzle's own connect failures (DNS,
 * connect, TLS) are recognised without it.
 */
interface NotSentException extends NetworkExceptionInterface {}
