<?php

declare(strict_types=1);

namespace Tawked\Exception;

use RuntimeException;
use Tawked\Internal\Data;

/**
 * The API answered with an error. Every error carries the API's `error` code, the HTTP
 * status and the `X-Request-Id` to quote to support. The subclasses group the codes by
 * status; `errorCode` is the exact code (see Tawked\ErrorCode).
 */
class ApiException extends RuntimeException implements TawkedException
{
    private ?string $idempotencyKey = null;

    /**
     * @param array<string, mixed> $body the error object as the API sent it
     * @param array<string, list<string>> $headers lowercase header names
     */
    public function __construct(
        public readonly int $statusCode,
        public readonly string $errorCode,
        public readonly ?string $requestId,
        public readonly array $body = [],
        public readonly array $headers = [],
    ) {
        parent::__construct(self::describe($statusCode, $errorCode, $requestId, $body));
    }

    /** The HTTP status. */
    public function status(): int
    {
        return $this->statusCode;
    }

    /** The API's `error` code (see Tawked\ErrorCode); branch on it. */
    public function code(): string
    {
        return $this->errorCode;
    }

    /**
     * The Idempotency-Key the call was sent under (yours, or the one the SDK minted). After a 5xx or
     * a timeout, call again with this key, never a new one, to get the first request's answer.
     */
    public function idempotencyKey(): ?string
    {
        return $this->idempotencyKey;
    }

    /**
     * Seconds to wait, from the `Retry-After` header (seconds or an HTTP date), when the answer has one.
     */
    public function retryAfter(): ?int
    {
        $value = $this->header('Retry-After');
        if ($value === null || trim($value) === '') {
            return null;
        }
        $value = trim($value);
        if (ctype_digit($value)) {
            return (int) $value;
        }
        $at = strtotime($value);

        return $at === false ? null : max(0, $at - time());
    }

    /** On the Account API: the sentence that names what to call next. */
    public function next(): ?string
    {
        return Data::nullableString($this->body, 'next');
    }

    /**
     * On the Account API: what only a person can do (`reason`, `url`, `say`, `then`). Relay `say` and
     * `url`; once they say it is done, call the operation `then`.
     *
     * @return array<string, mixed>|null
     */
    public function humanAction(): ?array
    {
        return Data::object($this->body, 'human_action');
    }

    /**
     * On the Account API's 428: the approval waiting for its person (`id`, `status`, `operation`, `expires_at`).
     *
     * @return array<string, mixed>|null
     */
    public function approval(): ?array
    {
        return Data::object($this->body, 'approval');
    }

    /** On the Account API's insufficient_permission: the permission the token lacks. */
    public function needs(): ?string
    {
        return Data::nullableString($this->body, 'needs');
    }

    /**
     * On the Account API: operations meant, on a wrong operation or path.
     *
     * @return list<array<string, mixed>>
     */
    public function didYouMean(): array
    {
        return Data::objects($this->body, 'did_you_mean');
    }

    /** The scope the call needs, on insufficient_scope (from `WWW-Authenticate`). */
    public function requiredScope(): ?string
    {
        $header = $this->header('WWW-Authenticate');
        if ($header !== null && preg_match('/scope="([^"]*)"/', $header, $match) === 1) {
            return $match[1];
        }

        return null;
    }

    /**
     * @internal the transport names the key on every error of a keyed call, first in the message of
     * a failure after sending (a 5xx: the server may have acted)
     */
    public function attachIdempotencyKey(string $key, bool $afterSend, bool $secret = false, bool $keyReleased = false): void
    {
        $this->idempotencyKey = $key;
        if ($afterSend) {
            // The server releases the key on a 5xx unless the key and the effect commit together
            // (`idempotent`): then the same key runs it again, so the caller looks first.
            $prefix = $keyReleased
                ? "Idempotency-Key {$key}: the request may have been applied, and Tawked released the key on this 5xx, so the same key runs it again: check whether it took effect (get or list it by its id or reference) before calling again."
                : "Idempotency-Key {$key}: the request may have been applied; to retry safely call again with `idempotency_key: '{$key}'` (not a new key).";
            if ($secret) {
                $prefix .= ' Its answer holds a secret shown once: find what it made by its id, then rotate or revoke it.';
            }
            $this->message = $prefix.' '.$this->message;
        }
    }

    /**
     * The API's human detail, on the codes that carry one.
     */
    public function apiMessage(): ?string
    {
        return Data::nullableString($this->body, 'message');
    }

    /**
     * The fields the API refused (`invalid_request` on the Partner API, the logo upload and
     * the template writes).
     *
     * @return list<string>
     */
    public function fields(): array
    {
        return Data::strings($this->body, 'fields');
    }

    /**
     * Each refused field with its reason codes (`invalid_request` on the template writes):
     * `['body' => ['placeholderEdges']]`, or `['name' => ['nameReserved']]` for a name a
     * connected app may not take. Empty on any other error.
     *
     * @return array<string, list<string>>
     */
    public function fieldErrors(): array
    {
        $out = [];
        foreach (Data::object($this->body, 'errors') ?? [] as $field => $codes) {
            if (is_array($codes)) {
                $out[$field] = Data::strings(['codes' => $codes], 'codes');
            }
        }

        return $out;
    }

    /**
     * What still sends the template, on `template_in_use`: `campaign`, `automation`,
     * `store_notice`. Empty on any other error.
     *
     * @return list<string>
     */
    public function usedBy(): array
    {
        return Data::strings($this->body, 'used_by');
    }

    /**
     * The current status the error names: a verification's on `not_resendable` and
     * `not_cancelable`, a delivery's on `not_retryable`. On `template_not_editable`, why:
     * the template's status, `META_SAMPLE` or `MEDIA_HEADER`.
     */
    public function currentStatus(): ?string
    {
        return Data::nullableString($this->body, 'status');
    }

    /** The same as currentStatus(): the status of the resource the error names. */
    public function resourceStatus(): ?string
    {
        return $this->currentStatus();
    }

    public function header(string $name): ?string
    {
        return $this->headers[strtolower($name)][0] ?? null;
    }

    /**
     * @param array<string, mixed> $body
     */
    private static function describe(int $status, string $code, ?string $requestId, array $body): string
    {
        $text = "Tawked API error {$code} (HTTP {$status})";
        $detail = Data::nullableString($body, 'message');
        if ($detail !== null && $detail !== '') {
            $text .= ': '.$detail;
        }
        $fields = Data::strings($body, 'fields');
        if ($fields !== []) {
            $text .= ' [fields: '.implode(', ', $fields).']';
        }
        if ($requestId !== null) {
            $text .= " (request id {$requestId})";
        }

        return $text;
    }
}
