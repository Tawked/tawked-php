<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `EmailRecipient` object.
 *
 * Contract type: `EmailRecipient`.
 */
final class EmailRecipient extends Model
{
    public function __construct(
        public readonly string $id,
        public readonly string $address,
        public readonly ?string $name,
        public readonly string $type,
        public readonly string $status,
        public readonly ?DateTimeImmutable $sentAt,
        public readonly ?DateTimeImmutable $deliveredAt,
        public readonly ?DateTimeImmutable $bouncedAt,
        public readonly ?DateTimeImmutable $complainedAt,
        public readonly ?DateTimeImmutable $delayedAt,
        public readonly ?DateTimeImmutable $failedAt,
        public readonly ?EmailBounce $bounce,
        public readonly ?EmailError $error,
        public readonly ?DateTimeImmutable $openedAt,
        public readonly ?DateTimeImmutable $clickedAt,
        /** Counted here only when the message has one recipient; else on the message. */
        public readonly int $opens,
        public readonly int $clicks,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'id'),
            Data::string($data, 'address'),
            Data::nullableString($data, 'name'),
            Data::string($data, 'type'),
            Data::string($data, 'status'),
            Data::date($data, 'sent_at'),
            Data::date($data, 'delivered_at'),
            Data::date($data, 'bounced_at'),
            Data::date($data, 'complained_at'),
            Data::date($data, 'delayed_at'),
            Data::date($data, 'failed_at'),
            is_array($data['bounce'] ?? null) ? EmailBounce::fromArray(Data::stringKeys($data['bounce'])) : null,
            is_array($data['error'] ?? null) ? EmailError::fromArray(Data::stringKeys($data['error'])) : null,
            Data::date($data, 'opened_at'),
            Data::date($data, 'clicked_at'),
            Data::int($data, 'opens'),
            Data::int($data, 'clicks'),
        );
        $model->raw = $data;

        return $model;
    }
}
