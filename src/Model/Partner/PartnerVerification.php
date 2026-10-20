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
final class PartnerVerification extends Model
{
    public function __construct(
        public readonly string $id,
        public readonly string $applicationId,
        public readonly ?string $externalId,
        public readonly string $status,
        public readonly string $channel,
        /** E.164 */
        public readonly string $to,
        public readonly ?string $reference,
        public readonly int $attemptsRemaining,
        public readonly int $resendsRemaining,
        public readonly ?DateTimeImmutable $expiresAt,
        public readonly ?DateTimeImmutable $verifiedAt,
        public readonly ?DateTimeImmutable $createdAt,
        public readonly string $mode,
        public readonly string $lang,
        public readonly int $priceHalalas,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'id'),
            Data::string($data, 'application_id'),
            Data::nullableString($data, 'external_id'),
            Data::string($data, 'status'),
            Data::string($data, 'channel'),
            Data::string($data, 'to'),
            Data::nullableString($data, 'reference'),
            Data::int($data, 'attempts_remaining'),
            Data::int($data, 'resends_remaining'),
            Data::date($data, 'expires_at'),
            Data::date($data, 'verified_at'),
            Data::date($data, 'created_at'),
            Data::string($data, 'mode'),
            Data::string($data, 'lang'),
            Data::int($data, 'price_halalas'),
        );
        $model->raw = $data;

        return $model;
    }
}
