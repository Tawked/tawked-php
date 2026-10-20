<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `EmailTag` object.
 *
 * Contract type: `EmailTag`.
 */
final class EmailTag extends Model
{
    public function __construct(
        public readonly string $name,
        public readonly string $value,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'name'),
            Data::string($data, 'value'),
        );
        $model->raw = $data;

        return $model;
    }
}
