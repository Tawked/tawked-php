<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Partner;

use Tawked\Internal\Data;
use Tawked\Model\Model;
use Tawked\Pagination\CursorPage;

/**
 * An object of the Tawked API.
 *
 * @implements CursorPage<PartnerVerification>
 */
final class VerificationPage extends Model implements CursorPage
{
    /**
     * @param list<PartnerVerification> $data
     */
    public function __construct(
        public readonly array $data,
        public readonly ?string $nextCursor,
        public readonly bool $hasMore,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            array_map(static fn(array $item): PartnerVerification => PartnerVerification::fromArray($item), Data::objects($data, 'data')),
            Data::nullableString($data, 'next_cursor'),
            Data::bool($data, 'has_more'),
        );
        $model->raw = $data;

        return $model;
    }

    /**
     * @return list<PartnerVerification>
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
    public const ITEM_CLASS = PartnerVerification::class;
}
