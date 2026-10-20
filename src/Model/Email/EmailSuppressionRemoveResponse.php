<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class EmailSuppressionRemoveResponse extends Model
{
    public function __construct(
        public readonly string $address,
        public readonly bool $removed,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'address'),
            Data::bool($data, 'removed'),
        );
        $model->raw = $data;

        return $model;
    }
}
