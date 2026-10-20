<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Partner;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class ScreeningResult extends Model
{
    /**
     * @param list<string> $rules
     */
    public function __construct(
        public readonly string $result,
        public readonly array $rules,
        public readonly ScreeningReview $review,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'result'),
            Data::strings($data, 'rules'),
            ScreeningReview::fromArray(Data::object($data, 'review') ?? []),
        );
        $model->raw = $data;

        return $model;
    }
}
