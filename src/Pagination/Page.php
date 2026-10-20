<?php

declare(strict_types=1);

namespace Tawked\Pagination;

/**
 * One page of a list, as the API answered it. `iterate()` on the same service walks every page.
 *
 * @template T
 */
interface Page
{
    /**
     * @return list<T>
     */
    public function items(): array;
}
