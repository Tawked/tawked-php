<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class EmailMessageContentResponse extends Model
{
    public function __construct(
        public readonly string $id,
        public readonly string $subject,
        public readonly ?string $html,
        public readonly ?string $text,
        public readonly ?DateTimeImmutable $keptUntil,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'id'),
            Data::string($data, 'subject'),
            Data::nullableString($data, 'html'),
            Data::nullableString($data, 'text'),
            Data::date($data, 'kept_until'),
        );
        $model->raw = $data;

        return $model;
    }
}
