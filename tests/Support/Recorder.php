<?php

declare(strict_types=1);

namespace Tawked\Tests\Support;

/**
 * What the contract tests saw each method do, for scripts/surface.php (the
 * behaviour half): whether a key was sent, how many requests followed a 500, a timeout after
 * sending and a failure before sending, and whether iterate() followed pages. Written when the
 * test process ends, to TAWKED_SURFACE_RECORD (.phpunit.cache/surface-observed.json by default).
 */
final class Recorder
{
    /** @var array<string, array<string, mixed>> */
    private static array $seen = [];

    private static bool $registered = false;

    public static function observe(string $surface, string $call, string $what, mixed $value): void
    {
        self::$seen[$surface.' '.$call][$what] = $value;
        if (! self::$registered) {
            self::$registered = true;
            register_shutdown_function([self::class, 'write']);
        }
    }

    public static function path(): string
    {
        $given = getenv('TAWKED_SURFACE_RECORD');

        return is_string($given) && $given !== '' ? $given : dirname(__DIR__, 2).'/.phpunit.cache/surface-observed.json';
    }

    public static function write(): void
    {
        $path = self::path();
        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }
        $existing = is_file($path) ? json_decode((string) file_get_contents($path), true) : [];
        $all = is_array($existing) ? $existing : [];
        foreach (self::$seen as $key => $values) {
            $all[$key] = [...(is_array($all[$key] ?? null) ? $all[$key] : []), ...$values];
        }
        ksort($all);
        file_put_contents($path, json_encode($all, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n");
    }
}
