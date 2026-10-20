<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\WhatsApp;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class DeletedTemplate extends Model
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $language,
        public readonly bool $deleted,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'id'),
            Data::string($data, 'name'),
            Data::string($data, 'language'),
            Data::bool($data, 'deleted'),
        );
        $model->raw = $data;

        return $model;
    }
}
