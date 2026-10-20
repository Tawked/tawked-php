<?php

declare(strict_types=1);

namespace Tawked\Internal;

use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Client\ClientInterface;
use Tawked\Exception\InvalidArgumentException;
use Tawked\Http\Transport;

/**
 * Reads the options both clients take and makes their transport.
 *
 * @internal
 */
final class ClientOptions
{
    public const DEFAULT_BASE_URL = 'https://tawked.com';

    public const OPTIONS = ['base_url', 'timeout', 'connect_timeout', 'max_retries', 'http_client', 'headers', 'sleep', 'in_progress_wait'];

    /**
     * @param array<string, mixed> $options
     * @param list<string> $extra options the client reads itself
     * @param array<string, string> $headers headers of the client itself
     */
    public static function transport(#[\SensitiveParameter] string $credential, array $options, string $surface, array $extra = [], array $headers = []): Transport
    {
        $known = [...self::OPTIONS, ...$extra];
        $unknown = array_diff(array_keys($options), $known);
        if ($unknown !== []) {
            throw new InvalidArgumentException('Unknown option(s): '.implode(', ', $unknown).'. Known: '.implode(', ', $known).'.');
        }

        $baseUrl = $options['base_url'] ?? self::env('TAWKED_BASE_URL') ?? self::DEFAULT_BASE_URL;
        if (! is_string($baseUrl) || preg_match('#^https?://[^/\s]+#i', $baseUrl) !== 1) {
            throw new InvalidArgumentException('`base_url` must be an https:// URL (or http:// for a local mock).');
        }

        $http = $options['http_client'] ?? new GuzzleClient();
        if (! $http instanceof ClientInterface) {
            throw new InvalidArgumentException('`http_client` must be a PSR-18 client (a Guzzle client is one).');
        }

        $sleep = $options['sleep'] ?? null;
        if ($sleep !== null && ! is_callable($sleep)) {
            throw new InvalidArgumentException('`sleep` must be callable.');
        }

        return new Transport(
            $credential,
            rtrim($baseUrl, '/'),
            $http,
            array_key_exists('timeout', $options) ? self::seconds($options, 'timeout', 30.0) : null,
            self::seconds($options, 'connect_timeout', 10.0),
            self::retries($options),
            [...self::headers($options), ...$headers],
            $sleep,
            $surface,
            self::seconds($options, 'in_progress_wait', 30.0, true),
        );
    }

    public static function env(string $name): ?string
    {
        $value = $_ENV[$name] ?? $_SERVER[$name] ?? getenv($name);

        return is_string($value) && $value !== '' ? $value : null;
    }

    /**
     * The credential given or read from the environment, checked: present and with no whitespace.
     */
    public static function credential(#[\SensitiveParameter] ?string $given, string $env, string $what): string
    {
        $value = trim((string) ($given ?? self::env($env)));
        if ($value === '') {
            throw new InvalidArgumentException("A Tawked {$what} is required: pass it, or set {$env}.");
        }
        if (preg_match('/\s/', $value) === 1) {
            throw new InvalidArgumentException("The Tawked {$what} must not contain whitespace.");
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $options
     */
    private static function seconds(array $options, string $name, float $default, bool $zero = false): float
    {
        $value = $options[$name] ?? $default;
        if ((! is_int($value) && ! is_float($value)) || $value < 0 || (! $zero && $value == 0)) {
            throw new InvalidArgumentException("`{$name}` must be a positive number of seconds.");
        }

        return (float) $value;
    }

    /**
     * @param array<string, mixed> $options
     */
    private static function retries(array $options): int
    {
        $value = $options['max_retries'] ?? 2;
        if (! is_int($value) || $value < 0) {
            throw new InvalidArgumentException('`max_retries` must be an integer of 0 or more.');
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $options
     * @return array<string, string>
     */
    private static function headers(array $options): array
    {
        $given = $options['headers'] ?? [];
        if (! is_array($given)) {
            throw new InvalidArgumentException('`headers` must be an array of strings.');
        }
        $headers = [];
        foreach ($given as $name => $value) {
            if (! is_string($name) || ! is_scalar($value)) {
                throw new InvalidArgumentException('`headers` must be an array of strings.');
            }
            $headers[$name] = (string) $value;
        }

        return $headers;
    }
}
