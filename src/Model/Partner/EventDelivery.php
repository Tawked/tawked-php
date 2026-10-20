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
final class EventDelivery extends Model
{
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly int $attempts,
        public readonly ?DateTimeImmutable $deliveredAt,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'id'),
            Data::string($data, 'status'),
            Data::int($data, 'attempts'),
            Data::date($data, 'delivered_at'),
        );
        $model->raw = $data;

        return $model;
    }
}
