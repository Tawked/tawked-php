<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `EmailMetrics` object.
 *
 * Contract type: `EmailMetrics`.
 */
final class EmailMetrics extends Model
{
    /**
     * @param list<EmailMetricsData> $data
     */
    public function __construct(
        public readonly string $from,
        public readonly string $to,
        public readonly string $timezone,
        public readonly string $granularity,
        public readonly ?EmailMetricsDomain $domain,
        public readonly EmailMetricsTotals $totals,
        public readonly EmailMetricsRates $rates,
        public readonly array $data,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'from'),
            Data::string($data, 'to'),
            Data::string($data, 'timezone'),
            Data::string($data, 'granularity'),
            is_array($data['domain'] ?? null) ? EmailMetricsDomain::fromArray(Data::stringKeys($data['domain'])) : null,
            EmailMetricsTotals::fromArray(Data::object($data, 'totals') ?? []),
            EmailMetricsRates::fromArray(Data::object($data, 'rates') ?? []),
            array_map(static fn(array $item): EmailMetricsData => EmailMetricsData::fromArray($item), Data::objects($data, 'data')),
        );
        $model->raw = $data;

        return $model;
    }
}
