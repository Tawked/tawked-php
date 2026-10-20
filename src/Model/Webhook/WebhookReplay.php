<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Webhook;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `WebhookReplay` object.
 *
 * Contract type: `WebhookReplay`.
 */
final class WebhookReplay extends Model
{
    public function __construct(
        public readonly string $id,
        public readonly string $state,
        public readonly int $matched,
        public readonly ?int $queued,
        public readonly ?DateTimeImmutable $since,
        public readonly ?DateTimeImmutable $until,
        public readonly ?DateTimeImmutable $createdAt,
        public readonly ?DateTimeImmutable $finishedAt,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'id'),
            Data::string($data, 'state'),
            Data::int($data, 'matched'),
            Data::nullableInt($data, 'queued'),
            Data::date($data, 'since'),
            Data::date($data, 'until'),
            Data::date($data, 'created_at'),
            Data::date($data, 'finished_at'),
        );
        $model->raw = $data;

        return $model;
    }
}
