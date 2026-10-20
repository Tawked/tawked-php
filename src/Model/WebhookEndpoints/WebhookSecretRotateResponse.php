<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\WebhookEndpoints;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class WebhookSecretRotateResponse extends Model
{
    public function __construct(
        /** The endpoint's id. */
        public readonly string $id,
        public readonly ?DateTimeImmutable $previousValidUntil,
        public readonly ?DateTimeImmutable $tawkedSwitchedAt,
        public readonly WebhookSecretRotateResponseSecrets $secrets,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'id'),
            Data::date($data, 'previous_valid_until'),
            Data::date($data, 'tawked_switched_at'),
            WebhookSecretRotateResponseSecrets::fromArray(Data::object($data, 'secrets') ?? []),
        );
        $model->raw = $data;

        return $model;
    }
}
