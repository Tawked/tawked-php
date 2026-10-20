<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `EmailSuppression` object.
 *
 * Contract type: `EmailSuppression`.
 */
final class EmailSuppression extends Model
{
    public function __construct(
        public readonly string $id,
        public readonly string $address,
        public readonly bool $suppressed,
        public readonly bool $visible,
        public readonly string $reason,
        public readonly string $source,
        public readonly ?string $applicationId,
        public readonly ?string $emailId,
        public readonly ?DateTimeImmutable $createdAt,
        public readonly string $lift,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'id'),
            Data::string($data, 'address'),
            Data::bool($data, 'suppressed'),
            Data::bool($data, 'visible'),
            Data::string($data, 'reason'),
            Data::string($data, 'source'),
            Data::nullableString($data, 'application_id'),
            Data::nullableString($data, 'email_id'),
            Data::date($data, 'created_at'),
            Data::string($data, 'lift'),
        );
        $model->raw = $data;

        return $model;
    }
}
