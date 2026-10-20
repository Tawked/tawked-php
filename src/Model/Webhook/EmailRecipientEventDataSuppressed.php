<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Webhook;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class EmailRecipientEventDataSuppressed extends Model
{
    public function __construct(
        public readonly string $reason,
        public readonly string $by,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'reason'),
            Data::string($data, 'by'),
        );
        $model->raw = $data;

        return $model;
    }
}
