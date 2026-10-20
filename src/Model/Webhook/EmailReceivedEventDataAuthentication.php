<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Webhook;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class EmailReceivedEventDataAuthentication extends Model
{
    public function __construct(
        public readonly ?string $spf,
        public readonly ?string $dkim,
        public readonly ?string $dmarc,
        public readonly ?string $dmarcPolicy,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::nullableString($data, 'spf'),
            Data::nullableString($data, 'dkim'),
            Data::nullableString($data, 'dmarc'),
            Data::nullableString($data, 'dmarc_policy'),
        );
        $model->raw = $data;

        return $model;
    }
}
