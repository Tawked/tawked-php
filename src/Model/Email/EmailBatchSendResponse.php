<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class EmailBatchSendResponse extends Model
{
    /**
     * @param list<EmailAccepted> $data
     * @param list<EmailBatchError> $errors
     */
    public function __construct(
        public readonly array $data,
        public readonly array $errors,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            array_map(static fn(array $item): EmailAccepted => EmailAccepted::fromArray($item), Data::objects($data, 'data')),
            array_map(static fn(array $item): EmailBatchError => EmailBatchError::fromArray($item), Data::objects($data, 'errors')),
        );
        $model->raw = $data;

        return $model;
    }
}
