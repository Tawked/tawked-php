<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Chat;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class ChatAttendantDraftSaveResponse extends Model
{
    /**
     * @param list<AttendantProblem> $problems
     */
    public function __construct(
        public readonly ?DateTimeImmutable $draftSavedAt,
        public readonly array $problems,
        public readonly AttendantMenu $menu,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::date($data, 'draft_saved_at'),
            array_map(static fn(array $item): AttendantProblem => AttendantProblem::fromArray($item), Data::objects($data, 'problems')),
            AttendantMenu::fromArray(Data::object($data, 'menu') ?? []),
        );
        $model->raw = $data;

        return $model;
    }
}
