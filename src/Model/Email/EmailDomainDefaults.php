<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class EmailDomainDefaults extends Model
{
    public function __construct(
        public readonly ?string $fromName,
        public readonly ?string $fromLocal,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::nullableString($data, 'from_name'),
            Data::nullableString($data, 'from_local'),
        );
        $model->raw = $data;

        return $model;
    }
}
