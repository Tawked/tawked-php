<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class EmailTemplateVersionVariable extends Model
{
    public function __construct(
        public readonly string $key,
        public readonly ?string $fallback,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'key'),
            Data::nullableString($data, 'fallback'),
        );
        $model->raw = $data;

        return $model;
    }
}
