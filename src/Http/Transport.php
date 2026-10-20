<?php

declare(strict_types=1);

namespace Tawked\Http;

use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Utils;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Tawked\Attachment;
use Tawked\Contract;
use Tawked\Exception\ApprovalRequiredException;
use Tawked\Exception\ConnectionException;
use Tawked\Exception\IdempotencyException;
use Tawked\Exception\InvalidArgumentException;
use Tawked\Exception\RateLimitException;
use Tawked\Exception\TimeoutException;
use Tawked\IdempotencyKey;
use Tawked\Internal\Errors;
use Tawked\Version;

/**
 * Sends one call to the API: the credential, the body, the per-attempt
 * timeout, the retries its class allows, the Idempotency-Key it mints, and the typed error.
 *
 * The four classes, from the contract (`sdk.retry`):
 *
 * - `safe` (every GET): retried after a failure before sending, a timeout, a reset or a 5xx.
 * - `idempotent`: the server keeps the key through a failure, so it is retried like a read under
 *   the same key (minted when none is given).
 * - `write_once`: a key is minted; retried only when the request never left (DNS, connect, TLS),
 *   or while the first request with the key is still running (409 idempotency_in_progress, up to
 *   30 s). After a timeout, a reset or a 5xx the exception carries the key, first in its message.
 * - `never`: retried only when the request never left; never after it may have reached Tawked.
 *
 * A 429 with `Retry-After` of 60 s or less is waited on by every class but `never`, whatever its
 * code; a 429 without one (a business limit) and any other 4xx are never retried. Backoff is 0.5 s × 2ⁿ (8 s at most, up to 25 % off).
 *
 * @internal
 */
final class Transport
{
    public const SAFE = 'safe';

    public const IDEMPOTENT = 'idempotent';

    public const WRITE_ONCE = 'write_once';

    public const NEVER = 'never';

    public const SURFACE_PRODUCT = 'product';

    public const SURFACE_ACCOUNT = 'account';

    public const SURFACE_OAUTH = 'oauth';

    /** cURL's "could not resolve host", "could not resolve proxy", "could not connect", "TLS handshake": the request never left. */
    private const NOT_SENT_ERRNOS = [5, 6, 7, 35];

    private const TIMEOUT_ERRNO = 28;

    private const MAX_RETRY_DELAY = 8.0;

    private const MAX_RETRY_AFTER = 60;

    /** The waits on `idempotency_in_progress`, in seconds; then every 5 s up to the window. */
    private const IN_PROGRESS_WAITS = [1.0, 2.0, 4.0];

    /** @var callable(float): void */
    private $sleep;

    /**
     * The credential, behind a closure so that var_export() and an (array) cast never show it and
     * serialize() refuses (__debugInfo covers only var_dump and print_r).
     *
     * @var \Closure(): string
     */
    private readonly \Closure $credential;

    /** The boundary the next multipart body takes: set only while a held call is replayed. */
    private ?string $replayBoundary = null;

    /**
     * @param array<string, string> $headers sent on every request
     * @param (callable(float): void)|null $sleep waits between retries (seconds)
     * @param float|null $timeout per attempt; null takes each call's own (30 s, 120 s for batches and secrets)
     */
    public function __construct(
        #[\SensitiveParameter]
        string $credential,
        private readonly string $baseUrl,
        private readonly ClientInterface $http,
        private readonly ?float $timeout,
        private readonly float $connectTimeout,
        private readonly int $maxRetries,
        private readonly array $headers = [],
        ?callable $sleep = null,
        private readonly string $surface = self::SURFACE_PRODUCT,
        private readonly float $inProgressWait = 30.0,
    ) {
        $this->credential = static fn(): string => $credential;
        $this->sleep = $sleep ?? static function (float $seconds): void {
            if ($seconds > 0) {
                usleep((int) round($seconds * 1_000_000));
            }
        };
    }

