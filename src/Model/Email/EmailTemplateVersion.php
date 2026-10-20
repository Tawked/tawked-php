<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `EmailTemplateVersion` object.
 *
 * Contract type: `EmailTemplateVersion`.
 */
final class EmailTemplateVersion extends Model
{
    /**
     * @param list<string> $replyTo
     * @param list<EmailTemplateVersionVariable> $variables
     * @param list<string> $placeholders
     * @param list<EmailTemplateProblem> $problems
     */
    public function __construct(
        public readonly string $id,
        public readonly ?int $number,
        public readonly string $state,
        public readonly string $subject,
        /** Left out in the list's rows (email-templates). */
        public readonly ?string $html,
        /** Left out in the list's rows (email-templates). */
        public readonly ?string $text,
        public readonly ?EmailTemplateVersionFrom $from,
        public readonly array $replyTo,
        public readonly array $variables,
        public readonly array $placeholders,
        public readonly array $problems,
        public readonly ?DateTimeImmutable $publishedAt,
        public readonly ?DateTimeImmutable $updatedAt,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'id'),
            Data::nullableInt($data, 'number'),
            Data::string($data, 'state'),
            Data::string($data, 'subject'),
            Data::nullableString($data, 'html'),
            Data::nullableString($data, 'text'),
            is_array($data['from'] ?? null) ? EmailTemplateVersionFrom::fromArray(Data::stringKeys($data['from'])) : null,
            Data::strings($data, 'reply_to'),
            array_map(static fn(array $item): EmailTemplateVersionVariable => EmailTemplateVersionVariable::fromArray($item), Data::objects($data, 'variables')),
            Data::strings($data, 'placeholders'),
            array_map(static fn(array $item): EmailTemplateProblem => EmailTemplateProblem::fromArray($item), Data::objects($data, 'problems')),
            Data::date($data, 'published_at'),
            Data::date($data, 'updated_at'),
        );
        $model->raw = $data;

        return $model;
    }
}
