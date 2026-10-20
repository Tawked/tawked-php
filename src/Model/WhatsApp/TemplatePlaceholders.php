<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\WhatsApp;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class TemplatePlaceholders extends Model
{
    /**
     * @param list<string> $header
     * @param list<string> $body
     * @param list<TemplateButton> $buttons
     */
    public function __construct(
        public readonly string $kind,
        public readonly array $header,
        public readonly array $body,
        public readonly array $buttons,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'kind'),
            Data::strings($data, 'header'),
            Data::strings($data, 'body'),
            array_map(static fn(array $item): TemplateButton => TemplateButton::fromArray($item), Data::objects($data, 'buttons')),
        );
        $model->raw = $data;

        return $model;
    }
}
