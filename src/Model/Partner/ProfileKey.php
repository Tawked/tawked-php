<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Partner;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class ProfileKey extends Model
{
    /**
     * @param list<string> $ipAllowlist
     */
    public function __construct(
        public readonly string $prefix,
        public readonly string $mode,
        public readonly ?DateTimeImmutable $createdAt,
        public readonly ?DateTimeImmutable $expiresAt,
        public readonly array $ipAllowlist,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'prefix'),
            Data::string($data, 'mode'),
            Data::date($data, 'created_at'),
            Data::date($data, 'expires_at'),
            Data::strings($data, 'ip_allowlist'),
        );
        $model->raw = $data;

        return $model;
    }
}
