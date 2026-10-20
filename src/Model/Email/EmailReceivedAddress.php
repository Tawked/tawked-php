<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class EmailReceivedAddress extends Model
{
    public function __construct(
        public readonly string $id,
        public readonly string $domain,
        public readonly string $kind,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'id'),
            Data::string($data, 'domain'),
            Data::string($data, 'kind'),
        );
        $model->raw = $data;

        return $model;
    }
}
