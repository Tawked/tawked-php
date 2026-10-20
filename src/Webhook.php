<?php

declare(strict_types=1);

namespace Tawked;

use JsonException;
use Psr\Http\Message\RequestInterface;
use Tawked\Exception\InvalidArgumentException;
use Tawked\Exception\WebhookSignatureException;
use Tawked\Internal\Data;
use Tawked\Model\Webhook\UnknownEvent;
use Tawked\Model\Webhook\WebhookEvent;
use Tawked\Webhook\Events;

/**
 * Verifies the deliveries of Tawked's webhooks, in both of its signing schemes, with the same arguments in every Tawked SDK:
 *
 * - `tawked`: `tawked-signature` is the lowercase hex HMAC-SHA256 of `"{tawked-timestamp}.{raw body}"`,
 *   keyed by the secret's UTF-8 bytes exactly as the console shows it; the timestamp is in milliseconds.
 * - `standard_webhooks` (Tawked Email webhook endpoints with Standard signing on):
 *   `webhook-signature` holds `v1,<base64>` entries of the HMAC-SHA256 of
 *   `"{webhook-id}.{webhook-timestamp}.{raw body}"`, keyed by the base64-decoded bytes of a `whsec_`
 *   secret; the timestamp is in seconds.
 *
 * The secret decides the scheme: a `whsec_` secret is only ever used for Standard Webhooks, any other
 * only for `tawked-signature`. Pass every secret you hold (a list while you rotate one).
 *
 *     $event = Tawked\Webhook::constructEvent($rawBody, getallheaders(), $secret);
 *
 * Always verify the raw body as received, never JSON you re-encoded.
 */
final class Webhook
{
    public const SIGNATURE_HEADER = 'tawked-signature';

    public const TIMESTAMP_HEADER = 'tawked-timestamp';

    public const EVENT_HEADER = 'tawked-event';

    public const ID_HEADER = 'tawked-webhook-id';

    public const STANDARD_ID_HEADER = 'webhook-id';

    public const STANDARD_TIMESTAMP_HEADER = 'webhook-timestamp';

    public const STANDARD_SIGNATURE_HEADER = 'webhook-signature';

    public const STANDARD_SECRET_PREFIX = 'whsec_';

    public const SCHEME_AUTO = 'auto';

    public const SCHEME_TAWKED = 'tawked';

    public const SCHEME_STANDARD = 'standard_webhooks';

    /** Seconds a delivery's timestamp may be away from now. */
    public const DEFAULT_TOLERANCE = 300;

