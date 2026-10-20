<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `EmailError` object.
 *
 * Contract type: `EmailError`.
 */
final class EmailError extends Model
{
    public function __construct(
        public readonly string $code,
        public readonly ?string $message,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'code'),
            Data::nullableString($data, 'message'),
        );
        $model->raw = $data;

        return $model;
    }
}
