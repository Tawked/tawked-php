<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Partner;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class ProfileWebhook extends Model
{
    public function __construct(
        public readonly bool $configured,
        public readonly ?string $url,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::bool($data, 'configured'),
            Data::nullableString($data, 'url'),
        );
        $model->raw = $data;

        return $model;
    }
}
