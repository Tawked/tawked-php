<?php

declare(strict_types=1);

namespace Tawked\Pagination;

/**
 * A page of a cursor list: the next page is asked for with `cursor()`, while `more()`.
 *
 * @template T
 *
 * @extends Page<T>
 */
interface CursorPage extends Page
{
    /** The cursor of the next page, or null at the end. */
    public function cursor(): ?string;

    /** Whether there is a next page (`has_more` where the list answers it, else a cursor). */
    public function more(): bool;
}
