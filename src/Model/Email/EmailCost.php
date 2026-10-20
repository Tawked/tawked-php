<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `EmailCost` object.
 *
 * Contract type: `EmailCost`.
 */
final class EmailCost extends Model
{
    public function __construct(
        /** Recipients handed over for sending; suppressed ones are not counted. */
        public readonly int $emails,
        public readonly int $suppressed,
        /** E-mails covered by this month's free allowance. */
        public readonly int $free,
        public readonly int $billed,
        /**
         * The message's cost before the wallet rounds to whole halalas, a decimal string with up to
         * three decimals: "0.3".
         */
        public readonly string $halalas,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::int($data, 'emails'),
            Data::int($data, 'suppressed'),
            Data::int($data, 'free'),
            Data::int($data, 'billed'),
            Data::string($data, 'halalas'),
        );
        $model->raw = $data;

        return $model;
    }
}
