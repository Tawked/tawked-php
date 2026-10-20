<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Webhook;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class EmailRecipientEventDataTemplate extends Model
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $alias,
        public readonly ?int $version,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'id'),
            Data::nullableString($data, 'alias'),
            Data::nullableInt($data, 'version'),
        );
        $model->raw = $data;

        return $model;
    }
}
