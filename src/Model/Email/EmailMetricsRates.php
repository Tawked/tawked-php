<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class EmailMetricsRates extends Model
{
    public function __construct(
        public readonly ?float $deliveryRate,
        public readonly ?float $bounceRate,
        public readonly ?float $complaintRate,
        public readonly ?float $openRate,
        public readonly ?float $clickRate,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::nullableFloat($data, 'delivery_rate'),
            Data::nullableFloat($data, 'bounce_rate'),
            Data::nullableFloat($data, 'complaint_rate'),
            Data::nullableFloat($data, 'open_rate'),
            Data::nullableFloat($data, 'click_rate'),
        );
        $model->raw = $data;

        return $model;
    }
}
