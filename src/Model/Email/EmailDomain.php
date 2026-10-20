<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `EmailDomain` object.
 *
 * Contract type: `EmailDomain`.
 */
final class EmailDomain extends Model
{
    /**
     * @param list<EmailDomainRecord> $records
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
        public readonly EmailDomainDefaults $defaults,
        /** How its mail authenticates (DKIM, the return path, DMARC), as the console shows it. */
        public readonly ?array $authentication,
        public readonly ?EmailDomainTracking $tracking,
        public readonly ?EmailDomainReceiving $receiving,
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
            array_map(static fn(array $item): EmailDomainRecord => EmailDomainRecord::fromArray($item), Data::objects($data, 'records')),
            EmailDomainDefaults::fromArray(Data::object($data, 'defaults') ?? []),
            Data::object($data, 'authentication'),
            is_array($data['tracking'] ?? null) ? EmailDomainTracking::fromArray(Data::stringKeys($data['tracking'])) : null,
            is_array($data['receiving'] ?? null) ? EmailDomainReceiving::fromArray(Data::stringKeys($data['receiving'])) : null,
        );
        $model->raw = $data;

        return $model;
    }

    /**
     * True when this call created it (201), false when it already existed (200). Null when the
     * object did not come from such a call.
     */
    public function wasCreated(): ?bool
    {
        $status = $this->meta()?->statusCode;

        return $status === null ? null : $status === 201;
    }
}
