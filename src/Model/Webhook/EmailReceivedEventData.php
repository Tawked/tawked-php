<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Webhook;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `EmailReceivedEventData` object.
 *
 * Contract type: `EmailReceivedEventData`.
 */
final class EmailReceivedEventData extends Model
{
    /**
     * @param list<string> $to
     * @param list<EmailReceivedEventDataAttachment> $attachments
     */
    public function __construct(
        public readonly string $id,
        public readonly ?EmailReceivedEventDataAddress $address,
        public readonly array $to,
        public readonly ?EmailReceivedEventDataFrom $from,
        public readonly ?string $subject,
        public readonly bool $spam,
        public readonly bool $hasAttachments,
        public readonly int $attachmentsCount,
        public readonly ?int $size,
        public readonly string $status,
        public readonly ?EmailReceivedEventDataThread $thread,
        public readonly ?DateTimeImmutable $receivedAt,
        public readonly ?string $envelopeFrom,
        public readonly ?string $messageId,
        public readonly ?string $inReplyTo,
        public readonly EmailReceivedEventDataAuthentication $authentication,
        public readonly string $parse,
        public readonly array $attachments,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'id'),
            is_array($data['address'] ?? null) ? EmailReceivedEventDataAddress::fromArray(Data::stringKeys($data['address'])) : null,
            Data::strings($data, 'to'),
            is_array($data['from'] ?? null) ? EmailReceivedEventDataFrom::fromArray(Data::stringKeys($data['from'])) : null,
            Data::nullableString($data, 'subject'),
            Data::bool($data, 'spam'),
            Data::bool($data, 'has_attachments'),
            Data::int($data, 'attachments_count'),
            Data::nullableInt($data, 'size'),
            Data::string($data, 'status'),
            is_array($data['thread'] ?? null) ? EmailReceivedEventDataThread::fromArray(Data::stringKeys($data['thread'])) : null,
            Data::date($data, 'received_at'),
            Data::nullableString($data, 'envelope_from'),
            Data::nullableString($data, 'message_id'),
            Data::nullableString($data, 'in_reply_to'),
            EmailReceivedEventDataAuthentication::fromArray(Data::object($data, 'authentication') ?? []),
            Data::string($data, 'parse'),
            array_map(static fn(array $item): EmailReceivedEventDataAttachment => EmailReceivedEventDataAttachment::fromArray($item), Data::objects($data, 'attachments')),
        );
        $model->raw = $data;

        return $model;
    }
}
