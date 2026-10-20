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
final class EmailReceivedRaw extends Model
{
    public function __construct(
        public readonly string $downloadUrl,
        public readonly ?DateTimeImmutable $expiresAt,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'download_url'),
            Data::date($data, 'expires_at'),
        );
        $model->raw = $data;

        return $model;
    }
}
