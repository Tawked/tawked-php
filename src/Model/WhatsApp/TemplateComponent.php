<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\WhatsApp;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class TemplateComponent extends Model
{
    /**
     * @param list<TemplateComponentButton> $buttons
     */
    public function __construct(
        /** HEADER, BODY, FOOTER or BUTTONS, as Meta names it. */
        public readonly string $type,
        public readonly ?string $format,
        public readonly ?string $text,
        public readonly array $buttons,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'type'),
            Data::nullableString($data, 'format'),
            Data::nullableString($data, 'text'),
            array_map(static fn(array $item): TemplateComponentButton => TemplateComponentButton::fromArray($item), Data::objects($data, 'buttons')),
        );
        $model->raw = $data;

        return $model;
    }
}
