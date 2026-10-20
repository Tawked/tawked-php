<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Webhook;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class WebhookEndpointSecrets extends Model
{
    public function __construct(
        public readonly ?string $standard,
        public readonly ?string $tawked,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::nullableString($data, 'standard'),
            Data::nullableString($data, 'tawked'),
        );
        $model->raw = $data;

        return $model;
    }
}
