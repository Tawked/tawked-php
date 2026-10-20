<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\WhatsApp;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class MessageError extends Model
{
    public function __construct(
        public readonly ?string $code,
        public readonly ?string $message,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::nullableString($data, 'code'),
            Data::nullableString($data, 'message'),
        );
        $model->raw = $data;

        return $model;
    }
}
