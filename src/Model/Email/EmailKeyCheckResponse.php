<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class EmailKeyCheckResponse extends Model
{
    public function __construct(
        public readonly EmailKeyCheckResponseApplication $application,
        public readonly bool $canSend,
        /** On a test key only; a live key's answer has no mode. */
        public readonly ?string $mode,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            EmailKeyCheckResponseApplication::fromArray(Data::object($data, 'application') ?? []),
            Data::bool($data, 'can_send'),
            Data::nullableString($data, 'mode'),
        );
        $model->raw = $data;

        return $model;
    }
}
