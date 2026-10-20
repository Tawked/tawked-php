<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Partner;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class Profile extends Model
{
    public function __construct(
        public readonly string $nameAr,
        public readonly ?string $nameEn,
        public readonly ?string $slug,
        public readonly string $approvalMode,
        public readonly int $balanceHalalas,
        public readonly int $lowBalanceThresholdHalalas,
        public readonly ProfilePrices $prices,
        public readonly int $rateLimitPerMinute,
        public readonly ProfileKey $key,
        public readonly ProfileWebhook $webhook,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'name_ar'),
            Data::nullableString($data, 'name_en'),
            Data::nullableString($data, 'slug'),
            Data::string($data, 'approval_mode'),
            Data::int($data, 'balance_halalas'),
            Data::int($data, 'low_balance_threshold_halalas'),
            ProfilePrices::fromArray(Data::object($data, 'prices') ?? []),
            Data::int($data, 'rate_limit_per_minute'),
            ProfileKey::fromArray(Data::object($data, 'key') ?? []),
            ProfileWebhook::fromArray(Data::object($data, 'webhook') ?? []),
        );
        $model->raw = $data;

        return $model;
    }
}
