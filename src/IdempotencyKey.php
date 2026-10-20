<?php

declare(strict_types=1);

namespace Tawked;

/**
 * Makes an Idempotency-Key. Prefer a key of your own business event (an order id, a sign-in
 * attempt id) so a retry after a crash reuses it; use this when there is none.
 */
final class IdempotencyKey
{
    /**
     * A random UUID v4.
     */
    public static function generate(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0F) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3F) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
    }
}
