<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Webhook;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class WebhookEndpointLastDelivery extends Model
{
    public function __construct(
        public readonly string $status,
        public readonly ?int $code,
        public readonly ?DateTimeImmutable $at,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'status'),
            Data::nullableInt($data, 'code'),
            Data::date($data, 'at'),
        );
        $model->raw = $data;

        return $model;
    }
}
