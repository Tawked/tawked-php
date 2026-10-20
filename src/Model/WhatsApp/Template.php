<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\WhatsApp;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `WhatsAppTemplate` object.
 *
 * Contract type: `WhatsAppTemplate`.
 */
final class Template extends Model
{
    /**
     * @param list<TemplateComponent> $components
     * @param array<string, string> $examples
     */
    public function __construct(
        /** Meta's id of this language of the template. */
        public readonly string $id,
        public readonly string $name,
        public readonly string $language,
        public readonly string $category,
        /** Meta's status: APPROVED, PENDING, REJECTED, PAUSED, ... */
        public readonly string $status,
        /**
         * Meta's quality score in capitals (GREEN, YELLOW, RED, UNKNOWN), null when Meta gives
         * none.
         */
        public readonly ?string $quality,
        public readonly ?string $rejectedReason,
        public readonly array $components,
        /**
         * The sample values by placeholder key (header:<key>, body:<key>, button:<index>). Always
         * an object: an empty one, {}, for a template with no placeholders.
         */
        public readonly array $examples,
        public readonly TemplatePlaceholders $placeholders,
        /**
         * True only when the calling key is a connected app's and that app created the template
         * through this application.
         */
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
            Data::nullableString($data, 'quality'),
            Data::nullableString($data, 'rejected_reason'),
            array_map(static fn(array $item): TemplateComponent => TemplateComponent::fromArray($item), Data::objects($data, 'components')),
            Data::stringMap($data, 'examples') ?? [],
            TemplatePlaceholders::fromArray(Data::object($data, 'placeholders') ?? []),
            Data::bool($data, 'managed'),
        );
        $model->raw = $data;

        return $model;
    }
}
