<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `EmailTemplateVersionRow` object.
 *
 * Contract type: `EmailTemplateVersionRow`.
 */
final class EmailTemplateVersionRow extends Model
{
    public function __construct(
        public readonly string $id,
        public readonly int $number,
        public readonly string $state,
        public readonly string $subject,
        public readonly ?DateTimeImmutable $publishedAt,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'id'),
            Data::int($data, 'number'),
            Data::string($data, 'state'),
            Data::string($data, 'subject'),
            Data::date($data, 'published_at'),
        );
        $model->raw = $data;

        return $model;
    }
}
