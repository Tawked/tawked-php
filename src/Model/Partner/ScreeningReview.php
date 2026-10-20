<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Partner;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class ScreeningReview extends Model
{
    public function __construct(
        public readonly bool $needed,
        public readonly ?string $decidedBy,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::bool($data, 'needed'),
            Data::nullableString($data, 'decided_by'),
        );
        $model->raw = $data;

        return $model;
    }
}
