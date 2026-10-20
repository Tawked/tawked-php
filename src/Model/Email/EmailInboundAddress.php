<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `EmailInboundAddress` object.
 *
 * Contract type: `EmailInboundAddress`.
 */
final class EmailInboundAddress extends Model
{
    public function __construct(
        public readonly string $id,
        public readonly string $kind,
        public readonly string $domain,
        public readonly string $example,
        public readonly string $state,
        public readonly ?EmailInboundAddressHeld $held,
        public readonly string $spamPolicy,
        public readonly ?EmailInboundAddressForward $forward,
        public readonly ?DateTimeImmutable $createdAt,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'id'),
            Data::string($data, 'kind'),
            Data::string($data, 'domain'),
            Data::string($data, 'example'),
            Data::string($data, 'state'),
            is_array($data['held'] ?? null) ? EmailInboundAddressHeld::fromArray(Data::stringKeys($data['held'])) : null,
            Data::string($data, 'spam_policy'),
            is_array($data['forward'] ?? null) ? EmailInboundAddressForward::fromArray(Data::stringKeys($data['forward'])) : null,
            Data::date($data, 'created_at'),
        );
        $model->raw = $data;

        return $model;
    }
}
