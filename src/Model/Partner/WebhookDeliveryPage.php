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
 * @implements CursorPage<WebhookDelivery>
 */
final class WebhookDeliveryPage extends Model implements CursorPage
{
    /**
     * @param list<WebhookDelivery> $data
     */
    public function __construct(
        public readonly array $data,
        public readonly ?string $nextCursor,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            array_map(static fn(array $item): WebhookDelivery => WebhookDelivery::fromArray($item), Data::objects($data, 'data')),
            Data::nullableString($data, 'next_cursor'),
        );
        $model->raw = $data;

        return $model;
    }

    /**
     * @return list<WebhookDelivery>
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
        return $this->cursor() !== null;
    }

    /** The class of this page's items. */
    public const ITEM_CLASS = WebhookDelivery::class;
}
