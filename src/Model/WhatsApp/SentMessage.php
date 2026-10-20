<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\WhatsApp;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class SentMessage extends Model
{
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly string $to,
        public readonly string $template,
        public readonly ?string $lang,
        public readonly ?string $reference,
        public readonly int $priceHalalas,
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
            Data::string($data, 'to'),
            Data::string($data, 'template'),
            Data::nullableString($data, 'lang'),
            Data::nullableString($data, 'reference'),
            Data::int($data, 'price_halalas'),
            Data::date($data, 'created_at'),
        );
        $model->raw = $data;

        return $model;
    }
}
