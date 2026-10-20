<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Webhook;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `SuppressionAddedEventData` object.
 *
 * Contract type: `SuppressionAddedEventData`.
 */
final class SuppressionAddedEventData extends Model
{
    public function __construct(
        public readonly ?string $id,
        public readonly string $address,
        public readonly string $reason,
        public readonly string $source,
        public readonly ?string $applicationId,
        public readonly ?string $emailId,
        public readonly ?DateTimeImmutable $createdAt,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::nullableString($data, 'id'),
            Data::string($data, 'address'),
            Data::string($data, 'reason'),
            Data::string($data, 'source'),
            Data::nullableString($data, 'application_id'),
            Data::nullableString($data, 'email_id'),
            Data::date($data, 'created_at'),
        );
        $model->raw = $data;

        return $model;
    }
}
