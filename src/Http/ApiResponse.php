<?php

declare(strict_types=1);

namespace Tawked\Http;

/**
 * A successful response: its meta and its decoded JSON object, or, for a download (a received
 * e-mail's raw message or attachment), its bytes as they came (`raw`, the body left empty).
 *
 * @internal
 */
final class ApiResponse
{
    /**
     * @param array<string, mixed> $body
     */
    public function __construct(
        public readonly ResponseMeta $meta,
        public readonly array $body,
        public readonly ?string $raw = null,
    ) {}
}
