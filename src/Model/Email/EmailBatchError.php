<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `EmailBatchError` object.
 *
 * Contract type: `EmailBatchError`.
 */
final class EmailBatchError extends Model
{
    public function __construct(
        /** The item's place in the batch. */
        public readonly int $index,
        public readonly string $error,
        public readonly string $message,
        /** The request field at fault: to, cc, headers.List-Unsubscribe, tags... */
        public readonly ?string $field,
        /** The value's place inside that field. */
        public readonly ?int $fieldIndex,
        /** On header_not_allowed: the refused header's name. */
        public readonly ?string $header,
        /** On content_blocked: the blocked host the message links to. */
        public readonly ?string $host,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::int($data, 'index'),
            Data::string($data, 'error'),
            Data::string($data, 'message'),
            Data::nullableString($data, 'field'),
            Data::nullableInt($data, 'field_index'),
            Data::nullableString($data, 'header'),
            Data::nullableString($data, 'host'),
        );
        $model->raw = $data;

        return $model;
    }
}
