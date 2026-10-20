<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Partner;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class UsageRow extends Model
{
    public function __construct(
        public readonly ?string $externalId,
        public readonly string $applicationId,
        public readonly string $nameAr,
        public readonly int $sent,
        public readonly int $verified,
        public readonly int $spendHalalas,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::nullableString($data, 'external_id'),
            Data::string($data, 'application_id'),
            Data::string($data, 'name_ar'),
            Data::int($data, 'sent'),
            Data::int($data, 'verified'),
            Data::int($data, 'spend_halalas'),
        );
        $model->raw = $data;

        return $model;
    }
}
