<?php

declare(strict_types=1);

namespace Tawked\Http;

/**
 * A call the server held (a 428 approval): everything needed to send exactly the same request again.
 *
 * @internal
 */
final class PendingRequest
{
    /**
     * @param array<string, mixed> $call the transport's send() arguments
     */
    public function __construct(
        public readonly array $call,
        public readonly ?string $idempotencyKey,
        public readonly ?string $boundary = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        return ['method' => $this->call['method'] ?? null, 'path' => $this->call['path'] ?? null, 'idempotencyKey' => $this->idempotencyKey];
    }
}
