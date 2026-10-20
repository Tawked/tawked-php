<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `EmailReceived` object.
 *
 * Contract type: `EmailReceived`.
 */
final class EmailReceived extends Model
{
    /**
     * @param list<string> $to
     * @param list<EmailReceivedHeaderTo> $headerTo
     * @param list<EmailReceivedCc> $cc
     * @param list<EmailReceivedReplyTo> $replyTo
     * @param list<string> $references
     * @param list<EmailReceivedHeader> $headers
     * @param list<EmailReceivedAttachment> $attachments
     */
    public function __construct(
        public readonly string $id,
        public readonly ?EmailReceivedAddress $address,
        public readonly array $to,
        public readonly ?EmailReceivedFrom $from,
        public readonly ?string $subject,
        public readonly bool $spam,
        public readonly bool $hasAttachments,
        public readonly int $attachmentsCount,
        public readonly ?int $size,
        public readonly string $status,
        public readonly ?EmailReceivedThread $thread,
        public readonly ?DateTimeImmutable $receivedAt,
        public readonly ?string $envelopeFrom,
        public readonly array $headerTo,
        public readonly array $cc,
        public readonly array $replyTo,
        public readonly ?string $messageId,
        public readonly ?string $inReplyTo,
        public readonly array $references,
        public readonly ?EmailReceivedReplyHeaders $replyHeaders,
        public readonly ?DateTimeImmutable $date,
        public readonly ?string $text,
        public readonly ?string $html,
        public readonly bool $textTruncated,
        public readonly bool $htmlTruncated,
        public readonly array $headers,
        public readonly array $attachments,
        public readonly int $attachmentsSkipped,
        public readonly EmailReceivedRaw $rawMessage,
        public readonly EmailReceivedAuthentication $authentication,
        public readonly ?string $virus,
        public readonly ?string $spamVerdict,
        public readonly ?string $parseError,
        public readonly mixed $forwarded,
        public readonly int $priceHalalas,
        public readonly string $billing,
        public readonly ?DateTimeImmutable $expiresAt,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'id'),
            is_array($data['address'] ?? null) ? EmailReceivedAddress::fromArray(Data::stringKeys($data['address'])) : null,
            Data::strings($data, 'to'),
            is_array($data['from'] ?? null) ? EmailReceivedFrom::fromArray(Data::stringKeys($data['from'])) : null,
            Data::nullableString($data, 'subject'),
            Data::bool($data, 'spam'),
            Data::bool($data, 'has_attachments'),
            Data::int($data, 'attachments_count'),
            Data::nullableInt($data, 'size'),
            Data::string($data, 'status'),
            is_array($data['thread'] ?? null) ? EmailReceivedThread::fromArray(Data::stringKeys($data['thread'])) : null,
            Data::date($data, 'received_at'),
            Data::nullableString($data, 'envelope_from'),
            array_map(static fn(array $item): EmailReceivedHeaderTo => EmailReceivedHeaderTo::fromArray($item), Data::objects($data, 'header_to')),
            array_map(static fn(array $item): EmailReceivedCc => EmailReceivedCc::fromArray($item), Data::objects($data, 'cc')),
            array_map(static fn(array $item): EmailReceivedReplyTo => EmailReceivedReplyTo::fromArray($item), Data::objects($data, 'reply_to')),
            Data::nullableString($data, 'message_id'),
            Data::nullableString($data, 'in_reply_to'),
            Data::strings($data, 'references'),
            is_array($data['reply_headers'] ?? null) ? EmailReceivedReplyHeaders::fromArray(Data::stringKeys($data['reply_headers'])) : null,
            Data::date($data, 'date'),
            Data::nullableString($data, 'text'),
            Data::nullableString($data, 'html'),
            Data::bool($data, 'text_truncated'),
            Data::bool($data, 'html_truncated'),
            array_map(static fn(array $item): EmailReceivedHeader => EmailReceivedHeader::fromArray($item), Data::objects($data, 'headers')),
            array_map(static fn(array $item): EmailReceivedAttachment => EmailReceivedAttachment::fromArray($item), Data::objects($data, 'attachments')),
            Data::int($data, 'attachments_skipped'),
            EmailReceivedRaw::fromArray(Data::object($data, 'raw') ?? []),
            EmailReceivedAuthentication::fromArray(Data::object($data, 'authentication') ?? []),
            Data::nullableString($data, 'virus'),
            Data::nullableString($data, 'spam_verdict'),
            Data::nullableString($data, 'parse_error'),
            $data['forwarded'] ?? null,
            Data::int($data, 'price_halalas'),
            Data::string($data, 'billing'),
            Data::date($data, 'expires_at'),
        );
        $model->raw = $data;

        return $model;
    }
}
