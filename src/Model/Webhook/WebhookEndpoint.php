<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Webhook;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `WebhookEndpoint` object.
 *
 * Contract type: `WebhookEndpoint`.
 */
final class WebhookEndpoint extends Model
{
    /**
     * @param list<string> $events
     */
    public function __construct(
        public readonly string $id,
        public readonly string $url,
        public readonly array $events,
        public readonly bool $enabled,
        /** receiving, off, failing or disabled_failing. */
        public readonly string $state,
        public readonly ?string $disabledReason,
        public readonly ?DateTimeImmutable $failingSince,
        public readonly bool $receivesTest,
        public readonly ?WebhookEndpointLastDelivery $lastDelivery,
        public readonly WebhookEndpointSigning $signing,
        public readonly ?DateTimeImmutable $createdAt,
        public readonly ?DateTimeImmutable $updatedAt,
        public readonly ?WebhookEndpointSecrets $secrets,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'id'),
            Data::string($data, 'url'),
            Data::strings($data, 'events'),
            Data::bool($data, 'enabled'),
            Data::string($data, 'state'),
            Data::nullableString($data, 'disabled_reason'),
            Data::date($data, 'failing_since'),
            Data::bool($data, 'receives_test'),
            is_array($data['last_delivery'] ?? null) ? WebhookEndpointLastDelivery::fromArray(Data::stringKeys($data['last_delivery'])) : null,
            WebhookEndpointSigning::fromArray(Data::object($data, 'signing') ?? []),
            Data::date($data, 'created_at'),
            Data::date($data, 'updated_at'),
            is_array($data['secrets'] ?? null) ? WebhookEndpointSecrets::fromArray(Data::stringKeys($data['secrets'])) : null,
        );
        $model->raw = $data;

        return $model;
    }
}
