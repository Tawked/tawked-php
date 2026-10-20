<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use Tawked\Internal\Data;
use Tawked\Model\Model;
use Tawked\Pagination\CursorPage;

/**
 * An object of the Tawked API.
 *
 * @implements CursorPage<EmailMessageExtended>
 */
final class EmailMessageListResponse extends Model implements CursorPage
{
    /**
     * @param list<EmailMessageExtended> $data
     */
    public function __construct(
        public readonly array $data,
        public readonly bool $hasMore,
        public readonly ?string $nextCursor,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            array_map(static fn(array $item): EmailMessageExtended => EmailMessageExtended::fromArray($item), Data::objects($data, 'data')),
            Data::bool($data, 'has_more'),
            Data::nullableString($data, 'next_cursor'),
        );
        $model->raw = $data;

        return $model;
    }

    /**
     * @return list<EmailMessageExtended>
     */
    public function items(): array
    {
        return $this->data;
    }

    public function cursor(): ?string
    {
        return $this->nextCursor;
    }

    public function more(): bool
    {
        return $this->hasMore && $this->cursor() !== null;
    }

    /** The class of this page's items. */
    public const ITEM_CLASS = EmailMessageExtended::class;
}
