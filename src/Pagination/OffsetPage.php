<?php

declare(strict_types=1);

namespace Tawked\Pagination;

/**
 * A page of an offset list (`offset`, `limit`, `total`).
 *
 * @template T
 *
 * @extends Page<T>
 */
interface OffsetPage extends Page
{
    /** Every item of the list, all pages together. */
    public function totalItems(): int;
}
