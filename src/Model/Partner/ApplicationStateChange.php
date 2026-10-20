<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Partner;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class ApplicationStateChange extends Model
{
    public function __construct(
        public readonly ?string $externalId,
        public readonly ?bool $disabled,
        public readonly ?bool $paused,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::nullableString($data, 'external_id'),
            Data::nullableBool($data, 'disabled'),
            Data::nullableBool($data, 'paused'),
        );
        $model->raw = $data;

        return $model;
    }
}
