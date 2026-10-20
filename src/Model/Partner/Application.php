<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Partner;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `ApplicationRow` object.
 *
 * Contract type: `ApplicationRow`.
 */
final class Application extends Model
{
    /**
     * @param array<string, mixed>|null $merchant
     * @param array<string, string>|null $metadata
     */
    public function __construct(
        /** Legacy name; always equals application_id. */
        public readonly string $serviceId,
        public readonly string $applicationId,
        public readonly ?string $externalId,
        public readonly string $status,
        public readonly ?Screening $screening,
        public readonly VerifySettings $verify,
        public readonly bool $paused,
        public readonly string $nameAr,
        public readonly string $nameEn,
        public readonly ?string $website,
        public readonly ?string $industry,
        /** The `merchant` object the partner sent, stored as given. */
        public readonly ?array $merchant,
        public readonly ?string $logoUrl,
        public readonly ?array $metadata,
        public readonly ?string $statusMessage,
        public readonly ?DateTimeImmutable $approvedAt,
        public readonly bool $disabled,
        public readonly ?DateTimeImmutable $disabledAt,
        public readonly bool $archived,
        public readonly ?DateTimeImmutable $archivedAt,
        public readonly ?DateTimeImmutable $pausedAt,
        public readonly ?string $pauseReason,
        public readonly ?DateTimeImmutable $createdAt,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'service_id'),
            Data::string($data, 'application_id'),
            Data::nullableString($data, 'external_id'),
            Data::string($data, 'status'),
            is_array($data['screening'] ?? null) ? Screening::fromArray(Data::stringKeys($data['screening'])) : null,
            VerifySettings::fromArray(Data::object($data, 'verify') ?? []),
            Data::bool($data, 'paused'),
            Data::string($data, 'name_ar'),
            Data::string($data, 'name_en'),
            Data::nullableString($data, 'website'),
            Data::nullableString($data, 'industry'),
            Data::object($data, 'merchant'),
            Data::nullableString($data, 'logo_url'),
            Data::stringMap($data, 'metadata'),
            Data::nullableString($data, 'status_message'),
            Data::date($data, 'approved_at'),
            Data::bool($data, 'disabled'),
            Data::date($data, 'disabled_at'),
            Data::bool($data, 'archived'),
            Data::date($data, 'archived_at'),
            Data::date($data, 'paused_at'),
            Data::nullableString($data, 'pause_reason'),
            Data::date($data, 'created_at'),
        );
        $model->raw = $data;

        return $model;
    }
}
