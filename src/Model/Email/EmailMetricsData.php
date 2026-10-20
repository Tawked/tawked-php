<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class EmailMetricsData extends Model
{
    public function __construct(
        public readonly string $period,
        public readonly int $sent,
        public readonly int $delivered,
        public readonly int $deliveryDelayed,
        public readonly int $bounced,
        public readonly int $bouncedPermanent,
        public readonly int $bouncedTransient,
        public readonly int $complained,
        public readonly int $failed,
        public readonly int $suppressed,
        public readonly int $opened,
        public readonly int $uniqueOpened,
        public readonly int $clicked,
        public readonly int $uniqueClicked,
        public readonly int $openTrackedTargets,
        public readonly int $clickTrackedTargets,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'period'),
            Data::int($data, 'sent'),
            Data::int($data, 'delivered'),
            Data::int($data, 'delivery_delayed'),
            Data::int($data, 'bounced'),
            Data::int($data, 'bounced_permanent'),
            Data::int($data, 'bounced_transient'),
            Data::int($data, 'complained'),
            Data::int($data, 'failed'),
            Data::int($data, 'suppressed'),
            Data::int($data, 'opened'),
            Data::int($data, 'unique_opened'),
            Data::int($data, 'clicked'),
            Data::int($data, 'unique_clicked'),
            Data::int($data, 'open_tracked_targets'),
            Data::int($data, 'click_tracked_targets'),
        );
        $model->raw = $data;

        return $model;
    }
}
