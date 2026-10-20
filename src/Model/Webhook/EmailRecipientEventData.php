<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Webhook;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `EmailRecipientEventData` object.
 *
 * Contract type: `EmailRecipientEventData`.
 */
final class EmailRecipientEventData extends Model
{
    /**
     * @param list<string> $recipients
     * @param list<\Tawked\Model\Email\EmailTag> $tags
     */
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly string $applicationId,
        public readonly \Tawked\Model\Email\EmailAddress $from,
        /**
         * This recipient's full address; null on an open or a click of a message of several
         * recipients, which `recipients` lists.
         */
        public readonly ?string $to,
        public readonly ?EmailRecipientEventDataRecipient $recipient,
        public readonly array $recipients,
        public readonly array $tags,
        public readonly ?string $mode,
        public readonly ?EmailRecipientEventDataTemplate $template,
        public readonly string $subject,
        public readonly ?string $reference,
        public readonly int $priceHalalas,
        public readonly ?\Tawked\Model\Email\EmailError $error,
        public readonly ?\Tawked\Model\Email\EmailBounce $bounce,
        public readonly ?DateTimeImmutable $scheduledAt,
        public readonly ?EmailRecipientEventDataDelay $delay,
        public readonly ?EmailRecipientEventDataSuppressed $suppressed,
        public readonly ?EmailRecipientEventDataOpen $open,
        public readonly ?EmailRecipientEventDataClick $click,
        public readonly ?DateTimeImmutable $occurredAt,
        public readonly ?DateTimeImmutable $createdAt,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'id'),
            Data::string($data, 'status'),
            Data::string($data, 'application_id'),
            \Tawked\Model\Email\EmailAddress::fromArray(Data::object($data, 'from') ?? []),
            Data::nullableString($data, 'to'),
            is_array($data['recipient'] ?? null) ? EmailRecipientEventDataRecipient::fromArray(Data::stringKeys($data['recipient'])) : null,
            Data::strings($data, 'recipients'),
            array_map(static fn(array $item): \Tawked\Model\Email\EmailTag => \Tawked\Model\Email\EmailTag::fromArray($item), Data::objects($data, 'tags')),
            Data::nullableString($data, 'mode'),
            is_array($data['template'] ?? null) ? EmailRecipientEventDataTemplate::fromArray(Data::stringKeys($data['template'])) : null,
            Data::string($data, 'subject'),
            Data::nullableString($data, 'reference'),
            Data::int($data, 'price_halalas'),
            is_array($data['error'] ?? null) ? \Tawked\Model\Email\EmailError::fromArray(Data::stringKeys($data['error'])) : null,
            is_array($data['bounce'] ?? null) ? \Tawked\Model\Email\EmailBounce::fromArray(Data::stringKeys($data['bounce'])) : null,
            Data::date($data, 'scheduled_at'),
            is_array($data['delay'] ?? null) ? EmailRecipientEventDataDelay::fromArray(Data::stringKeys($data['delay'])) : null,
            is_array($data['suppressed'] ?? null) ? EmailRecipientEventDataSuppressed::fromArray(Data::stringKeys($data['suppressed'])) : null,
            is_array($data['open'] ?? null) ? EmailRecipientEventDataOpen::fromArray(Data::stringKeys($data['open'])) : null,
            is_array($data['click'] ?? null) ? EmailRecipientEventDataClick::fromArray(Data::stringKeys($data['click'])) : null,
            Data::date($data, 'occurred_at'),
            Data::date($data, 'created_at'),
        );
        $model->raw = $data;

        return $model;
    }
}