    /**
     * Sends one call and answers its decoded JSON object.
     *
     * @param array<string, mixed> $options the call's options: idempotency_key, timeout, max_retries, headers
     * @param self::SAFE|self::IDEMPOTENT|self::WRITE_ONCE|self::NEVER $retry
     * @param array<string, mixed> $query
     * @param array<array-key, mixed>|\stdClass|null $json a JSON body
     * @param array<string, mixed>|null $multipart a multipart body; the fields named in $files are files
     * @param list<string> $files
     * @param array<string, string>|null $form a form body (the OAuth exchange)
     * @param int $keyMax the longest Idempotency-Key the endpoint takes
     * @param bool $takesKey whether the endpoint reads an Idempotency-Key at all
     * @param bool $secret its answer holds a secret shown once
     * @param array<string, string> $headers headers of this call (Tawked-Approval, Basic credentials)
     * @param bool $bearer whether to send the credential as `Authorization: Bearer`
     * @param bool $binary the answer is bytes (a download), kept as they came and never decoded
     */
    public function send(
        string $method,
        string $path,
        array $options = [],
        string $retry = self::NEVER,
        float $timeout = 30.0,
        array $query = [],
        array|\stdClass|null $json = null,
        ?array $multipart = null,
        array $files = [],
        #[\SensitiveParameter]
        ?array $form = null,
        int $keyMax = 128,
        bool $takesKey = true,
        bool $secret = false,
        #[\SensitiveParameter]
        array $headers = [],
        bool $bearer = true,
        bool $binary = false,
    ): ApiResponse {
        $args = get_defined_vars();
        if (\PHP_VERSION_ID >= 80200) {
            return $this->call(...$args);
        }
        // PHP 8.1 has no #[\SensitiveParameter], so a trace made during the call would keep the
        // request and its Authorization header among its arguments: there, it keeps none.
        $before = ini_set('zend.exception_ignore_args', '1');
        try {
            return $this->call(...$args);
        } finally {
            if ($before !== false) {
                ini_set('zend.exception_ignore_args', $before);
            }
        }
    }

    /**
     * The call itself, behind send().
     *
     * @param array<string, mixed> $options the call's options: idempotency_key, timeout, max_retries, headers
     * @param self::SAFE|self::IDEMPOTENT|self::WRITE_ONCE|self::NEVER $retry
     * @param array<string, mixed> $query
     * @param array<array-key, mixed>|\stdClass|null $json a JSON body
     * @param array<string, mixed>|null $multipart a multipart body; the fields named in $files are files
     * @param list<string> $files
     * @param array<string, string>|null $form a form body (the OAuth exchange)
     * @param int $keyMax the longest Idempotency-Key the endpoint takes
     * @param bool $takesKey whether the endpoint reads an Idempotency-Key at all
     * @param bool $secret its answer holds a secret shown once
     * @param array<string, string> $headers headers of this call (Tawked-Approval, Basic credentials)
     * @param bool $bearer whether to send the credential as `Authorization: Bearer`
     * @param bool $binary the answer is bytes (a download), kept as they came and never decoded
     */
    private function call(
        string $method,
        string $path,
        array $options = [],
        string $retry = self::NEVER,
        float $timeout = 30.0,
        array $query = [],
        array|\stdClass|null $json = null,
        ?array $multipart = null,
        array $files = [],
        #[\SensitiveParameter]
        ?array $form = null,
        int $keyMax = 128,
        bool $takesKey = true,
        bool $secret = false,
        #[\SensitiveParameter]
        array $headers = [],
        bool $bearer = true,
        bool $binary = false,
    ): ApiResponse {
        $call = get_defined_vars();
        $opts = RequestOptions::from($options, $keyMax);
        $key = $opts->idempotencyKey;
        if (! $takesKey) {
            $key = null;
        } elseif ($key === null && in_array($retry, [self::IDEMPOTENT, self::WRITE_ONCE], true)) {
            $key = IdempotencyKey::generate();
        }
        $call['options'] = ['idempotency_key' => $key] + $options;
        $maxRetries = $opts->maxRetries ?? $this->maxRetries;
        $perAttempt = $opts->timeout ?? $this->timeout ?? $timeout;

        [$body, $contentType] = $this->body($json, $multipart, $files, $form);

        $attempt = 0;
        $waited = 0.0;
        while (true) {
            $request = $this->build($method, $path, $query, $body, $contentType, $key, [...$headers, ...$opts->headers], $bearer);

            try {
                $response = $this->dispatch($request, $perAttempt);
            } catch (ClientExceptionInterface $e) {
                $phase = self::phase($e);
                $again = $phase === 'pre' || in_array($retry, [self::SAFE, self::IDEMPOTENT], true);
                if ($again && $attempt < $maxRetries) {
                    ($this->sleep)($this->backoff($attempt));
                    $attempt++;

                    continue;
                }

                throw $this->connectionError($e, $phase, $key, $secret);
            }

            $status = $response->getStatusCode();
            if ($status >= 200 && $status < 300) {
                return $binary ? new ApiResponse(self::meta($response, $key), [], (string) $response->getBody()) : $this->success($response, $key);
            }

            $error = Errors::fromResponse($response, $this->surface);
            if ($key !== null) {
                $error->attachIdempotencyKey($key, $status >= 500, $secret, $status >= 500 && $retry !== self::IDEMPOTENT);
            }

            if ($error->errorCode === 'idempotency_in_progress' && $key !== null && in_array($retry, [self::IDEMPOTENT, self::WRITE_ONCE], true)) {
                $delay = $this->inProgressDelay($waited);
                if ($delay === null) {
                    throw IdempotencyException::stillInProgress($error, $key);
                }
                ($this->sleep)($delay);
                $waited += $delay;

                continue;
            }

            if ($status === 429) {
                $after = $error->retryAfter();
                if ($after !== null && $after > self::MAX_RETRY_AFTER && $error instanceof RateLimitException) {
                    throw $error;
                }
                // The server's Retry-After says a wait is worth it (the per-key minute, Email's per-second
                // rate_limited, the Account API's throttle); a business limit (daily_cap_reached,
                // spend_cap_reached, …) sends none, so it fails at once.
                if ($retry !== self::NEVER && $after !== null && $attempt < $maxRetries) {
                    ($this->sleep)((float) $after);
                    $attempt++;

                    continue;
                }

                throw $error;
            }

            if ($status >= 500 && in_array($retry, [self::SAFE, self::IDEMPOTENT], true) && $attempt < $maxRetries) {
                ($this->sleep)($this->backoff($attempt));
                $attempt++;

                continue;
            }

            if ($error instanceof ApprovalRequiredException) {
                $boundary = $multipart !== null && is_string($contentType) ? substr($contentType, strlen('multipart/form-data; boundary=')) : null;
                $error->pending = new PendingRequest($call, $key, $boundary);
            }

            throw $error;
        }
    }

