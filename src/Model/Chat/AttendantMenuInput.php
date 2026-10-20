<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Chat;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `AttendantMenuInput` object.
 *
 * Contract type: `AttendantMenuInput`.
 */
final class AttendantMenuInput extends Model
{
    /**
     * @param list<AttendantChoiceInput> $choices
     */
    public function __construct(
        public readonly ?int $version,
        /** The first message. */
        public readonly string $opening,
        /** The list's button label; «القائمة» or Menu by default. */
        public readonly ?string $listButton,
        /** Sent outside the team's working hours instead of handing over; null for Tawked's default. */
        public readonly ?string $away,
        /** Sent when the customer writes instead of choosing; null for Tawked's default. */
        public readonly ?string $fallback,
        public readonly array $choices,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::nullableInt($data, 'version'),
            Data::string($data, 'opening'),
            Data::nullableString($data, 'list_button'),
            Data::nullableString($data, 'away'),
            Data::nullableString($data, 'fallback'),
            array_map(static fn(array $item): AttendantChoiceInput => AttendantChoiceInput::fromArray($item), Data::objects($data, 'choices')),
        );
        $model->raw = $data;

        return $model;
    }
}
