<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `EmailTemplateProblem` object.
 *
 * Contract type: `EmailTemplateProblem`.
 */
final class EmailTemplateProblem extends Model
{
    public function __construct(
        public readonly string $code,
        public readonly ?string $key,
        public readonly string $severity,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'code'),
            Data::nullableString($data, 'key'),
            Data::string($data, 'severity'),
        );
        $model->raw = $data;

        return $model;
    }
}
