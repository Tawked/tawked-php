<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `EmailMessage` object.
 *
 * Contract type: `EmailMessage`.
 */
final class EmailMessage extends Model
{
    /**
     * @param list<EmailAttachment> $attachments
     */
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly EmailAddress $from,
        public readonly string $to,
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
            Data::string($data, 'to'),
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
        );
        $model->raw = $data;

        return $model;
    }
}
