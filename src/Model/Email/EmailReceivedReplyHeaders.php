<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class EmailReceivedReplyHeaders extends Model
{
    public function __construct(
        public readonly string $inReplyTo,
        public readonly string $references,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'In-Reply-To'),
            Data::string($data, 'References'),
        );
        $model->raw = $data;

        return $model;
    }
}
