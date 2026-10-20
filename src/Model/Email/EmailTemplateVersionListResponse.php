<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class EmailTemplateVersionListResponse extends Model
{
    /**
     * @param list<EmailTemplateVersionRow> $data
     */
    public function __construct(
        public readonly array $data,
        /** Always false: every version is answered at once. */
        public readonly bool $hasMore,
        public readonly mixed $nextCursor,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            array_map(static fn(array $item): EmailTemplateVersionRow => EmailTemplateVersionRow::fromArray($item), Data::objects($data, 'data')),
            Data::bool($data, 'has_more'),
            $data['next_cursor'] ?? null,
        );
        $model->raw = $data;

        return $model;
    }
}
