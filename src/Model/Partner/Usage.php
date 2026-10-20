<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Partner;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class Usage extends Model
{
    /**
     * @param list<UsageRow> $applications
     */
    public function __construct(
        public readonly ?DateTimeImmutable $from,
        public readonly ?DateTimeImmutable $to,
        public readonly int $balanceHalalas,
        public readonly array $applications,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::date($data, 'from'),
            Data::date($data, 'to'),
            Data::int($data, 'balance_halalas'),
            array_map(static fn(array $item): UsageRow => UsageRow::fromArray($item), Data::objects($data, 'applications')),
        );
        $model->raw = $data;

        return $model;
    }
}
