<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `EmailAccepted` object.
 *
 * Contract type: `EmailAccepted`.
 */
final class EmailAccepted extends Model
{
    /**
     * @param list<string> $to
     * @param list<string> $cc
     * @param list<string> $bcc
     * @param list<string> $replyTo
     * @param list<EmailTag> $tags
     * @param list<EmailRecipientAccepted> $recipients
     */
    public function __construct(
        public readonly string $id,
        /**
         * A first answer says accepted, scheduled or failed; a replay reads the message as it
         * stands now, queued, sending and sent as accepted.
         */
        public readonly string $status,
        public readonly EmailAddress $from,
        public readonly array $to,
        public readonly array $cc,
        public readonly array $bcc,
        public readonly array $replyTo,
        public readonly string $subject,
        public readonly ?string $reference,
        public readonly array $tags,
        public readonly ?DateTimeImmutable $scheduledAt,
        public readonly array $recipients,
        /** The whole halalas the wallet gave for the message. */
        public readonly int $priceHalalas,
        public readonly EmailCost $cost,
        public readonly ?DateTimeImmutable $createdAt,
        /** test when a test key sent it: simulated, never delivered or charged. */
        public readonly string $mode,
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
            Data::strings($data, 'to'),
            Data::strings($data, 'cc'),
            Data::strings($data, 'bcc'),
            Data::strings($data, 'reply_to'),
            Data::string($data, 'subject'),
            Data::nullableString($data, 'reference'),
            array_map(static fn(array $item): EmailTag => EmailTag::fromArray($item), Data::objects($data, 'tags')),
            Data::date($data, 'scheduled_at'),
            array_map(static fn(array $item): EmailRecipientAccepted => EmailRecipientAccepted::fromArray($item), Data::objects($data, 'recipients')),
            Data::int($data, 'price_halalas'),
            EmailCost::fromArray(Data::object($data, 'cost') ?? []),
            Data::date($data, 'created_at'),
            Data::string($data, 'mode'),
        );
        $model->raw = $data;

        return $model;
    }
}
