<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Webhook;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `DomainEventData` object.
 *
 * Contract type: `DomainEventData`.
 */
final class DomainEventData extends Model
{
    /**
     * @param list<DomainEventDataRecord> $records
     * @param array<string, mixed>|null $authentication
     */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $state,
        public readonly ?DateTimeImmutable $createdAt,
        public readonly ?DateTimeImmutable $verifiedAt,
        public readonly ?DateTimeImmutable $pendingUntil,
        public readonly array $records,
        public readonly DomainEventDataDefaults $defaults,
        /** How its mail authenticates (DKIM, the return path, DMARC), as the console shows it. */
        public readonly ?array $authentication,
        public readonly ?DomainEventDataTracking $tracking,
        public readonly ?DomainEventDataReceiving $receiving,
        public readonly ?string $previousState,
        public readonly ?string $reason,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'id'),
            Data::string($data, 'name'),
            Data::string($data, 'state'),
            Data::date($data, 'created_at'),
            Data::date($data, 'verified_at'),
            Data::date($data, 'pending_until'),
            array_map(static fn(array $item): DomainEventDataRecord => DomainEventDataRecord::fromArray($item), Data::objects($data, 'records')),
            DomainEventDataDefaults::fromArray(Data::object($data, 'defaults') ?? []),
            Data::object($data, 'authentication'),
            is_array($data['tracking'] ?? null) ? DomainEventDataTracking::fromArray(Data::stringKeys($data['tracking'])) : null,
            is_array($data['receiving'] ?? null) ? DomainEventDataReceiving::fromArray(Data::stringKeys($data['receiving'])) : null,
            Data::nullableString($data, 'previous_state'),
            Data::nullableString($data, 'reason'),
        );
        $model->raw = $data;

        return $model;
    }
}
