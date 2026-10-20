<?php

declare(strict_types=1);

namespace Tawked\Internal;

use JsonException;
use Psr\Http\Message\ResponseInterface;
use Tawked\ErrorCode;
use Tawked\Exception\ApiException;
use Tawked\Exception\ApprovalRequiredException;
use Tawked\Exception\AuthenticationException;
use Tawked\Exception\BadRequestException;
use Tawked\Exception\ConflictException;
use Tawked\Exception\GoneException;
use Tawked\Exception\IdempotencyException;
use Tawked\Exception\InsufficientCreditsException;
use Tawked\Exception\InsufficientScopeException;
use Tawked\Exception\NotFoundException;
use Tawked\Exception\OAuthException;
use Tawked\Exception\PermissionDeniedException;
use Tawked\Exception\RateLimitException;
use Tawked\Exception\SecretNotReplayableException;
use Tawked\Exception\ServerException;
use Tawked\Exception\UnexpectedResponseException;
use Tawked\Exception\UnprocessableEntityException;
use Tawked\Http\Transport;

/**
 * Turns an error response into the exception of its code: by the code's
 * documented status (Tawked\ErrorCode::STATUS), falling back to the answer's HTTP status for a code
 * the contract does not document.
 *
 * @internal
 */
final class Errors
{
    /** @var array<string, class-string<ApiException>> */
    private const BY_CODE = [
        ErrorCode::INSUFFICIENT_SCOPE => InsufficientScopeException::class,
        ErrorCode::INSUFFICIENT_CREDITS => InsufficientCreditsException::class,
        ErrorCode::IDEMPOTENCY_KEY_REUSED => IdempotencyException::class,
        ErrorCode::IDEMPOTENCY_IN_PROGRESS => IdempotencyException::class,
        'idempotency_secret_not_replayable' => SecretNotReplayableException::class,
        'approval_required' => ApprovalRequiredException::class,
        'product_retired' => GoneException::class,
    ];

    /** @var array<int, class-string<ApiException>> */
    private const BY_STATUS = [
        400 => BadRequestException::class,
        401 => AuthenticationException::class,
        402 => InsufficientCreditsException::class,
        403 => PermissionDeniedException::class,
        404 => NotFoundException::class,
        409 => ConflictException::class,
        410 => GoneException::class,
        422 => UnprocessableEntityException::class,
        428 => ApprovalRequiredException::class,
        429 => RateLimitException::class,
    ];

    /** The role of each class, the words of the shared role table, for the cross-language check. */
    public const ROLES = [
        InsufficientScopeException::class => 'scope',
        InsufficientCreditsException::class => 'credits',
        SecretNotReplayableException::class => 'secret not replayable',
        IdempotencyException::class => 'idempotency',
        ApprovalRequiredException::class => 'approval',
        GoneException::class => 'gone',
        BadRequestException::class => 'bad request',
        AuthenticationException::class => 'authentication',
        PermissionDeniedException::class => 'permission',
        NotFoundException::class => 'not found',
        ConflictException::class => 'conflict',
        UnprocessableEntityException::class => 'unprocessable',
        RateLimitException::class => 'rate limit',
        ServerException::class => 'server',
        UnexpectedResponseException::class => 'unexpected response',
        OAuthException::class => 'OAuth',
        ApiException::class => 'API base',
    ];

    public static function fromResponse(ResponseInterface $response, string $surface = Transport::SURFACE_PRODUCT): ApiException
    {
        $meta = Transport::meta($response);
        $body = self::decode((string) $response->getBody()) ?? [];
        $code = $body['error'] ?? null;
        $status = $meta->statusCode;
        if ((! is_string($code) || $code === '') && $status === 429) {
            // Laravel's own throttle answers `{message}` alone (the OAuth exchange's limit): a rate limit all the same.
            return new RateLimitException($status, ErrorCode::TOO_MANY_REQUESTS, $meta->requestId, $body, $meta->headers);
        }
        if (! is_string($code) || $code === '') {
            return new UnexpectedResponseException($status, ErrorCode::UNEXPECTED_RESPONSE, $meta->requestId, $body, $meta->headers);
        }

        if ($surface === Transport::SURFACE_OAUTH && $status < 500 && $status !== 429) {
            return new OAuthException($status, $code, $meta->requestId, $body, $meta->headers);
        }

        return new (self::classFor($code, $status, $surface))($status, $code, $meta->requestId, $body, $meta->headers);
    }

    /**
     * @return class-string<ApiException>
     */
    public static function classFor(string $code, int $status, string $surface = Transport::SURFACE_PRODUCT): string
    {
        if (isset(self::BY_CODE[$code])) {
            return self::BY_CODE[$code];
        }
        $documented = $surface === Transport::SURFACE_PRODUCT ? (ErrorCode::STATUS[$code] ?? $status) : $status;

        return self::BY_STATUS[$documented] ?? ($documented >= 500 ? ServerException::class : ApiException::class);
    }

    public static function unexpected(ResponseInterface $response): UnexpectedResponseException
    {
        $meta = Transport::meta($response);

        return new UnexpectedResponseException($meta->statusCode, ErrorCode::UNEXPECTED_RESPONSE, $meta->requestId, [], $meta->headers);
    }

    /**
     * @return array<string, mixed>|null
     */
    private static function decode(string $raw): ?array
    {
        try {
            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return null;
        }

        return is_array($decoded) ? Data::stringKeys($decoded) : null;
    }
}
