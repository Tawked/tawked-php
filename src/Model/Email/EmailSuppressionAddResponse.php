<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class EmailSuppressionAddResponse extends Model
{
    /**
     * @param list<EmailSuppressionAddResponseInvalid> $invalid
     */
    public function __construct(
        public readonly int $added,
        public readonly int $existing,
        public readonly array $invalid,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::int($data, 'added'),
            Data::int($data, 'existing'),
            array_map(static fn(array $item): EmailSuppressionAddResponseInvalid => EmailSuppressionAddResponseInvalid::fromArray($item), Data::objects($data, 'invalid')),
        );
        $model->raw = $data;

        return $model;
    }
}
