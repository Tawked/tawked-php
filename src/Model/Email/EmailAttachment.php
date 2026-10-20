<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class EmailAttachment extends Model
{
    public function __construct(
        public readonly string $filename,
        public readonly string $contentType,
        public readonly int $size,
        public readonly bool $inline,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'filename'),
            Data::string($data, 'content_type'),
            Data::int($data, 'size'),
            Data::bool($data, 'inline'),
        );
        $model->raw = $data;

        return $model;
    }
}
