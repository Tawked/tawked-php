<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Partner;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class BatchItem extends Model
{
    /**
     * @param list<string> $fields
     */
    public function __construct(
        public readonly int $index,
        public readonly ?string $externalId,
        public readonly string $outcome,
        public readonly array $fields,
        public readonly ?ApplicationSummary $application,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::int($data, 'index'),
            Data::nullableString($data, 'external_id'),
            Data::string($data, 'outcome'),
            Data::strings($data, 'fields'),
            is_array($data['application'] ?? null) ? ApplicationSummary::fromArray(Data::stringKeys($data['application'])) : null,
        );
        $model->raw = $data;

        return $model;
    }
}
