<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Webhook;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `EmailEventData` object.
 *
 * Contract type: `EmailEventData`.
 */
final class EmailEventData extends Model
{
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly string $applicationId,
        public readonly \Tawked\Model\Email\EmailAddress $from,
        /** The full address. */
        public readonly string $to,
        public readonly string $subject,
        public readonly ?string $reference,
        public readonly int $priceHalalas,
        public readonly ?\Tawked\Model\Email\EmailError $error,
        public readonly ?\Tawked\Model\Email\EmailBounce $bounce,
        public readonly ?DateTimeImmutable $scheduledAt,
        public readonly ?EmailEventDataDelay $delay,
        public readonly ?EmailEventDataSuppressed $suppressed,
        public readonly ?EmailEventDataOpen $open,
        public readonly ?EmailEventDataClick $click,
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
            Data::string($data, 'to'),
            Data::string($data, 'subject'),
            Data::nullableString($data, 'reference'),
            Data::int($data, 'price_halalas'),
            is_array($data['error'] ?? null) ? \Tawked\Model\Email\EmailError::fromArray(Data::stringKeys($data['error'])) : null,
            is_array($data['bounce'] ?? null) ? \Tawked\Model\Email\EmailBounce::fromArray(Data::stringKeys($data['bounce'])) : null,
            Data::date($data, 'scheduled_at'),
            is_array($data['delay'] ?? null) ? EmailEventDataDelay::fromArray(Data::stringKeys($data['delay'])) : null,
            is_array($data['suppressed'] ?? null) ? EmailEventDataSuppressed::fromArray(Data::stringKeys($data['suppressed'])) : null,
            is_array($data['open'] ?? null) ? EmailEventDataOpen::fromArray(Data::stringKeys($data['open'])) : null,
            is_array($data['click'] ?? null) ? EmailEventDataClick::fromArray(Data::stringKeys($data['click'])) : null,
            Data::date($data, 'occurred_at'),
            Data::date($data, 'created_at'),
        );
        $model->raw = $data;

        return $model;
    }
}
