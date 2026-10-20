<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\WhatsApp;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class TemplateButton extends Model
{
    public function __construct(
        public readonly int $index,
        public readonly string $type,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::int($data, 'index'),
            Data::string($data, 'type'),
        );
        $model->raw = $data;

        return $model;
    }
}
