<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class EmailReceivedCc extends Model
{
    public function __construct(
        public readonly ?string $name,
        public readonly string $address,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::nullableString($data, 'name'),
            Data::string($data, 'address'),
        );
        $model->raw = $data;

        return $model;
    }
}
