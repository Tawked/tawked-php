<?php

declare(strict_types=1);

namespace Tawked\Exception;

use RuntimeException;
use Throwable;

/**
 * No answer from the API: the connection failed, or it broke once the request was sent.
 *
 * `mayHaveBeenApplied()` says which: false when the request never left (DNS, connect, TLS; the SDK
 * already retried those), true when it may have reached Tawked. Then a keyed call's message starts
 * with its `Idempotency-Key`: call again with that key (`idempotencyKey()`), never a new one.
 */
class ConnectionException extends RuntimeException implements TawkedException
{
    public function __construct(
        string $message,
        private readonly ?string $idempotencyKey = null,
        private readonly bool $afterSend = true,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    public function idempotencyKey(): ?string
    {
        return $this->idempotencyKey;
    }

    public function mayHaveBeenApplied(): bool
    {
        return $this->afterSend;
    }
}
