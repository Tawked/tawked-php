<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `EmailReceivedListItem` object.
 *
 * Contract type: `EmailReceivedListItem`.
 */
final class EmailReceivedListItem extends Model
{
    /**
     * @param list<string> $to
     */
    public function __construct(
        public readonly string $id,
        public readonly ?EmailReceivedListItemAddress $address,
        public readonly array $to,
        public readonly ?EmailReceivedListItemFrom $from,
        public readonly ?string $subject,
        public readonly bool $spam,
        public readonly bool $hasAttachments,
        public readonly int $attachmentsCount,
        public readonly ?int $size,
        public readonly string $status,
        public readonly ?EmailReceivedListItemThread $thread,
        public readonly ?DateTimeImmutable $receivedAt,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'id'),
            is_array($data['address'] ?? null) ? EmailReceivedListItemAddress::fromArray(Data::stringKeys($data['address'])) : null,
            Data::strings($data, 'to'),
            is_array($data['from'] ?? null) ? EmailReceivedListItemFrom::fromArray(Data::stringKeys($data['from'])) : null,
            Data::nullableString($data, 'subject'),
            Data::bool($data, 'spam'),
            Data::bool($data, 'has_attachments'),
            Data::int($data, 'attachments_count'),
            Data::nullableInt($data, 'size'),
            Data::string($data, 'status'),
            is_array($data['thread'] ?? null) ? EmailReceivedListItemThread::fromArray(Data::stringKeys($data['thread'])) : null,
            Data::date($data, 'received_at'),
        );
        $model->raw = $data;

        return $model;
    }
}
