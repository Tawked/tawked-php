<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Chat;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `AttendantProblem` object.
 *
 * Contract type: `AttendantProblem`.
 */
final class AttendantProblem extends Model
{
    public function __construct(
        public readonly string $code,
        /** The choice it is on; null for the top level. */
        public readonly ?string $choiceId,
        public readonly string $field,
        /** The limit crossed, or null. */
        public readonly ?int $max,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'code'),
            Data::nullableString($data, 'choice_id'),
            Data::string($data, 'field'),
            Data::nullableInt($data, 'max'),
        );
        $model->raw = $data;

        return $model;
    }
}
