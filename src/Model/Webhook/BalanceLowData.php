<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Webhook;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class BalanceLowData extends Model
{
    public function __construct(
        public readonly int $balanceHalalas,
        public readonly int $thresholdHalalas,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::int($data, 'balance_halalas'),
            Data::int($data, 'threshold_halalas'),
        );
        $model->raw = $data;

        return $model;
    }
}
