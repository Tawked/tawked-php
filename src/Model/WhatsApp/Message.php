<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\WhatsApp;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `WhatsAppMessage` object.
 *
 * Contract type: `WhatsAppMessage`.
 */
final class Message extends Model
{
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        /** E.164 */
        public readonly string $to,
        public readonly string $template,
        public readonly ?string $lang,
        public readonly ?string $reference,
        public readonly int $priceHalalas,
        public readonly ?MessageError $error,
        public readonly ?DateTimeImmutable $createdAt,
        public readonly ?DateTimeImmutable $sentAt,
        public readonly ?DateTimeImmutable $deliveredAt,
        public readonly ?DateTimeImmutable $readAt,
        public readonly ?DateTimeImmutable $failedAt,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'id'),
            Data::string($data, 'status'),
            Data::string($data, 'to'),
            Data::string($data, 'template'),
            Data::nullableString($data, 'lang'),
            Data::nullableString($data, 'reference'),
            Data::int($data, 'price_halalas'),
            is_array($data['error'] ?? null) ? MessageError::fromArray(Data::stringKeys($data['error'])) : null,
            Data::date($data, 'created_at'),
            Data::date($data, 'sent_at'),
            Data::date($data, 'delivered_at'),
            Data::date($data, 'read_at'),
            Data::date($data, 'failed_at'),
        );
        $model->raw = $data;

        return $model;
    }
}
