<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class EmailInboundAddressHeld extends Model
{
    public function __construct(
        public readonly string $reason,
        public readonly ?DateTimeImmutable $until,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'reason'),
            Data::date($data, 'until'),
        );
        $model->raw = $data;

        return $model;
    }
}
