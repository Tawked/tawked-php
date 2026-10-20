<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Webhook;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `SuppressionRemovedEventData` object.
 *
 * Contract type: `SuppressionRemovedEventData`.
 */
final class SuppressionRemovedEventData extends Model
{
    public function __construct(
        public readonly mixed $id,
        public readonly string $address,
        public readonly string $reason,
        public readonly ?string $liftReason,
        public readonly string $by,
        public readonly ?DateTimeImmutable $removedAt,
        public readonly ?string $applicationId,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            $data['id'] ?? null,
            Data::string($data, 'address'),
            Data::string($data, 'reason'),
            Data::nullableString($data, 'lift_reason'),
            Data::string($data, 'by'),
            Data::date($data, 'removed_at'),
            Data::nullableString($data, 'application_id'),
        );
        $model->raw = $data;

        return $model;
    }
}
