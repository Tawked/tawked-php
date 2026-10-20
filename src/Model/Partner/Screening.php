<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Partner;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `Screening` object.
 *
 * Contract type: `Screening`.
 */
final class Screening extends Model
{
    /**
     * @param list<string> $rules
     */
    public function __construct(
        public readonly string $result,
        public readonly array $rules,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'result'),
            Data::strings($data, 'rules'),
        );
        $model->raw = $data;

        return $model;
    }
}
