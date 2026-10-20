<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\WhatsApp;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class TemplateSummary extends Model
{
    public function __construct(
        /** Meta's id of this language of the template; the template operations take it. */
        public readonly string $id,
        public readonly string $name,
        public readonly string $language,
        public readonly string $category,
        /** Meta's status: APPROVED, PENDING, REJECTED, PAUSED, ... */
        public readonly string $status,
        public readonly TemplatePlaceholders $placeholders,
        /** True only when the calling key is a connected app's and that app created the template. */
        public readonly bool $managed,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'id'),
            Data::string($data, 'name'),
            Data::string($data, 'language'),
            Data::string($data, 'category'),
            Data::string($data, 'status'),
            TemplatePlaceholders::fromArray(Data::object($data, 'placeholders') ?? []),
            Data::bool($data, 'managed'),
        );
        $model->raw = $data;

        return $model;
    }
}
