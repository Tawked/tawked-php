<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `EmailBounce` object.
 *
 * Contract type: `EmailBounce`.
 */
final class EmailBounce extends Model
{
    public function __construct(
        public readonly string $type,
        public readonly ?string $subtype,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'type'),
            Data::nullableString($data, 'subtype'),
        );
        $model->raw = $data;

        return $model;
    }
}
