<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `EmailSuppressionImport` object.
 *
 * Contract type: `EmailSuppressionImport`.
 */
final class EmailSuppressionImport extends Model
{
    /**
     * @param list<int> $invalidLines
     */
    public function __construct(
        public readonly string $id,
        public readonly string $state,
        public readonly ?string $filename,
        public readonly ?int $rowsTotal,
        public readonly int $rowsAdded,
        public readonly int $rowsExisting,
        public readonly int $rowsInvalid,
        public readonly array $invalidLines,
        public readonly ?string $failure,
        public readonly ?DateTimeImmutable $createdAt,
        public readonly ?DateTimeImmutable $finishedAt,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'id'),
            Data::string($data, 'state'),
            Data::nullableString($data, 'filename'),
            Data::nullableInt($data, 'rows_total'),
            Data::int($data, 'rows_added'),
            Data::int($data, 'rows_existing'),
            Data::int($data, 'rows_invalid'),
            Data::ints($data, 'invalid_lines'),
            Data::nullableString($data, 'failure'),
            Data::date($data, 'created_at'),
            Data::date($data, 'finished_at'),
        );
        $model->raw = $data;

        return $model;
    }
}
