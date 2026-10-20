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
final class DomainEventDataTracking extends Model
{
    public function __construct(
        public readonly bool $opens,
        public readonly bool $clicks,
        /** The tracking host (links.<domain>); null while tracking is off. */
        public readonly ?string $subdomain,
        /** off while neither opens nor clicks are tracked (DomainPresenter::tracking). */
        public readonly string $state,
        /** cname_missing, cname_elsewhere, proxied, tls_not_ready, edge_unreachable or ses_failed. */
        public readonly ?string $problem,
        public readonly ?DateTimeImmutable $checkedAt,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::bool($data, 'opens'),
            Data::bool($data, 'clicks'),
            Data::nullableString($data, 'subdomain'),
            Data::string($data, 'state'),
            Data::nullableString($data, 'problem'),
            Data::date($data, 'checked_at'),
        );
        $model->raw = $data;

        return $model;
    }
}
