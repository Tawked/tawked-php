<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\WhatsApp;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class TemplateComponentButton extends Model
{
    public function __construct(
        public readonly string $type,
        public readonly ?string $text,
        public readonly ?string $url,
        public readonly ?string $phoneNumber,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'type'),
            Data::nullableString($data, 'text'),
            Data::nullableString($data, 'url'),
            Data::nullableString($data, 'phone_number'),
        );
        $model->raw = $data;

        return $model;
    }
}
