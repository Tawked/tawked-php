<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Chat;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class ChatAttendantPublishResponse extends Model
{
    public function __construct(
        public readonly int $version,
        public readonly ?DateTimeImmutable $publishedAt,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::int($data, 'version'),
            Data::date($data, 'published_at'),
        );
        $model->raw = $data;

        return $model;
    }
}
