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
final class EmailSendResponse extends Model
{
    /**
     * @param list<string> $cc
     * @param list<string> $bcc
     * @param list<string> $replyTo
     * @param list<EmailTag> $tags
     * @param list<EmailRecipientAccepted> $recipients
     */
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly EmailAddress $from,
        public readonly mixed $to,
        public readonly string $subject,
        public readonly ?string $reference,
        public readonly int $priceHalalas,
        public readonly ?DateTimeImmutable $createdAt,
        public readonly array $cc,
        public readonly array $bcc,
        public readonly array $replyTo,
        public readonly array $tags,
        public readonly ?DateTimeImmutable $scheduledAt,
        public readonly array $recipients,
        public readonly ?EmailCost $cost,
        /** test when a test key sent it: simulated, never delivered or charged. */
        public readonly ?string $mode,
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
            Data::int($data, 'price_halalas'),
            Data::date($data, 'created_at'),
            Data::strings($data, 'cc'),
            Data::strings($data, 'bcc'),
            Data::strings($data, 'reply_to'),
            array_map(static fn(array $item): EmailTag => EmailTag::fromArray($item), Data::objects($data, 'tags')),
            Data::date($data, 'scheduled_at'),
            array_map(static fn(array $item): EmailRecipientAccepted => EmailRecipientAccepted::fromArray($item), Data::objects($data, 'recipients')),
            is_array($data['cost'] ?? null) ? EmailCost::fromArray(Data::stringKeys($data['cost'])) : null,
            Data::nullableString($data, 'mode'),
        );
        $model->raw = $data;

        return $model;
    }
}
