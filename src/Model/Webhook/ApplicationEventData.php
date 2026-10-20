<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Webhook;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class ApplicationEventData extends Model
{
    public function __construct(
        /** Legacy name; always equals application_id. */
        public readonly string $serviceId,
        public readonly string $applicationId,
        public readonly ?string $externalId,
        public readonly ?string $status,
        public readonly ?string $reason,
        public readonly ?string $reasonCode,
        public readonly ?DateTimeImmutable $archivedAt,
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
            Data::nullableString($data, 'status'),
            Data::nullableString($data, 'reason'),
            Data::nullableString($data, 'reason_code'),
            Data::date($data, 'archived_at'),
        );
        $model->raw = $data;

        return $model;
    }
}
