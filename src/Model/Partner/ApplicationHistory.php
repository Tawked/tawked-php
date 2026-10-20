<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Partner;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class ApplicationHistory extends Model
{
    /**
     * @param list<HistoryEvent> $events
     */
    public function __construct(
        public readonly ?string $externalId,
        public readonly array $events,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::nullableString($data, 'external_id'),
            array_map(static fn(array $item): HistoryEvent => HistoryEvent::fromArray($item), Data::objects($data, 'events')),
        );
        $model->raw = $data;

        return $model;
    }
}
