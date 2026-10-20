<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Webhook;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class EmailRecipientEventDataRecipient extends Model
{
    public function __construct(
        public readonly string $id,
        /** The full address, as `to`. */
        public readonly string $address,
        public readonly string $type,
        public readonly string $status,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'id'),
            Data::string($data, 'address'),
            Data::string($data, 'type'),
            Data::string($data, 'status'),
        );
        $model->raw = $data;

        return $model;
    }
}
