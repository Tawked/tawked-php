<?php

declare(strict_types=1);

namespace Tawked\Exception;

/**
 * 409 idempotency_key_reused (the key was used for a different request: use one key per
 * business event) or idempotency_in_progress (the first request with this key is still running).
 */
class IdempotencyException extends ConflictException
{
    /**
     * @internal the first request with this key was still running when the SDK's wait (30 s) ended
     */
    public static function stillInProgress(ApiException $error, string $key): self
    {
        $exception = new self($error->statusCode, $error->errorCode, $error->requestId, $error->body, $error->headers);
        $exception->attachIdempotencyKey($key, false);
        $exception->message = "`Idempotency-Key {$key}` is still being processed: call again with `idempotency_key: '{$key}'` (not a new key) to get its answer. ".$exception->message;

        return $exception;
    }
}
