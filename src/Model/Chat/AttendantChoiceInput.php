<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Chat;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `AttendantChoiceInput` object.
 *
 * Contract type: `AttendantChoiceInput`.
 */
final class AttendantChoiceInput extends Model
{
    public function __construct(
        /** Optional: a choice without one gets one. */
        public readonly ?string $id,
        /**
         * Up to 20 characters when its menu shows as buttons (1 to 3 choices), 24 as a list; unique
         * in its menu.
         */
        public readonly string $title,
        /** A list item's second line. */
        public readonly ?string $description,
        public readonly AttendantChoiceAction $action,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::nullableString($data, 'id'),
            Data::string($data, 'title'),
            Data::nullableString($data, 'description'),
            AttendantChoiceAction::fromArray(Data::object($data, 'action') ?? []),
        );
        $model->raw = $data;

        return $model;
    }
}
