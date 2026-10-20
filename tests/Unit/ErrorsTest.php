<?php

declare(strict_types=1);

namespace Tawked\Tests\Unit;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;
use Tawked\ErrorCode;
use Tawked\Exception\ApiException;
use Tawked\Exception\AuthenticationException;
use Tawked\Exception\BadRequestException;
use Tawked\Exception\ConflictException;
use Tawked\Exception\IdempotencyException;
use Tawked\Exception\InsufficientCreditsException;
use Tawked\Exception\InsufficientScopeException;
use Tawked\Exception\NotFoundException;
use Tawked\Exception\PermissionDeniedException;
use Tawked\Exception\RateLimitException;
use Tawked\Exception\ServerException;
use Tawked\Exception\TawkedException;
use Tawked\Exception\UnexpectedResponseException;
use Tawked\Exception\UnprocessableEntityException;
use Tawked\Tests\TestCase;

final class ErrorsTest extends TestCase
{
    /**
     * @return iterable<string, array{string, int}>
     */
    public static function contractErrors(): iterable
    {
        $errors = self::contract()['errors'];
        self::assertIsArray($errors);
        foreach ($errors as $error) {
            self::assertIsArray($error);
            yield (string) $error['error'] => [(string) $error['error'], (int) $error['http']];
        }
    }

    #[DataProvider('contractErrors')]
    public function test_every_error_code_of_the_contract_becomes_its_typed_exception(string $code, int $status): void
    {
        $tawked = $this->client([self::json($status, ['error' => $code], ['X-Request-Id' => 'req-abc'])], ['max_retries' => 0]);

        try {
            $tawked->verify->get('b7e5c2b0-9c1a-4e2f-8f2a-3a6b0e9d1c44');
            self::fail('No exception thrown.');
        } catch (ApiException $e) {
            self::assertInstanceOf(self::expectedClass($code, $status), $e);
            self::assertInstanceOf(TawkedException::class, $e);
            self::assertSame($code, $e->errorCode);
            self::assertSame($status, $e->statusCode);
            self::assertSame('req-abc', $e->requestId);
            self::assertSame(['error' => $code], $e->body);
            self::assertStringContainsString($code, $e->getMessage());
            self::assertStringContainsString('req-abc', $e->getMessage());
            self::assertStringNotContainsString(self::KEY, $e->getMessage());
        }
    }

    #[DataProvider('contractErrors')]
    public function test_every_error_code_has_its_constant(string $code, int $status): void
    {
        $constants = (new ReflectionClass(ErrorCode::class))->getConstants();

        self::assertSame($code, $constants[strtoupper($code)] ?? null);
        self::assertSame($status, ErrorCode::STATUS[$code]);
    }

    public function test_error_code_holds_no_code_the_contract_lacks(): void
    {
        $contract = array_map(static fn(array $args): string => $args[0], iterator_to_array(self::contractErrors()));

        self::assertEqualsCanonicalizing(array_values($contract), array_keys(ErrorCode::STATUS));
    }

    public function test_invalid_request_names_its_fields_and_message(): void
    {
        $tawked = $this->client([self::json(400, ['error' => 'invalid_request', 'fields' => ['logo'], 'message' => 'The logo must be a PNG or a JPEG.'])], [], self::PARTNER_KEY);

        try {
            $tawked->partner->applications->removeLogo('store_88');
            self::fail('No exception thrown.');
        } catch (BadRequestException $e) {
            self::assertSame(['logo'], $e->fields());
            self::assertSame('The logo must be a PNG or a JPEG.', $e->apiMessage());
            self::assertStringContainsString('The logo must be a PNG or a JPEG. [fields: logo]', $e->getMessage());
        }
    }

    public function test_not_resendable_carries_the_current_status(): void
    {
        $tawked = $this->client([self::json(409, ['error' => 'not_resendable', 'status' => 'verified'])]);

        try {
            $tawked->verify->resend('b7e5c2b0-9c1a-4e2f-8f2a-3a6b0e9d1c44');
            self::fail('No exception thrown.');
        } catch (ConflictException $e) {
            self::assertSame(ErrorCode::NOT_RESENDABLE, $e->errorCode);
            self::assertSame('verified', $e->currentStatus());
        }
    }

