<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Chat;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `ChatMenu` object.
 *
 * Contract type: `ChatMenu`.
 */
final class ChatMenu extends Model
{
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        /** The link as saved, tokens and the page's own key included. */
        public readonly string $url,
        /** An https link to the 64 px PNG Tawked Chat shows; null with no icon. */
        public readonly ?string $iconUrl,
        /**
         * The last check of whether the site may be framed inside Tawked Chat: opens, window
         * (agents get "Open in a new window"), or unknown.
         */
        public readonly string $frame,
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
            Data::string($data, 'title'),
            Data::string($data, 'url'),
            Data::nullableString($data, 'icon_url'),
            Data::string($data, 'frame'),
            Data::date($data, 'created_at'),
            Data::date($data, 'updated_at'),
        );
        $model->raw = $data;

        return $model;
    }
}
