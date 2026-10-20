<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Webhook;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class EmailRecipientEventDataOpen extends Model
{
    public function __construct(
        public readonly ?string $userAgent,
        public readonly ?string $ip,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::nullableString($data, 'user_agent'),
            Data::nullableString($data, 'ip'),
        );
        $model->raw = $data;

        return $model;
    }
}
