<?php

declare(strict_types=1);

namespace Tawked\Internal;

use DateTimeImmutable;
use Exception;

/**
 * Reads one field of a decoded response, typed. Lenient on purpose: a field the API
 * adds later is ignored, and a field it stops sending reads as its empty value.
 *
 * @internal
 */
final class Data
{
    /**
     * @param array<array-key, mixed> $data
     */
    public static function string(array $data, string $key): string
    {
        $value = $data[$key] ?? null;

        return is_scalar($value) ? (string) $value : '';
    }

    /**
     * @param array<array-key, mixed> $data
     */
    public static function nullableString(array $data, string $key): ?string
    {
        $value = $data[$key] ?? null;

        return is_scalar($value) ? (string) $value : null;
    }

    /**
     * @param array<array-key, mixed> $data
     */
    public static function int(array $data, string $key): int
    {
        return self::nullableInt($data, $key) ?? 0;
    }

    /**
     * @param array<array-key, mixed> $data
     */
    public static function nullableInt(array $data, string $key): ?int
    {
        $value = $data[$key] ?? null;
        if (is_int($value)) {
            return $value;
        }
        if (is_float($value) || (is_string($value) && is_numeric($value))) {
            return (int) $value;
        }

        return null;
    }

    /**
     * @param array<array-key, mixed> $data
     */
    public static function float(array $data, string $key): float
    {
        return self::nullableFloat($data, $key) ?? 0.0;
    }

    /**
     * @param array<array-key, mixed> $data
     */
    public static function nullableFloat(array $data, string $key): ?float
    {
        $value = $data[$key] ?? null;
        if (is_int($value) || is_float($value) || (is_string($value) && is_numeric($value))) {
            return (float) $value;
        }

        return null;
    }

    /**
     * @param array<array-key, mixed> $data
     * @return list<int>
     */
    public static function ints(array $data, string $key): array
    {
        $value = $data[$key] ?? null;
        if (! is_array($value)) {
            return [];
        }
        $out = [];
        foreach ($value as $item) {
            if (is_int($item) || (is_numeric($item))) {
                $out[] = (int) $item;
            }
        }

        return $out;
    }

    /**
     * A list as sent, items of any kind.
     *
     * @param array<array-key, mixed> $data
     * @return list<mixed>
     */
    public static function list(array $data, string $key): array
    {
        $value = $data[$key] ?? null;

        return is_array($value) ? array_values($value) : [];
    }

    /**
     * @param array<array-key, mixed> $data
     */
    public static function bool(array $data, string $key): bool
    {
        return self::nullableBool($data, $key) ?? false;
    }

    /**
     * @param array<array-key, mixed> $data
     */
    public static function nullableBool(array $data, string $key): ?bool
    {
        $value = $data[$key] ?? null;

        return is_bool($value) ? $value : null;
    }

    /**
     * @param array<array-key, mixed> $data
     */
    public static function date(array $data, string $key): ?DateTimeImmutable
    {
        $value = $data[$key] ?? null;
        if (! is_string($value) || $value === '') {
            return null;
        }

        try {
            return new DateTimeImmutable($value);
        } catch (Exception) {
            return null;
        }
    }

    /**
     * @param array<array-key, mixed> $data
     * @return list<string>
     */
    public static function strings(array $data, string $key): array
    {
        $value = $data[$key] ?? null;
        if (! is_array($value)) {
            return [];
        }

        $out = [];
        foreach ($value as $item) {
            if (is_scalar($item)) {
                $out[] = (string) $item;
            }
        }

        return $out;
    }

    /**
     * An object of the response, as an array.
     *
     * @param array<array-key, mixed> $data
     * @return array<string, mixed>|null
     */
    public static function object(array $data, string $key): ?array
    {
        $value = $data[$key] ?? null;

        return is_array($value) ? self::stringKeys($value) : null;
    }

    /**
     * A flat object of strings, such as metadata.
     *
     * @param array<array-key, mixed> $data
     * @return array<string, string>|null
     */
    public static function stringMap(array $data, string $key): ?array
    {
        $value = $data[$key] ?? null;
        if (! is_array($value)) {
            return null;
        }

        $out = [];
        foreach ($value as $k => $item) {
            if (is_scalar($item)) {
                $out[(string) $k] = (string) $item;
            }
        }

        return $out;
    }

    /**
     * A list of objects, each as an array.
     *
     * @param array<array-key, mixed> $data
     * @return list<array<string, mixed>>
     */
    public static function objects(array $data, string $key): array
    {
        $value = $data[$key] ?? null;
        if (! is_array($value)) {
            return [];
        }

        $out = [];
        foreach ($value as $item) {
            if (is_array($item)) {
                $out[] = self::stringKeys($item);
            }
        }

        return $out;
    }

    /**
     * @param array<array-key, mixed> $value
     * @return array<string, mixed>
     */
    public static function stringKeys(array $value): array
    {
        $out = [];
        foreach ($value as $k => $item) {
            $out[(string) $k] = $item;
        }

        return $out;
    }
}
