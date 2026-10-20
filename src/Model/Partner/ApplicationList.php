<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Partner;

use Tawked\Internal\Data;
use Tawked\Model\Model;
use Tawked\Pagination\OffsetPage;

/**
 * An object of the Tawked API.
 *
 * @implements OffsetPage<Application>
 */
final class ApplicationList extends Model implements OffsetPage
{
    /**
     * @param list<Application> $applications
     */
    public function __construct(
        public readonly int $total,
        public readonly array $applications,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::int($data, 'total'),
            array_map(static fn(array $item): Application => Application::fromArray($item), Data::objects($data, 'applications')),
        );
        $model->raw = $data;

        return $model;
    }

    /**
     * @return list<Application>
     */
    public function items(): array
    {
        return $this->applications;
    }

    public function totalItems(): int
    {
        return $this->total;
    }

    /** The class of this page's items. */
    public const ITEM_CLASS = Application::class;
}
