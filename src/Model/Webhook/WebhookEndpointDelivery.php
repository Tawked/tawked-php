<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Webhook;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `WebhookEndpointDelivery` object.
 *
 * Contract type: `WebhookEndpointDelivery`.
 */
final class WebhookEndpointDelivery extends Model
{
    /**
     * @param array<string, mixed>|null $payload
     * @param list<mixed> $attemptLog
     */
    public function __construct(
        public readonly string $id,
        public readonly ?string $eventId,
        public readonly string $event,
        public readonly string $mode,
        public readonly string $status,
        public readonly int $attempts,
        public readonly ?int $lastStatusCode,
        public readonly ?string $lastError,
        public readonly ?string $replayOfId,
        public readonly ?DateTimeImmutable $createdAt,
        public readonly ?DateTimeImmutable $deliveredAt,
        public readonly ?DateTimeImmutable $nextAttemptAt,
        public readonly ?array $payload,
        public readonly array $attemptLog,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'id'),
            Data::nullableString($data, 'event_id'),
            Data::string($data, 'event'),
            Data::string($data, 'mode'),
            Data::string($data, 'status'),
            Data::int($data, 'attempts'),
            Data::nullableInt($data, 'last_status_code'),
            Data::nullableString($data, 'last_error'),
            Data::nullableString($data, 'replay_of_id'),
            Data::date($data, 'created_at'),
            Data::date($data, 'delivered_at'),
            Data::date($data, 'next_attempt_at'),
            Data::object($data, 'payload'),
            Data::list($data, 'attempt_log'),
        );
        $model->raw = $data;

        return $model;
    }
}