    /**
     * Sends a call the server held for its person's approval again: the same request, the same
     * Idempotency-Key, with `Tawked-Approval`.
     */
    public function replay(PendingRequest $pending, string $approvalId): ApiResponse
    {
        $call = $pending->call;
        $call['headers'] = [...$call['headers'], 'Tawked-Approval' => $approvalId];
        $call['options'] = ['idempotency_key' => $pending->idempotencyKey] + $call['options'];
        // A multipart body goes again under its first boundary: exactly the same bytes as the held call.
        $this->replayBoundary = $pending->boundary;
        try {
            return $this->send(...$call);
        } finally {
            $this->replayBoundary = null;
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        return [
            'credential' => '[hidden]',
            'baseUrl' => $this->baseUrl,
            'surface' => $this->surface,
            'timeout' => $this->timeout,
            'connectTimeout' => $this->connectTimeout,
            'maxRetries' => $this->maxRetries,
        ];
    }

    public function __serialize(): array
    {
        throw new \LogicException('A Tawked client holds a secret and cannot be serialized.');
    }

    public function baseUrl(): string
    {
        return $this->baseUrl;
    }

    public function surface(): string
    {
        return $this->surface;
    }

    private function inProgressDelay(float $waited): ?float
    {
        $remaining = $this->inProgressWait - $waited;
        if ($remaining <= 0.0001) {
            return null;
        }
        $total = 0.0;
        $next = 5.0;
        foreach (self::IN_PROGRESS_WAITS as $wait) {
            if ($waited < $total + $wait - 0.0001) {
                $next = $wait;
                break;
            }
            $total += $wait;
        }

        return min($next, $remaining);
    }

    private function backoff(int $attempt): float
    {
        $delay = min(self::MAX_RETRY_DELAY, 0.5 * (2 ** $attempt));

        return $delay * (1 - (mt_rand(0, 250) / 1000));
    }

    /**
     * Where a transport failure happened: `pre` (the request never left), `timeout` (no answer in
     * time once sent) or `after` (a reset or an unknown failure once sent).
     */
    private static function phase(ClientExceptionInterface $e): string
    {
        if ($e instanceof NotSentException) {
            return 'pre';
        }
        if (! $e instanceof ConnectException) {
            return 'after';
        }
        // cURL's errno, from the message both Guzzle 7 and Guzzle 8 write ("cURL error 7: ...").
        if (preg_match('/^cURL error (\d+):/', $e->getMessage(), $match) !== 1) {
            return 'after';
        }
        $errno = (int) $match[1];
        if (in_array($errno, self::NOT_SENT_ERRNOS, true)) {
            return 'pre';
        }
        if ($errno === self::TIMEOUT_ERRNO) {
            // "Connection timed out after …" / "Resolving timed out after …": still connecting.
            return preg_match('/(Connection|Resolving) timed out/i', $e->getMessage()) === 1 ? 'pre' : 'timeout';
        }

        return 'after';
    }

    private function connectionError(#[\SensitiveParameter] ClientExceptionInterface $e, string $phase, ?string $key, bool $secret): ConnectionException
    {
        // The HTTP client's exception holds the request, and with it the Authorization header:
        // only its class and message are kept, so a dump of the error never shows the credential.
        $cause = new \RuntimeException($e::class.': '.self::scrub($e->getMessage()), (int) $e->getCode());
        $what = $phase === 'timeout' ? 'No answer from the Tawked API in time' : 'Could not reach the Tawked API';
        $text = $what.': '.self::scrub($e->getMessage());
        if ($phase !== 'pre') {
            if ($secret) {
                $text = 'The request may have been applied, and its answer holds a secret shown once: find what it made by its id, then rotate or revoke it. '.$text;
            }
            if ($key !== null) {
                $text = "Idempotency-Key {$key}: the request may have been applied; to retry safely call again with `idempotency_key: '{$key}'` (not a new key). ".$text;
            } elseif (! $secret) {
                $text = 'The request may have reached Tawked; it is not sent again automatically. '.$text;
            }
        }
        $class = $phase === 'timeout' || ($phase === 'pre' && str_contains($e->getMessage(), 'timed out')) ? TimeoutException::class : ConnectionException::class;

        return new $class($text, $key, $phase !== 'pre', $cause);
    }

    private static function scrub(string $message): string
    {
        return (string) preg_replace('/(Bearer|Basic)\s+[A-Za-z0-9._~+\/=-]+/i', '$1 [hidden]', $message);
    }

    private function dispatch(RequestInterface $request, float $timeout): ResponseInterface
    {
        if ($this->http instanceof GuzzleClientInterface) {
            return $this->http->send($request, [
                'timeout' => $timeout,
                'connect_timeout' => min($this->connectTimeout, $timeout),
                'http_errors' => false,
                'allow_redirects' => false,
            ]);
        }

        return $this->http->sendRequest($request);
    }

    /**
     * @param array<array-key, mixed>|\stdClass|null $json
     * @param array<string, mixed>|null $multipart
     * @param list<string> $files
     * @param array<string, string>|null $form
     * @return array{0: string|array<int, array<string, mixed>>|null, 1: string|null}
     */
    private function body(array|\stdClass|null $json, ?array $multipart, array $files, ?array $form): array
    {
        if ($multipart !== null) {
            // One boundary for every attempt, and for the replay of a held call: a retry sends the same bytes.
            $boundary = $this->replayBoundary ?? 'tawked-'.bin2hex(random_bytes(12));
            $this->replayBoundary = null;

            return [self::multipartParts($multipart, $files), 'multipart/form-data; boundary='.$boundary];
        }
        if ($form !== null) {
            return [http_build_query($form, '', '&', PHP_QUERY_RFC1738), 'application/x-www-form-urlencoded'];
        }
        if ($json !== null) {
            return [self::encode($json), 'application/json'];
        }

        return [null, null];
    }

    /**
     * @param array<string, mixed> $query
     * @param string|array<int, array<string, mixed>>|null $body
     * @param array<string, string> $headers
     */
    private function build(string $method, string $path, array $query, string|array|null $body, ?string $contentType, ?string $key, array $headers, bool $bearer): RequestInterface
    {
        $url = $this->baseUrl.$path;
        $encoded = self::query($query);
        if ($encoded !== '') {
            $url .= '?'.$encoded;
        }

        $all = [
            'Accept' => 'application/json',
            'User-Agent' => 'tawked-php/'.Version::VERSION.' (php/'.PHP_VERSION.'; api '.Contract::API_VERSION.')',
        ];
        if ($bearer) {
            $all['Authorization'] = 'Bearer '.($this->credential)();
        }
        foreach ([...$this->headers, ...$headers] as $name => $value) {
            $lower = strtolower($name);
            if ($lower === 'authorization' && $bearer) {
                continue;
            }
            if ($lower === 'idempotency-key') {
                continue;
            }
            $all[$name] = $value;
        }
        if ($key !== null) {
            $all['Idempotency-Key'] = $key;
        }

        $stream = null;
        if (is_array($body)) {
            $multipart = new MultipartStream($body, substr((string) $contentType, strlen('multipart/form-data; boundary=')));
            $all['Content-Type'] = 'multipart/form-data; boundary='.$multipart->getBoundary();
            $stream = $multipart;
        } elseif ($body !== null) {
            $all['Content-Type'] = (string) $contentType;
            $stream = Utils::streamFor($body);
        }

        return new Request($method, $url, $all, $stream);
    }

    private function success(ResponseInterface $response, ?string $key): ApiResponse
    {
        $raw = (string) $response->getBody();
        $meta = self::meta($response, $key);
        if (trim($raw) === '') {
            return new ApiResponse($meta, []);
        }

        try {
            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw Errors::unexpected($response);
        }
        if (! is_array($decoded) || ($decoded !== [] && array_is_list($decoded))) {
            throw Errors::unexpected($response);
        }

        $body = [];
        foreach ($decoded as $name => $value) {
            $body[(string) $name] = $value;
        }

        return new ApiResponse($meta, $body);
    }

    /**
     * @internal
     */
    public static function meta(ResponseInterface $response, ?string $key = null): ResponseMeta
    {
        $headers = [];
        foreach ($response->getHeaders() as $name => $values) {
            $headers[strtolower((string) $name)] = array_values($values);
        }
        $requestId = $response->getHeaderLine('X-Request-Id');

        return new ResponseMeta(
            $response->getStatusCode(),
            $requestId === '' ? null : $requestId,
            strtolower($response->getHeaderLine('Idempotent-Replayed')) === 'true',
            $headers,
            $key,
        );
    }

    /**
     * @param array<array-key, mixed>|\stdClass $json
     */
    private static function encode(array|\stdClass $json): string
    {
        if ($json === []) {
            return '{}';
        }

        try {
            return json_encode(self::jsonValue($json), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION | JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new InvalidArgumentException('The request body cannot be encoded as JSON: '.$e->getMessage(), 0, $e);
        }
    }

    /** Dates as RFC 3339 with their offset; attachments as the wire's object; everything else as given. */
    private static function jsonValue(mixed $value): mixed
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format(\DateTimeInterface::RFC3339_EXTENDED);
        }
        if ($value instanceof Attachment) {
            return $value->toArray();
        }
        if (is_array($value)) {
            $out = [];
            foreach ($value as $k => $v) {
                $out[$k] = self::jsonValue($v);
            }

            return $out;
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $fields
     * @param list<string> $files
     * @return array<int, array<string, mixed>>
     */
    private static function multipartParts(array $fields, array $files): array
    {
        $parts = [];
        foreach ($fields as $name => $value) {
            if ($value === null) {
                continue;
            }
            if (in_array($name, $files, true) || $value instanceof Attachment || $value instanceof \SplFileInfo || $value instanceof StreamInterface || is_resource($value)) {
                $file = Attachment::from($value, $name);
                $parts[] = ['name' => $name, 'contents' => $file->bytes(), 'filename' => $file->filename, 'headers' => ['Content-Type' => $file->contentType]];

                continue;
            }
            foreach (self::flatten($name, $value) as [$field, $text]) {
                $parts[] = ['name' => $field, 'contents' => $text];
            }
        }

        return $parts;
    }

    /**
     * @return list<array{0: string, 1: string}>
     */
    private static function flatten(string $name, mixed $value): array
    {
        if (is_array($value)) {
            $out = [];
            foreach ($value as $k => $v) {
                foreach (self::flatten($name.'['.$k.']', $v) as $pair) {
                    $out[] = $pair;
                }
            }

            return $out;
        }
        if (is_bool($value)) {
            return [[$name, $value ? '1' : '0']];
        }
        if ($value instanceof \DateTimeInterface) {
            return [[$name, $value->format(\DateTimeInterface::RFC3339_EXTENDED)]];
        }
        if (is_scalar($value)) {
            return [[$name, (string) $value]];
        }

        throw new InvalidArgumentException("`{$name}` cannot be sent in a multipart body.");
    }

    /**
     * Drops nulls, writes booleans as true/false, dates as RFC 3339 and objects as key[sub]=value.
     *
     * @param array<string, mixed> $query
     */
    private static function query(array $query): string
    {
        $clean = self::cleanQuery($query);

        return $clean === [] ? '' : http_build_query($clean, '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * @param array<array-key, mixed> $values
     * @return array<array-key, mixed>
     */
    private static function cleanQuery(array $values): array
    {
        $out = [];
        foreach ($values as $name => $value) {
            if ($value === null) {
                continue;
            }
            if (is_bool($value)) {
                $out[$name] = $value ? 'true' : 'false';
            } elseif ($value instanceof \DateTimeInterface) {
                $out[$name] = $value->format(\DateTimeInterface::RFC3339_EXTENDED);
            } elseif (is_array($value)) {
                $out[$name] = self::cleanQuery($value);
            } else {
                $out[$name] = $value;
            }
        }

        return $out;
    }
}