    public function test_a_refused_template_names_each_field_with_its_reasons(): void
    {
        $body = ['error' => 'invalid_request', 'fields' => ['body', 'examples.header:order'], 'errors' => ['body' => ['placeholderEdges'], 'examples.header:order' => ['exampleRequired']]];
        $tawked = $this->client([self::json(400, $body)]);

        try {
            $tawked->whatsapp->templates->create(['name' => 'a', 'language' => 'ar', 'category' => 'UTILITY', 'body' => '{{1}}']);
            self::fail('No exception thrown.');
        } catch (BadRequestException $e) {
            self::assertSame(ErrorCode::INVALID_REQUEST, $e->errorCode);
            self::assertSame(['body', 'examples.header:order'], $e->fields());
            self::assertSame(['body' => ['placeholderEdges'], 'examples.header:order' => ['exampleRequired']], $e->fieldErrors());
            self::assertStringContainsString('[fields: body, examples.header:order]', $e->getMessage());
        }
    }

    public function test_a_name_a_connected_app_may_not_take_is_name_reserved(): void
    {
        $tawked = $this->client([self::json(400, ['error' => 'invalid_request', 'fields' => ['name'], 'errors' => ['name' => ['nameReserved']]])]);

        try {
            $tawked->whatsapp->templates->create(['name' => 'salla_order_shipped', 'language' => 'ar', 'category' => 'UTILITY', 'body' => 'مرحبا']);
            self::fail('No exception thrown.');
        } catch (BadRequestException $e) {
            self::assertSame(['name'], $e->fields());
            self::assertSame(['nameReserved'], $e->fieldErrors()['name']);
        }
    }

    public function test_template_in_use_says_what_still_sends_it_on_a_delete_and_on_an_apps_edit(): void
    {
        $tawked = $this->client([
            self::json(409, ['error' => 'template_in_use', 'used_by' => ['campaign', 'automation']]),
            self::json(409, ['error' => 'template_in_use', 'used_by' => ['store_notice']]),
        ]);

        try {
            $tawked->whatsapp->templates->delete('7000');
            self::fail('No exception thrown.');
        } catch (ConflictException $e) {
            self::assertSame(ErrorCode::TEMPLATE_IN_USE, $e->errorCode);
            self::assertSame(409, $e->statusCode);
            self::assertSame(['campaign', 'automation'], $e->usedBy());
        }

        try {
            $tawked->whatsapp->templates->update('7000', ['category' => 'UTILITY', 'body' => 'مرحبا']);
            self::fail('No exception thrown.');
        } catch (ConflictException $e) {
            self::assertSame(['store_notice'], $e->usedBy());
        }
    }

    public function test_template_not_editable_says_why(): void
    {
        $tawked = $this->client([self::json(409, ['error' => 'template_not_editable', 'status' => 'MEDIA_HEADER'])]);

        try {
            $tawked->whatsapp->templates->update('7000', ['category' => 'UTILITY', 'body' => 'مرحبا']);
            self::fail('No exception thrown.');
        } catch (ConflictException $e) {
            self::assertSame(ErrorCode::TEMPLATE_NOT_EDITABLE, $e->errorCode);
            self::assertSame('MEDIA_HEADER', $e->currentStatus());
            self::assertSame([], $e->usedBy());
            self::assertSame([], $e->fieldErrors());
        }
    }

    public function test_template_refused_carries_metas_own_sentence(): void
    {
        $tawked = $this->client([self::json(422, ['error' => 'template_refused', 'message' => 'Meta error 100: Template name already exists'])]);

        try {
            $tawked->whatsapp->templates->create(['name' => 'a', 'language' => 'ar', 'category' => 'UTILITY', 'body' => 'مرحبا']);
            self::fail('No exception thrown.');
        } catch (UnprocessableEntityException $e) {
            self::assertSame(ErrorCode::TEMPLATE_REFUSED, $e->errorCode);
            self::assertSame('Meta error 100: Template name already exists', $e->apiMessage());
            self::assertStringContainsString('Meta error 100: Template name already exists', $e->getMessage());
        }
    }

    public function test_template_not_managed_is_a_permission_error(): void
    {
        $tawked = $this->client([self::json(403, ['error' => 'template_not_managed'])]);

        try {
            $tawked->whatsapp->templates->delete('7000');
            self::fail('No exception thrown.');
        } catch (PermissionDeniedException $e) {
            self::assertSame(ErrorCode::TEMPLATE_NOT_MANAGED, $e->errorCode);
        }
    }

