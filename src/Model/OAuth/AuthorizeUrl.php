<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\OAuth;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The URL to send the merchant to, with the state and the PKCE verifier to keep in their session
 * until the return.
 *
 * Contract type: `AuthorizeUrl`.
 */
final class AuthorizeUrl extends Model
{
    public function __construct(
        public readonly string $url,
        public readonly string $state,
        public readonly string $codeVerifier,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'url'),
            Data::string($data, 'state'),
            Data::string($data, 'code_verifier'),
        );
        $model->raw = $data;

        return $model;
    }
}
