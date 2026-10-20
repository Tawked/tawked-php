<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Partner;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class PartnerEvent extends Model
{
    /**
     * @param array<string, mixed>|null $data
     */
    public function __construct(
        public readonly string $id,
        public readonly string $event,
        public readonly ?string $externalId,
        /** The webhook data of that event: webhooks.partner.events[event].data. */
        public readonly ?array $data,
        public readonly ?DateTimeImmutable $createdAt,
        public readonly ?EventDelivery $delivery,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'id'),
            Data::string($data, 'event'),
            Data::nullableString($data, 'external_id'),
            Data::object($data, 'data'),
            Data::date($data, 'created_at'),
            is_array($data['delivery'] ?? null) ? EventDelivery::fromArray(Data::stringKeys($data['delivery'])) : null,
        );
        $model->raw = $data;

        return $model;
    }
}
