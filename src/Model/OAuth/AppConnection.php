<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\OAuth;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `AppConnection` object.
 *
 * Contract type: `AppConnection`.
 */
final class AppConnection extends Model
{
    /**
     * @param list<string> $permissions
     */
    public function __construct(
        /**
         * The connection's key: a tk_live_ application key for the one application the merchant
         * chose. Shown once; there is no refresh token and it does not expire with time.
         */
        public readonly string $accessToken,
        public readonly string $tokenType,
        public readonly string $connectionId,
        public readonly AppConnectionApplication $application,
        public readonly array $permissions,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'access_token'),
            Data::string($data, 'token_type'),
            Data::string($data, 'connection_id'),
            AppConnectionApplication::fromArray(Data::object($data, 'application') ?? []),
            Data::strings($data, 'permissions'),
        );
        $model->raw = $data;

        return $model;
    }
}
