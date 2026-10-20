<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Webhook;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class EmailReceivedEventDataAttachment extends Model
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $filename,
        public readonly string $contentType,
        public readonly int $size,
        public readonly bool $inline,
        public readonly bool $risky,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'id'),
            Data::nullableString($data, 'filename'),
            Data::string($data, 'content_type'),
            Data::int($data, 'size'),
            Data::bool($data, 'inline'),
            Data::bool($data, 'risky'),
        );
        $model->raw = $data;

        return $model;
    }
}
