<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Webhook;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `MessageEventData` object.
 *
 * Contract type: `MessageEventData`.
 */
final class MessageEventData extends Model
{
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly string $applicationId,
        /** Masked the way the console shows it, such as "+9665•• ••• 000". */
        public readonly string $to,
        public readonly string $template,
        public readonly ?string $lang,
        public readonly ?string $reference,
        public readonly int $priceHalalas,
        public readonly ?\Tawked\Model\WhatsApp\MessageError $error,
        public readonly ?DateTimeImmutable $occurredAt,
        public readonly ?DateTimeImmutable $createdAt,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'id'),
            Data::string($data, 'status'),
            Data::string($data, 'application_id'),
            Data::string($data, 'to'),
            Data::string($data, 'template'),
            Data::nullableString($data, 'lang'),
            Data::nullableString($data, 'reference'),
            Data::int($data, 'price_halalas'),
            is_array($data['error'] ?? null) ? \Tawked\Model\WhatsApp\MessageError::fromArray(Data::stringKeys($data['error'])) : null,
            Data::date($data, 'occurred_at'),
            Data::date($data, 'created_at'),
        );
        $model->raw = $data;

        return $model;
    }
}
