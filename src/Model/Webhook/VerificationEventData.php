<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Webhook;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `VerificationEventData` object.
 *
 * Contract type: `VerificationEventData`.
 */
final class VerificationEventData extends Model
{
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly string $applicationId,
        public readonly ?string $reference,
        public readonly string $channel,
        /** E.164, unmasked */
        public readonly string $to,
        public readonly string $mode,
        public readonly int $attempts,
        public readonly ?DateTimeImmutable $expiresAt,
        public readonly ?DateTimeImmutable $verifiedAt,
        public readonly ?DateTimeImmutable $createdAt,
        public readonly ?string $externalId,
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
            Data::nullableString($data, 'reference'),
            Data::string($data, 'channel'),
            Data::string($data, 'to'),
            Data::string($data, 'mode'),
            Data::int($data, 'attempts'),
            Data::date($data, 'expires_at'),
            Data::date($data, 'verified_at'),
            Data::date($data, 'created_at'),
            Data::nullableString($data, 'external_id'),
        );
        $model->raw = $data;

        return $model;
    }
}
