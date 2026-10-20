<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Partner;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `WebhookDelivery` object.
 *
 * Contract type: `WebhookDelivery`.
 */
final class WebhookDelivery extends Model
{
    public function __construct(
        public readonly string $id,
        public readonly string $event,
        public readonly string $status,
        public readonly int $attempts,
        public readonly ?int $lastStatusCode,
        /**
         * A fixed code: http_<status>, timeout, connect_failed, request_failed, or the address
         * refusal.
         */
        public readonly ?string $lastError,
        public readonly ?DateTimeImmutable $nextAttemptAt,
        public readonly ?DateTimeImmutable $deliveredAt,
        public readonly ?DateTimeImmutable $createdAt,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'id'),
            Data::string($data, 'event'),
            Data::string($data, 'status'),
            Data::int($data, 'attempts'),
            Data::nullableInt($data, 'last_status_code'),
            Data::nullableString($data, 'last_error'),
            Data::date($data, 'next_attempt_at'),
            Data::date($data, 'delivered_at'),
            Data::date($data, 'created_at'),
        );
        $model->raw = $data;

        return $model;
    }
}
