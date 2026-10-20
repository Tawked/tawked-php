<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Partner;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class BatchResult extends Model
{
    /**
     * @param list<BatchItem> $results
     */
    public function __construct(
        public readonly int $created,
        public readonly int $existing,
        public readonly int $refused,
        public readonly array $results,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::int($data, 'created'),
            Data::int($data, 'existing'),
            Data::int($data, 'refused'),
            array_map(static fn(array $item): BatchItem => BatchItem::fromArray($item), Data::objects($data, 'results')),
        );
        $model->raw = $data;

        return $model;
    }
}