    public function test_a_business_key_on_a_template_write_lacks_the_scope_a_connected_app_holds(): void
    {
        $refusal = fn() => self::json(403, ['error' => 'insufficient_scope'], ['WWW-Authenticate' => 'Bearer error="insufficient_scope", scope="notifications:templates"']);
        $tawked = $this->client([$refusal(), $refusal(), $refusal()]);

        foreach ([
            fn() => $tawked->whatsapp->templates->create(['name' => 'a', 'language' => 'ar', 'category' => 'UTILITY', 'body' => 'مرحبا'], ['idempotency_key' => 'k-1']),
            fn() => $tawked->whatsapp->templates->update('7000', ['category' => 'UTILITY', 'body' => 'مرحبا'], ['idempotency_key' => 'k-2']),
            fn() => $tawked->whatsapp->templates->delete('7000', ['idempotency_key' => 'k-3']),
        ] as $write) {
            try {
                $write();
                self::fail('No exception thrown.');
            } catch (InsufficientScopeException $e) {
                self::assertInstanceOf(PermissionDeniedException::class, $e);
                self::assertSame(ErrorCode::INSUFFICIENT_SCOPE, $e->errorCode);
                self::assertSame('notifications:templates', $e->requiredScope());
            }
        }
        // A refusal is never retried, even under an Idempotency-Key.
        self::assertCount(3, $this->history);
    }

    public function test_insufficient_scope_names_the_scope_the_call_needs(): void
    {
        $tawked = $this->client([self::json(403, ['error' => 'insufficient_scope'], ['WWW-Authenticate' => 'Bearer error="insufficient_scope", scope="verify:send"'])]);

        try {
            $tawked->verify->start(['to' => '+966551234567']);
            self::fail('No exception thrown.');
        } catch (InsufficientScopeException $e) {
            self::assertSame('verify:send', $e->requiredScope());
        }
    }

    public function test_insufficient_credits_on_whatsapp_says_the_balance_and_the_price(): void
    {
        $tawked = $this->client([self::json(402, ['error' => 'insufficient_credits', 'message' => 'Insufficient credits', 'balance_halalas' => 0, 'price_halalas' => 9])]);

        try {
            $tawked->whatsapp->messages->send(['to' => '+966550000000', 'template' => 't']);
            self::fail('No exception thrown.');
        } catch (InsufficientCreditsException $e) {
            self::assertSame(0, $e->balanceHalalas());
            self::assertSame(9, $e->priceHalalas());
        }
    }

    public function test_rate_limit_reads_a_retry_after_when_one_is_sent(): void
    {
        $tawked = $this->client([self::json(429, ['error' => 'rate_limited'], ['Retry-After' => '30'])]);

        try {
            $tawked->verify->start(['to' => '+966551234567'], ['max_retries' => 0]);
            self::fail('No exception thrown.');
        } catch (RateLimitException $e) {
            self::assertSame(30, $e->retryAfter());
        }
    }

    public function test_a_response_that_is_not_the_api_json_is_an_unexpected_response(): void
    {
        $tawked = $this->client([
            new Response(404, ['Content-Type' => 'application/json'], '{"message":"The route v1/nope could not be found."}'),
            new Response(502, ['Content-Type' => 'text/html'], '<html>Bad gateway</html>'),
            new Response(200, ['Content-Type' => 'text/html'], '<html>not json</html>'),
        ], ['max_retries' => 0]);

        foreach ([[404, '{"message"'], [502, '<html>'], [200, '<html>']] as [$status]) {
            try {
                $tawked->verify->cancel('x');
                self::fail('No exception thrown.');
            } catch (UnexpectedResponseException $e) {
                self::assertSame($status, $e->statusCode);
                self::assertSame(ErrorCode::UNEXPECTED_RESPONSE, $e->errorCode);
            }
        }
    }

    public function test_an_unknown_code_keeps_its_status_class(): void
    {
        $tawked = $this->client([self::json(422, ['error' => 'a_code_added_later'])]);

        try {
            $tawked->verify->cancel('x');
            self::fail('No exception thrown.');
        } catch (UnprocessableEntityException $e) {
            self::assertSame('a_code_added_later', $e->errorCode);
        }
    }

    /**
     * @return class-string<ApiException>
     */
    private static function expectedClass(string $code, int $status): string
    {
        return match (true) {
            $code === 'insufficient_scope' => InsufficientScopeException::class,
            in_array($code, ['idempotency_key_reused', 'idempotency_in_progress'], true) => IdempotencyException::class,
            $status === 400 => BadRequestException::class,
            $status === 401 => AuthenticationException::class,
            $status === 402 => InsufficientCreditsException::class,
            $status === 403 => PermissionDeniedException::class,
            $status === 404 => NotFoundException::class,
            $status === 409 => ConflictException::class,
            $status === 422 => UnprocessableEntityException::class,
            $status === 410 => \Tawked\Exception\GoneException::class,
            $status === 429 => RateLimitException::class,
            $status >= 500 => ServerException::class,
            default => ApiException::class,
        };
    }
}
