<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `EmailTemplate` object.
 *
 * Contract type: `EmailTemplate`.
 */
final class EmailTemplate extends Model
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $alias,
        public readonly string $name,
        public readonly string $state,
        public readonly ?int $publishedVersion,
        public readonly ?EmailTemplateVersion $published,
        public readonly ?EmailTemplateVersion $draft,
        public readonly ?DateTimeImmutable $createdAt,
        public readonly ?DateTimeImmutable $updatedAt,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'id'),
            Data::nullableString($data, 'alias'),
            Data::string($data, 'name'),
            Data::string($data, 'state'),
            Data::nullableInt($data, 'published_version'),
            is_array($data['published'] ?? null) ? EmailTemplateVersion::fromArray(Data::stringKeys($data['published'])) : null,
            is_array($data['draft'] ?? null) ? EmailTemplateVersion::fromArray(Data::stringKeys($data['draft'])) : null,
            Data::date($data, 'created_at'),
            Data::date($data, 'updated_at'),
        );
        $model->raw = $data;

        return $model;
    }
}
