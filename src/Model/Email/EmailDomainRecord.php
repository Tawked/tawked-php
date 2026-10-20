<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class EmailDomainRecord extends Model
{
    public function __construct(
        public readonly string $purpose,
        public readonly string $type,
        public readonly string $name,
        public readonly string $value,
        public readonly ?int $priority,
        /** found, missing, or what the check saw. */
        public readonly string $state,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'purpose'),
            Data::string($data, 'type'),
            Data::string($data, 'name'),
            Data::string($data, 'value'),
            Data::nullableInt($data, 'priority'),
            Data::string($data, 'state'),
        );
        $model->raw = $data;

        return $model;
    }
}
