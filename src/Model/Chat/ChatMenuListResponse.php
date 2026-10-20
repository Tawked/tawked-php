<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Chat;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class ChatMenuListResponse extends Model
{
    /**
     * @param list<ChatMenu> $data
     */
    public function __construct(
        public readonly array $data,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            array_map(static fn(array $item): ChatMenu => ChatMenu::fromArray($item), Data::objects($data, 'data')),
        );
        $model->raw = $data;

        return $model;
    }
}
