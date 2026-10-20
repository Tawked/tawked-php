<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `EmailReceivedAttachment` object.
 *
 * Contract type: `EmailReceivedAttachment`.
 */
final class EmailReceivedAttachment extends Model
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $filename,
        public readonly string $contentType,
        public readonly ?string $contentId,
        public readonly bool $inline,
        public readonly int $size,
        public readonly bool $risky,
        /** Tawked's own signed link, valid for an hour; no key needed. */
        public readonly string $downloadUrl,
        public readonly ?DateTimeImmutable $downloadExpiresAt,
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
            Data::nullableString($data, 'content_id'),
            Data::bool($data, 'inline'),
            Data::int($data, 'size'),
            Data::bool($data, 'risky'),
            Data::string($data, 'download_url'),
            Data::date($data, 'download_expires_at'),
        );
        $model->raw = $data;

        return $model;
    }
}
