<?php

declare(strict_types=1);

namespace Tawked\Http;

use Tawked\Exception\InvalidArgumentException;

/**
 * The options of one call, the last argument of every method:
 *
 * - `idempotency_key` (string): sent as `Idempotency-Key` on the calls that take one (a Verify
 *   start, a WhatsApp send, every Partner API write). 1 to 128 printable ASCII characters.
 * - `timeout` (float, seconds): this call's timeout.
 * - `max_retries` (int): this call's retries.
 * - `headers` (array<string, string>): extra headers.
 *
 * @internal
 */
final class RequestOptions
{
    private const KEYS = ['idempotency_key', 'timeout', 'max_retries', 'headers'];

    /**
     * @param array<string, string> $headers
     */
    private function __construct(
        public readonly ?string $idempotencyKey,
        public readonly ?float $timeout,
        public readonly ?int $maxRetries,
        public readonly array $headers,
    ) {}

    /**
     * @param array<string, mixed> $options
     */
    public static function from(array $options, int $keyMax = 128): self
    {
        $unknown = array_diff(array_keys($options), self::KEYS);
        if ($unknown !== []) {
            throw new InvalidArgumentException('Unknown request option(s): '.implode(', ', $unknown).'. Known: '.implode(', ', self::KEYS).'.');
        }

        $key = $options['idempotency_key'] ?? null;
        if ($key !== null) {
            if (! is_string($key) || preg_match('/^[\x21-\x7E]{1,'.$keyMax.'}$/', trim($key)) !== 1) {
                throw new InvalidArgumentException("`Idempotency-Key` must be 1-{$keyMax} printable ASCII characters");
            }
            $key = trim($key);
        }

        $timeout = $options['timeout'] ?? null;
        if ($timeout !== null && (! is_int($timeout) && ! is_float($timeout) || $timeout <= 0)) {
            throw new InvalidArgumentException('`timeout` must be a positive number of seconds.');
        }

        $retries = $options['max_retries'] ?? null;
        if ($retries !== null && (! is_int($retries) || $retries < 0)) {
            throw new InvalidArgumentException('`max_retries` must be an integer of 0 or more.');
        }

        $headers = [];
        $given = $options['headers'] ?? [];
        if (! is_array($given)) {
            throw new InvalidArgumentException('`headers` must be an array of strings.');
        }
        foreach ($given as $name => $value) {
            if (! is_string($name) || ! is_scalar($value)) {
                throw new InvalidArgumentException('`headers` must be an array of strings.');
            }
            $headers[$name] = (string) $value;
        }

        return new self($key, $timeout === null ? null : (float) $timeout, $retries, $headers);
    }
}