    /**
     * Whether a delivery verifies. Options: `tolerance` (seconds, 300; null turns the timestamp
     * check off), `scheme` (`auto`, `tawked` or `standard_webhooks`), `now` (milliseconds since the
     * epoch, for tests).
     *
     * @param array<array-key, mixed> $headers the request's headers, any case; `$_SERVER` works too
     * @param string|list<string> $secrets
     * @param array{tolerance?: int|null, scheme?: string, now?: int} $options
     */
    public static function verify(string $payload, array $headers, #[\SensitiveParameter] string|array $secrets, array $options = []): bool
    {
        return self::reason($payload, $headers, $secrets, $options) === null;
    }

    /**
     * Verifies a delivery, throwing WebhookSignatureException with its reason() when it does not hold.
     *
     * @param array<array-key, mixed> $headers
     * @param string|list<string> $secrets
     * @param array{tolerance?: int|null, scheme?: string, now?: int} $options
     */
    public static function assertValid(string $payload, array $headers, #[\SensitiveParameter] string|array $secrets, array $options = []): void
    {
        $reason = self::reason($payload, $headers, $secrets, $options);
        if ($reason !== null) {
            throw new WebhookSignatureException(self::sentence($reason), $reason);
        }
    }

    /**
     * Verifies a delivery and reads its event: a WebhookEvent whose typedData() is typed by its
     * event, or an UnknownEvent (raw data) for an event this version does not know. A body that is
     * not JSON throws `malformed_body`.
     *
     * The fourth argument is the options array of verify(); an int is read as the tolerance and a
     * fifth int as `now`, as in the earlier releases.
     *
     * @param array<array-key, mixed> $headers
     * @param string|list<string> $secrets
     * @param array{tolerance?: int|null, scheme?: string, now?: int}|int|null $options
     */
    public static function constructEvent(string $payload, array $headers, #[\SensitiveParameter] string|array $secrets, array|int|null $options = [], ?int $now = null): WebhookEvent
    {
        $options = self::options($options, $now, func_num_args());
        self::assertValid($payload, $headers, $secrets, $options);
        $headers = self::normalise($headers);
        $timestamp = $headers[self::TIMESTAMP_HEADER] ?? null;
        if ($timestamp === null && isset($headers[self::STANDARD_TIMESTAMP_HEADER])) {
            $timestamp = $headers[self::STANDARD_TIMESTAMP_HEADER].'000';
        }

        return self::parse($payload, $timestamp);
    }

    /**
     * Verifies a PSR-7 request (a framework's request converted, or a ServerRequest) and reads its
     * event. The body stream is rewound before and after.
     *
     * @param string|list<string> $secrets
     * @param array{tolerance?: int|null, scheme?: string, now?: int}|int|null $options
     */
    public static function constructEventFromRequest(RequestInterface $request, #[\SensitiveParameter] string|array $secrets, array|int|null $options = [], ?int $now = null): WebhookEvent
    {
        $options = self::options($options, $now, func_num_args() + 1);
        $body = $request->getBody();
        if ($body->isSeekable()) {
            $body->rewind();
        }
        $payload = $body->getContents();
        if ($body->isSeekable()) {
            $body->rewind();
        }
        $headers = [];
        foreach ($request->getHeaders() as $name => $values) {
            $headers[(string) $name] = $values[0] ?? '';
        }

        return self::constructEvent($payload, $headers, $secrets, $options);
    }

    /**
     * The signature Tawked sends for this body, for a receiver's own tests: the hex HMAC for a
     * tawked secret (timestamp in milliseconds), `v1,<base64>` for a `whsec_` secret (timestamp in
     * seconds, `id` required).
     *
     * @param array{scheme?: string, id?: string} $options
     */
    public static function sign(string $payload, #[\SensitiveParameter] string $secret, int|string $timestamp, array $options = []): string
    {
        $scheme = $options['scheme'] ?? (str_starts_with($secret, self::STANDARD_SECRET_PREFIX) ? self::SCHEME_STANDARD : self::SCHEME_TAWKED);
        if ($scheme === self::SCHEME_STANDARD) {
            $id = $options['id'] ?? null;
            if (! is_string($id) || $id === '') {
                throw new InvalidArgumentException('A Standard Webhooks signature needs the delivery `id`.');
            }

            return 'v1,'.base64_encode(hash_hmac('sha256', $id.'.'.$timestamp.'.'.$payload, self::standardKey($secret), true));
        }
        if ($scheme !== self::SCHEME_TAWKED) {
            throw new InvalidArgumentException('`scheme` must be tawked or standard_webhooks.');
        }
        if (str_starts_with($secret, self::STANDARD_SECRET_PREFIX)) {
            throw new InvalidArgumentException('A whsec_ secret signs only Standard Webhooks.');
        }

        return hash_hmac('sha256', $timestamp.'.'.$payload, $secret);
    }

    /**
     * The earlier argument order, `($secret, $timestamp, $payload)`.
     *
     * @deprecated since 0.1.0, removed in 0.2.0: use sign($payload, $secret, $timestamp)
     */
    public static function signLegacy(#[\SensitiveParameter] string $secret, string $timestamp, string $payload): string
    {
        return self::sign($payload, $secret, $timestamp, ['scheme' => self::SCHEME_TAWKED]);
    }

    /**
     * The headers of a delivery of this body signed now, for a receiver's own tests.
     *
     * @param array{scheme?: string, id?: string, timestamp?: int|string, event?: string} $options
     * @return array<string, string>
     */
    public static function testHeaders(string $payload, #[\SensitiveParameter] string $secret, array $options = []): array
    {
        $standard = ($options['scheme'] ?? null) === self::SCHEME_STANDARD || (! isset($options['scheme']) && str_starts_with($secret, self::STANDARD_SECRET_PREFIX));
        $id = $options['id'] ?? 'msg_'.bin2hex(random_bytes(12));
        if ($standard) {
            $timestamp = (string) ($options['timestamp'] ?? time());

            return [
                self::STANDARD_ID_HEADER => $id,
                self::STANDARD_TIMESTAMP_HEADER => $timestamp,
                self::STANDARD_SIGNATURE_HEADER => self::sign($payload, $secret, $timestamp, ['scheme' => self::SCHEME_STANDARD, 'id' => $id]),
            ];
        }
        $timestamp = (string) ($options['timestamp'] ?? (int) floor(microtime(true) * 1000));
        $headers = [
            self::TIMESTAMP_HEADER => $timestamp,
            self::SIGNATURE_HEADER => self::sign($payload, $secret, $timestamp, ['scheme' => self::SCHEME_TAWKED]),
            self::ID_HEADER => $id,
        ];
        $decoded = json_decode($payload, true);
        $event = $options['event'] ?? (is_array($decoded) && is_string($decoded['event'] ?? null) ? $decoded['event'] : null);
        if ($event !== null) {
            $headers[self::EVENT_HEADER] = $event;
        }

        return $headers;
    }

    /**
     * Why a delivery does not verify, or null when it does (the contract's reading).
     *
     * @param array<array-key, mixed> $headers
     * @param string|list<string> $secrets
     * @param array{tolerance?: int|null, scheme?: string, now?: int} $options
     */
    private static function reason(string $payload, array $headers, #[\SensitiveParameter] string|array $secrets, array $options): ?string
    {
        $headers = self::normalise($headers);
        $secrets = array_values(array_filter(is_array($secrets) ? $secrets : [$secrets], fn(string $s) => $s !== ''));
        $tolerance = array_key_exists('tolerance', $options) ? $options['tolerance'] : self::DEFAULT_TOLERANCE;
        $scheme = $options['scheme'] ?? self::SCHEME_AUTO;
        $now = $options['now'] ?? (int) floor(microtime(true) * 1000);
        if (! in_array($scheme, [self::SCHEME_AUTO, self::SCHEME_TAWKED, self::SCHEME_STANDARD], true)) {
            throw new InvalidArgumentException('`scheme` must be auto, tawked or standard_webhooks.');
        }

        $standardSecrets = array_values(array_filter($secrets, fn(string $s) => str_starts_with($s, self::STANDARD_SECRET_PREFIX)));
        $tawkedSecrets = array_values(array_filter($secrets, fn(string $s) => ! str_starts_with($s, self::STANDARD_SECRET_PREFIX)));
        $hasStandard = isset($headers[self::STANDARD_SIGNATURE_HEADER]);
        $hasTawked = isset($headers[self::SIGNATURE_HEADER]);

        // under auto the secrets choose the scheme, never the headers; a whsec_ secret means Standard only.
        $useStandard = match ($scheme) {
            self::SCHEME_STANDARD => true,
            self::SCHEME_TAWKED => false,
            default => $standardSecrets !== [],
        };
        if ($useStandard) {
            if (! $hasStandard) {
                return $scheme === self::SCHEME_AUTO && $hasTawked && $tawkedSecrets === [] ? WebhookSignatureException::NO_SECRET_FOR_SCHEME : WebhookSignatureException::MISSING_HEADER;
            }
            if ($standardSecrets === []) {
                return WebhookSignatureException::NO_SECRET_FOR_SCHEME;
            }

            return self::checkStandard($payload, $headers, $standardSecrets, $tolerance, $now);
        }
        if ($scheme === self::SCHEME_TAWKED || $tawkedSecrets !== []) {
            if (! $hasTawked) {
                return $scheme === self::SCHEME_AUTO && $hasStandard ? WebhookSignatureException::NO_SECRET_FOR_SCHEME : WebhookSignatureException::MISSING_HEADER;
            }
            if ($tawkedSecrets === []) {
                return WebhookSignatureException::NO_SECRET_FOR_SCHEME;
            }

            return self::checkTawked($payload, $headers, $tawkedSecrets, $tolerance, $now);
        }

        return ($hasStandard || $hasTawked) ? WebhookSignatureException::NO_SECRET_FOR_SCHEME : WebhookSignatureException::MISSING_HEADER;
    }

    /**
     * @param array<string, string> $headers
     * @param list<string> $secrets
     */
    private static function checkStandard(string $payload, array $headers, array $secrets, ?int $tolerance, int $now): ?string
    {
        $id = $headers[self::STANDARD_ID_HEADER] ?? '';
        $timestamp = $headers[self::STANDARD_TIMESTAMP_HEADER] ?? null;
        if ($id === '' || $timestamp === null) {
            return WebhookSignatureException::MISSING_HEADER;
        }
        if (preg_match('/^\d{10}$/', $timestamp) !== 1) {
            return WebhookSignatureException::MALFORMED_HEADER;
        }
        if ($tolerance !== null && abs(intdiv($now, 1000) - (int) $timestamp) > $tolerance) {
            return WebhookSignatureException::TIMESTAMP_OUT_OF_TOLERANCE;
        }
        $entries = preg_split('/\s+/', trim($headers[self::STANDARD_SIGNATURE_HEADER])) ?: [];
        foreach ($secrets as $secret) {
            $key = self::standardKeyOrNull($secret);
            if ($key === null) {
                continue;
            }
            $expected = 'v1,'.base64_encode(hash_hmac('sha256', $id.'.'.$timestamp.'.'.$payload, $key, true));
            foreach ($entries as $entry) {
                if (str_starts_with($entry, 'v1,') && hash_equals($expected, $entry)) {
                    return null;
                }
            }
        }

        return WebhookSignatureException::SIGNATURE_MISMATCH;
    }

    /**
     * @param array<string, string> $headers
     * @param list<string> $secrets
     */
    private static function checkTawked(string $payload, array $headers, array $secrets, ?int $tolerance, int $now): ?string
    {
        $timestamp = $headers[self::TIMESTAMP_HEADER] ?? null;
        if ($timestamp === null || trim($timestamp) === '') {
            return WebhookSignatureException::MISSING_HEADER;
        }
        $timestamp = trim($timestamp);
        if (preg_match('/^\d{13}$/', $timestamp) !== 1) {
            return WebhookSignatureException::MALFORMED_HEADER;
        }
        if ($tolerance !== null && abs($now - (int) $timestamp) > $tolerance * 1000) {
            return WebhookSignatureException::TIMESTAMP_OUT_OF_TOLERANCE;
        }
        $signature = strtolower(trim($headers[self::SIGNATURE_HEADER]));
        $matched = false;
        foreach ($secrets as $secret) {
            if (hash_equals(hash_hmac('sha256', $timestamp.'.'.$payload, $secret), $signature)) {
                $matched = true;
            }
        }

        return $matched ? null : WebhookSignatureException::SIGNATURE_MISMATCH;
    }

    private static function standardKey(#[\SensitiveParameter] string $secret): string
    {
        $key = self::standardKeyOrNull($secret);
        if ($key === null) {
            throw new InvalidArgumentException('A Standard Webhooks secret is whsec_ followed by base64.');
        }

        return $key;
    }

    private static function standardKeyOrNull(#[\SensitiveParameter] string $secret): ?string
    {
        if (! str_starts_with($secret, self::STANDARD_SECRET_PREFIX)) {
            return null;
        }
        $key = base64_decode(substr($secret, strlen(self::STANDARD_SECRET_PREFIX)), true);

        return $key === false || $key === '' ? null : $key;
    }

    private static function sentence(string $reason): string
    {
        return match ($reason) {
            WebhookSignatureException::MISSING_HEADER => 'The webhook signature headers are missing (tawked-timestamp and tawked-signature, or webhook-id, webhook-timestamp and webhook-signature).',
            WebhookSignatureException::NO_SECRET_FOR_SCHEME => 'The delivery is signed in a scheme you gave no secret for: a whsec_ secret verifies webhook-signature, any other tawked-signature.',
            WebhookSignatureException::TIMESTAMP_OUT_OF_TOLERANCE => 'The webhook timestamp is too far from now (a replay, or a clock that is off).',
            WebhookSignatureException::MALFORMED_HEADER => 'A webhook header is malformed: tawked-timestamp is milliseconds (13 digits), webhook-timestamp seconds (10 digits).',
            WebhookSignatureException::MALFORMED_BODY => 'The webhook body is not a Tawked event envelope.',
            default => 'The webhook signature does not match: check the secret, and verify the raw body as received.',
        };
    }

    /**
     * @param array{tolerance?: int|null, scheme?: string, now?: int}|int|null $options
     * @return array{tolerance?: int|null, scheme?: string, now?: int}
     */
    private static function options(array|int|null $options, ?int $now, int $args): array
    {
        if (is_array($options)) {
            return $now === null ? $options : ['now' => $now] + $options;
        }
        // The earlier signature: (…, ?int $tolerance, ?int $now).
        $out = $args >= 4 ? ['tolerance' => $options] : [];
        if ($now !== null) {
            $out['now'] = $now;
        }

        return $out;
    }

    private static function parse(string $payload, ?string $timestamp): WebhookEvent
    {
        try {
            $decoded = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new WebhookSignatureException(self::sentence(WebhookSignatureException::MALFORMED_BODY), WebhookSignatureException::MALFORMED_BODY, $e);
        }
        if (! is_array($decoded)) {
            throw new WebhookSignatureException(self::sentence(WebhookSignatureException::MALFORMED_BODY), WebhookSignatureException::MALFORMED_BODY);
        }
        $body = Data::stringKeys($decoded);
        // Tawked's envelope names the event `event`; Standard Webhooks' envelope `type`.
        $event = $body['event'] ?? $body['type'] ?? null;
        if (! is_string($event) || ! is_array($body['data'] ?? null)) {
            throw new WebhookSignatureException(self::sentence(WebhookSignatureException::MALFORMED_BODY), WebhookSignatureException::MALFORMED_BODY);
        }
        if (! isset($body['event'])) {
            $body['event'] = $event;
        }

        return isset(Events::DATA[$event])
            ? WebhookEvent::fromArray($body, $timestamp)
            : UnknownEvent::fromArray($body, $timestamp);
    }

    /**
     * @param array<array-key, mixed> $headers
     * @return array<string, string>
     */
    private static function normalise(array $headers): array
    {
        $out = [];
        foreach ($headers as $name => $value) {
            $name = strtolower(str_replace('_', '-', (string) $name));
            if (str_starts_with($name, 'http-')) {
                $name = substr($name, 5);
            }
            if (is_array($value)) {
                $value = $value[0] ?? '';
            }
            if (is_scalar($value)) {
                $out[$name] = (string) $value;
            }
        }

        return $out;
    }
}
