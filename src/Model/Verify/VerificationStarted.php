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
final class VerificationStarted extends Model
{
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly ?DateTimeImmutable $expiresAt,
        public readonly ?string $reference,
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
            Data::nullableString($data, 'reference'),
        );
        $model->raw = $data;

        return $model;
    }
}
