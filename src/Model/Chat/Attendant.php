<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Chat;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * The contract's `Attendant` object.
 *
 * Contract type: `Attendant`.
 */
final class Attendant extends Model
{
    /**
     * @param list<AttendantProblem> $problems
     */
    public function __construct(
        /**
         * first: nothing saved yet (draft is Tawked's starter menu); draft: saved, never published;
         * off: published, switched off; live: published and on; no_number, no_chat, blocked: the
         * number cannot run it.
         */
        public readonly string $state,
        /** Why the number cannot send, on blocked. */
        public readonly ?string $lock,
        public readonly bool $enabled,
        public readonly string $mode,
        /** The language of the bot's own words. */
        public readonly string $language,
        public readonly AttendantMenu $draft,
        public readonly ?DateTimeImmutable $draftSavedAt,
        public readonly array $problems,
        public readonly ?AttendantPublished $published,
        /** Whether the draft differs from the published menu. */
        public readonly bool $changed,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'state'),
            Data::nullableString($data, 'lock'),
            Data::bool($data, 'enabled'),
            Data::string($data, 'mode'),
            Data::string($data, 'language'),
            AttendantMenu::fromArray(Data::object($data, 'draft') ?? []),
            Data::date($data, 'draft_saved_at'),
            array_map(static fn(array $item): AttendantProblem => AttendantProblem::fromArray($item), Data::objects($data, 'problems')),
            is_array($data['published'] ?? null) ? AttendantPublished::fromArray(Data::stringKeys($data['published'])) : null,
            Data::bool($data, 'changed'),
        );
        $model->raw = $data;

        return $model;
    }
}
