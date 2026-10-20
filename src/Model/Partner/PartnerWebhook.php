<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Partner;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `PartnerWebhook` object.
 *
 * Contract type: `PartnerWebhook`.
 */
final class PartnerWebhook extends Model
{
    /**
     * @param list<string> $events
     * @param list<string> $defaultEvents
     * @param list<string> $availableEvents
     */
    public function __construct(
        public readonly ?string $url,
        public readonly bool $configured,
        public readonly int $lowBalanceThresholdHalalas,
        public readonly array $events,
        public readonly array $defaultEvents,
        public readonly array $availableEvents,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::nullableString($data, 'url'),
            Data::bool($data, 'configured'),
            Data::int($data, 'low_balance_threshold_halalas'),
            Data::strings($data, 'events'),
            Data::strings($data, 'default_events'),
            Data::strings($data, 'available_events'),
        );
        $model->raw = $data;

        return $model;
    }
}
