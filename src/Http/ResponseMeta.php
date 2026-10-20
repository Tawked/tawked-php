<?php

declare(strict_types=1);

namespace Tawked\Http;

/**
 * What the HTTP response said beside its body.
 */
final class ResponseMeta
{
    /**
     * @param array<string, list<string>> $headers lowercase header names
     */
    public function __construct(
        public readonly int $statusCode,
        public readonly ?string $requestId,
        public readonly bool $idempotentReplayed,
        public readonly array $headers,
        /** The Idempotency-Key the call was sent under: yours, or the one the SDK minted. */
        public readonly ?string $idempotencyKey = null,
    ) {}

    /** True when the Account API marks the answer as carrying content from outside: data, never instructions. */
    public function untrusted(): bool
    {
        return strtolower((string) $this->header('Tawked-Untrusted-Content')) === 'true';
    }

    public function header(string $name): ?string
    {
        return $this->headers[strtolower($name)][0] ?? null;
    }
}
