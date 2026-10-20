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
final class WebhookEndpointSigning extends Model
{
    /**
     * @param list<string> $schemes
     */
    public function __construct(
        public readonly array $schemes,
        public readonly ?DateTimeImmutable $standardSince,
        public readonly ?DateTimeImmutable $previousValidUntil,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::strings($data, 'schemes'),
            Data::date($data, 'standard_since'),
            Data::date($data, 'previous_valid_until'),
        );
        $model->raw = $data;

        return $model;
    }
}
