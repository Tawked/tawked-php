<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Partner;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `ApplicationSummary` object.
 *
 * Contract type: `ApplicationSummary`.
 */
final class ApplicationSummary extends Model
{
    public function __construct(
        /** Legacy name; always equals application_id. */
        public readonly string $serviceId,
        public readonly string $applicationId,
        public readonly ?string $externalId,
        public readonly string $status,
        public readonly ?Screening $screening,
        public readonly VerifySettings $verify,
        public readonly bool $paused,
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
