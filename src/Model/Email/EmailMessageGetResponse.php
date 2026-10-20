<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class EmailMessageGetResponse extends Model
{
    /**
     * @param list<EmailAttachment> $attachments
     * @param list<string> $cc
     * @param list<string> $bcc
     * @param list<string> $replyTo
     * @param list<EmailTag> $tags
     * @param list<EmailRecipient> $recipients
     * @param list<string> $headerNames
     */
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly EmailAddress $from,
        public readonly mixed $to,
        public readonly string $subject,
        public readonly ?string $reference,
        public readonly array $attachments,
        public readonly int $priceHalalas,
        public readonly ?EmailError $error,
        public readonly ?DateTimeImmutable $createdAt,
        public readonly ?DateTimeImmutable $sentAt,
        public readonly ?DateTimeImmutable $deliveredAt,
        public readonly ?DateTimeImmutable $bouncedAt,
        public readonly ?DateTimeImmutable $complainedAt,
        public readonly ?DateTimeImmutable $failedAt,
        public readonly array $cc,
        public readonly array $bcc,
        public readonly array $replyTo,
        public readonly array $tags,
        public readonly ?DateTimeImmutable $scheduledAt,
        public readonly array $recipients,
        public readonly ?EmailCost $cost,
        public readonly array $headerNames,
        public readonly ?DateTimeImmutable $canceledAt,
        public readonly ?string $batchId,
        /** test when a test key sent it: simulated, never delivered or charged. */
        public readonly ?string $mode,
        public readonly ?EmailMessageGetResponseTemplate $template,
        /**
         * What was tracked on this message, from its domain's settings when it was sent; null when
         * nothing was.
         */
        public readonly ?string $tracking,
        public readonly ?DateTimeImmutable $openedAt,
        public readonly ?DateTimeImmutable $clickedAt,
        /** Opens of the whole message: one pixel serves every recipient. */
        public readonly ?int $opens,
        public readonly ?int $clicks,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'id'),
            Data::string($data, 'status'),
            EmailAddress::fromArray(Data::object($data, 'from') ?? []),
            $data['to'] ?? null,
            Data::string($data, 'subject'),
            Data::nullableString($data, 'reference'),
            array_map(static fn(array $item): EmailAttachment => EmailAttachment::fromArray($item), Data::objects($data, 'attachments')),
            Data::int($data, 'price_halalas'),
            is_array($data['error'] ?? null) ? EmailError::fromArray(Data::stringKeys($data['error'])) : null,
            Data::date($data, 'created_at'),
            Data::date($data, 'sent_at'),
            Data::date($data, 'delivered_at'),
            Data::date($data, 'bounced_at'),
            Data::date($data, 'complained_at'),
            Data::date($data, 'failed_at'),
            Data::strings($data, 'cc'),
            Data::strings($data, 'bcc'),
            Data::strings($data, 'reply_to'),
            array_map(static fn(array $item): EmailTag => EmailTag::fromArray($item), Data::objects($data, 'tags')),
            Data::date($data, 'scheduled_at'),
            array_map(static fn(array $item): EmailRecipient => EmailRecipient::fromArray($item), Data::objects($data, 'recipients')),
            is_array($data['cost'] ?? null) ? EmailCost::fromArray(Data::stringKeys($data['cost'])) : null,
            Data::strings($data, 'header_names'),
            Data::date($data, 'canceled_at'),
            Data::nullableString($data, 'batch_id'),
            Data::nullableString($data, 'mode'),
            is_array($data['template'] ?? null) ? EmailMessageGetResponseTemplate::fromArray(Data::stringKeys($data['template'])) : null,
            Data::nullableString($data, 'tracking'),
            Data::date($data, 'opened_at'),
            Data::date($data, 'clicked_at'),
            Data::nullableInt($data, 'opens'),
            Data::nullableInt($data, 'clicks'),
        );
        $model->raw = $data;

        return $model;
    }
}
