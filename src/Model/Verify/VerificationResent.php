<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Verify;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class VerificationResent extends Model
{
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly ?DateTimeImmutable $expiresAt,
        public readonly int $resendsRemaining,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'id'),
            Data::string($data, 'status'),
            Data::date($data, 'expires_at'),
            Data::int($data, 'resends_remaining'),
        );
        $model->raw = $data;

        return $model;
    }
}
