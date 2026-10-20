<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Partner;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `VerifySettings` object.
 *
 * Contract type: `VerifySettings`.
 */
final class VerifySettings extends Model
{
    public function __construct(
        public readonly int $codeLength,
        public readonly int $ttlSeconds,
        public readonly int $maxAttempts,
        public readonly int $maxPerHour,
        /** Derived from the website; never accepted from a caller. */
        public readonly ?string $autofillDomain,
        public readonly bool $autofillDomainEnabled,
        public readonly ?string $androidAppHash,
        public readonly ?int $dailySpendCapHalalas,
        public readonly int $maxPerIpPerHour,
        public readonly int $maxNewDestinationsPerHour,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::int($data, 'code_length'),
            Data::int($data, 'ttl_seconds'),
            Data::int($data, 'max_attempts'),
            Data::int($data, 'max_per_hour'),
            Data::nullableString($data, 'autofill_domain'),
            Data::bool($data, 'autofill_domain_enabled'),
            Data::nullableString($data, 'android_app_hash'),
            Data::nullableInt($data, 'daily_spend_cap_halalas'),
            Data::int($data, 'max_per_ip_per_hour'),
            Data::int($data, 'max_new_destinations_per_hour'),
        );
        $model->raw = $data;

        return $model;
    }
}
