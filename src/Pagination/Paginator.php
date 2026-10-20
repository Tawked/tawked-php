<?php

declare(strict_types=1);

namespace Tawked\Pagination;

use Generator;

/**
 * Walks the pages of a list for `iterate()`: never more requests than needed, stops on an empty
 * page, and stops on a cursor that repeats.
 *
 * @internal
 */
final class Paginator
{
    /**
     * @template T
     *
     * @param callable(array<string, mixed>): CursorPage<T> $fetch
     * @param array<string, mixed> $params
     * @return Generator<int, T>
     */
    public static function cursor(callable $fetch, array $params, string $cursorParam): Generator
    {
        $seen = [];
        while (true) {
            $page = $fetch($params);
            $items = $page->items();
            foreach ($items as $item) {
                yield $item;
            }
            $cursor = $page->cursor();
            if ($items === [] || ! $page->more() || $cursor === null || isset($seen[$cursor])) {
                return;
            }
            $seen[$cursor] = true;
            $params[$cursorParam] = $cursor;
        }
    }

    /**
     * @template T
     *
     * @param callable(array<string, mixed>): OffsetPage<T> $fetch
     * @param array<string, mixed> $params
     * @return Generator<int, T>
     */
    public static function offset(callable $fetch, array $params, string $offsetParam): Generator
    {
        $offset = is_int($params[$offsetParam] ?? null) ? $params[$offsetParam] : 0;
        while (true) {
            $params[$offsetParam] = $offset;
            $page = $fetch($params);
            $items = $page->items();
            foreach ($items as $item) {
                yield $item;
            }
            $offset += count($items);
            if ($items === [] || $offset >= $page->totalItems()) {
                return;
            }
        }
    }
}
