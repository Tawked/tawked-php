<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class EmailReceivedTestResponse extends Model
{
    public function __construct(
        public readonly string $id,
        public readonly string $event,
        public readonly string $mode,
        public readonly int $deliveries,
        public readonly \Tawked\Model\Webhook\EmailReceivedEventData $data,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'id'),
            Data::string($data, 'event'),
            Data::string($data, 'mode'),
            Data::int($data, 'deliveries'),
            \Tawked\Model\Webhook\EmailReceivedEventData::fromArray(Data::object($data, 'data') ?? []),
        );
        $model->raw = $data;

        return $model;
    }
}
