<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\WebhookEndpoints;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class WebhookEndpointListResponse extends Model
{
    /**
     * @param list<\Tawked\Model\Webhook\WebhookEndpoint> $data
     */
    public function __construct(
        public readonly array $data,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            array_map(static fn(array $item): \Tawked\Model\Webhook\WebhookEndpoint => \Tawked\Model\Webhook\WebhookEndpoint::fromArray($item), Data::objects($data, 'data')),
        );
        $model->raw = $data;

        return $model;
    }
}
