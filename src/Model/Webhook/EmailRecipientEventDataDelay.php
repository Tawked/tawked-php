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
final class EmailRecipientEventDataDelay extends Model
{
    public function __construct(
        public readonly ?string $type,
        public readonly ?DateTimeImmutable $expiresAt,
        public readonly ?string $diagnostic,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::nullableString($data, 'type'),
            Data::date($data, 'expires_at'),
            Data::nullableString($data, 'diagnostic'),
        );
        $model->raw = $data;

        return $model;
    }
}
