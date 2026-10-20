<?php

declare(strict_types=1);

namespace Tawked\Service;

use Tawked\Exception\InvalidArgumentException;
use Tawked\Http\Transport;

/**
 * @internal the base of every generated service; use them through Tawked\TawkedClient
 */
abstract class Service
{
    public function __construct(protected readonly Transport $transport) {}

    /**
     * One path segment, encoded. An empty id is refused before anything is sent.
     */
    protected static function segment(string $value, string $name): string
    {
        if (trim($value) === '') {
            throw new InvalidArgumentException("`{$name}` must not be empty.");
        }

        return rawurlencode($value);
    }

    /**
     * The params named, for the query of a call whose other params go in the body.
     *
     * @param array<string, mixed> $params
     * @param list<string> $names
     * @return array<string, mixed>
     */
    protected static function only(array $params, array $names): array
    {
        return array_intersect_key($params, array_flip($names));
    }

    /**
     * @param array<string, mixed> $params
     * @param list<string> $names
     * @return array<string, mixed>
     */
    protected static function without(array $params, array $names): array
    {
        return array_diff_key($params, array_flip($names));
    }

    /**
     * A JSON body: none when there are no params; an empty array where the schema says object is
     * sent as `{}`, never `[]`.
     *
     * @param array<string, mixed> $params
     * @param list<string> $objects
     * @return array<string, mixed>|null
     */
    protected static function body(array $params, array $objects = []): ?array
    {
        if ($params === []) {
            return null;
        }
        foreach ($objects as $name) {
            if (array_key_exists($name, $params) && $params[$name] === []) {
                $params[$name] = new \stdClass();
            }
        }

        return $params;
    }

    /**
     * @param array<array-key, mixed> $items
     */
    protected static function assertListSize(array $items, string $name, int $min, int $max): void
    {
        $count = count($items);
        if ($count < $min || $count > $max) {
            throw new InvalidArgumentException("`{$name}` must hold {$min} to {$max} items; it holds {$count}.");
        }
    }
}
