<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `EmailRecipientAccepted` object.
 *
 * Contract type: `EmailRecipientAccepted`.
 */
final class EmailRecipientAccepted extends Model
{
    public function __construct(
        public readonly string $id,
        public readonly string $address,
        public readonly ?string $name,
        public readonly string $type,
        /**
         * A first answer says queued, scheduled or suppressed; a replay reads the recipient as it
         * stands now.
         */
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
            Data::nullableString($data, 'name'),
            Data::string($data, 'type'),
            Data::string($data, 'status'),
        );
        $model->raw = $data;

        return $model;
    }
}
