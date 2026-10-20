<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Chat;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class AttendantChoiceActionChoiceAction extends Model
{
    /**
     * @param list<AttendantChoice> $choices
     */
    public function __construct(
        public readonly string $type,
        public readonly ?string $text,
        /** https only. */
        public readonly ?string $link,
        public readonly array $choices,
        public readonly ?string $detail,
        /** For custom only. */
        public readonly ?string $label,
        public readonly ?string $question,
        /**
         * The label the conversation gets in Tawked Chat: letters (Arabic included), digits, _ or
         * -, starting with a letter or digit.
         */
        public readonly ?string $tag,
        public readonly ?AttendantChoiceActionChoiceActionAssign $assign,
        public readonly ?string $line,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'type'),
            Data::nullableString($data, 'text'),
            Data::nullableString($data, 'link'),
            array_map(static fn(array $item): AttendantChoice => AttendantChoice::fromArray($item), Data::objects($data, 'choices')),
            Data::nullableString($data, 'detail'),
            Data::nullableString($data, 'label'),
            Data::nullableString($data, 'question'),
            Data::nullableString($data, 'tag'),
            is_array($data['assign'] ?? null) ? AttendantChoiceActionChoiceActionAssign::fromArray(Data::stringKeys($data['assign'])) : null,
            Data::nullableString($data, 'line'),
        );
        $model->raw = $data;

        return $model;
    }
}
