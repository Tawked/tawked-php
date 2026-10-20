<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Chat;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class AttendantChoiceActionAssign extends Model
{
    public function __construct(
        /** auto: Tawked Chat assigns as usual; member: a named member, set in the console. */
        public readonly string $mode,
        public readonly ?string $memberId,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'mode'),
            Data::nullableString($data, 'member_id'),
        );
        $model->raw = $data;

        return $model;
    }
}
