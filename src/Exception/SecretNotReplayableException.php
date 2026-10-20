<?php

declare(strict_types=1);

namespace Tawked\Exception;

use Tawked\Internal\Data;

/**
 * 409 idempotency_secret_not_replayable: the first request with this key succeeded, and its answer
 * held a secret shown once that is never kept. Find what it made by `id()`, then rotate or revoke
 * it and make a new one if the secret was lost.
 */
class SecretNotReplayableException extends IdempotencyException
{
    /** The id of what the first request made. */
    public function id(): ?string
    {
        return Data::nullableString($this->body, 'id');
    }

    /** The status the first request answered. */
    public function originalStatus(): ?int
    {
        return Data::nullableInt($this->body, 'original_status');
    }
}
