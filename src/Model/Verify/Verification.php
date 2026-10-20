<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Verify;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `Verification` object.
 *
 * Contract type: `Verification`.
 */
final class Verification extends Model
{
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly string $channel,
        /** E.164 */
        public readonly string $to,
        public readonly ?string $reference,
        public readonly int $attemptsRemaining,
        public readonly int $resendsRemaining,
        public readonly ?DateTimeImmutable $expiresAt,
        public readonly ?DateTimeImmutable $verifiedAt,
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
            Data::string($data, 'channel'),
            Data::string($data, 'to'),
            Data::nullableString($data, 'reference'),
            Data::int($data, 'attempts_remaining'),
            Data::int($data, 'resends_remaining'),
            Data::date($data, 'expires_at'),
            Data::date($data, 'verified_at'),
            Data::date($data, 'created_at'),
        );
        $model->raw = $data;

        return $model;
    }
}
