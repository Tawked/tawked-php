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
final class HistoryEvent extends Model
{
    public function __construct(
        public readonly string $action,
        public readonly string $actor,
        public readonly ?string $reason,
        public readonly ?Screening $screening,
        public readonly ?DateTimeImmutable $createdAt,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'action'),
            Data::string($data, 'actor'),
            Data::nullableString($data, 'reason'),
            is_array($data['screening'] ?? null) ? Screening::fromArray(Data::stringKeys($data['screening'])) : null,
            Data::date($data, 'created_at'),
        );
        $model->raw = $data;

        return $model;
    }
}
