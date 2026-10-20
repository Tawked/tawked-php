<?php

declare(strict_types=1);

namespace Tawked\Tests\Contract;

use GuzzleHttp\Client;
use PHPUnit\Framework\Attributes\DataProvider;
use Tawked\Exception\InvalidArgumentException;
use Tawked\Exception\OAuthException;
use Tawked\Exception\RateLimitException;
use Tawked\Exception\ServerException;
use Tawked\Exception\TimeoutException;
use Tawked\Model\OAuth\AppConnection;
use Tawked\OAuth\Apps;
use Tawked\Tests\Support\ContractCase;
use Tawked\Tests\Support\Mock;
use Tawked\Tests\Support\Recorder;

/**
 * Connected apps: the PKCE worked example, the authorize URL the mock approves, the exchange, its
 * errors as OAuthException, and an exchange that is sent once after a 500 and after a timeout.
 */
final class OAuthContractTest extends ContractCase
{
    private const CLIENT_ID = 'app_mock_client';

    private const SECRET = 'mock-client-secret-0000000000000000';

    private const RETURN = 'https://app.example/return';

    public function test_the_pkce_worked_example(): void
    {
        $oauth = self::contract()['oauth'];
        self::assertIsArray($oauth);
        $example = $oauth['pkce']['worked_example'];
        self::assertSame($example['code_challenge'], Apps::challenge((string) $example['code_verifier']));
        self::assertMatchesRegularExpression('/^[A-Za-z0-9_-]{43}$/', Apps::newVerifier());
    }

    public function test_authorize_url_makes_no_request_and_carries_the_s256_challenge(): void
    {
        $start = Apps::authorizeUrl(['client_id' => self::CLIENT_ID, 'redirect_uri' => self::RETURN, 'base_url' => Mock::url()]);

        self::assertCount(0, Mock::requests());
        parse_str((string) parse_url($start->url, PHP_URL_QUERY), $query);
        self::assertSame(['response_type', 'client_id', 'redirect_uri', 'state', 'code_challenge', 'code_challenge_method'], array_keys($query));
        self::assertSame('S256', $query['code_challenge_method']);
        self::assertSame(Apps::challenge($start->codeVerifier), $query['code_challenge']);
        self::assertSame($start->state, $query['state']);
        self::assertStringStartsWith(Mock::url().'/oauth/authorize?', $start->url);
        Recorder::observe('oauth', 'apps.authorizeUrl', 'requests', 0);

        $this->expectException(InvalidArgumentException::class);
        Apps::authorizeUrl(['client_id' => self::CLIENT_ID, 'redirect_uri' => self::RETURN, 'scope' => 'all']);
    }

    public function test_the_code_from_the_approval_is_exchanged_once(): void
    {
        $code = $this->approvedCode($start);

        $connection = Apps::exchangeCode([
            'client_id' => self::CLIENT_ID,
            'client_secret' => self::SECRET,
            'code' => $code,
            'redirect_uri' => self::RETURN,
            'code_verifier' => $start->codeVerifier,
        ], ['base_url' => Mock::url()]);

        self::assertInstanceOf(AppConnection::class, $connection);
        self::assertStringStartsWith('tk_live_', $connection->accessToken);
        self::assertSame('Bearer', $connection->tokenType);
        $sent = self::requestsOf('oauth-token');
        self::assertCount(1, $sent);
        self::assertStringStartsWith('Basic ', (string) ($sent[0]['headers']['authorization'] ?? ''));
        self::assertSame('application/x-www-form-urlencoded', $sent[0]['headers']['content-type'] ?? null);
        self::assertArrayNotHasKey('idempotency-key', $sent[0]['headers']);

        // Single-use: the same code again is invalid_grant.
        try {
            Apps::exchangeCode(['client_id' => self::CLIENT_ID, 'client_secret' => self::SECRET, 'code' => $code, 'redirect_uri' => self::RETURN, 'code_verifier' => $start->codeVerifier], ['base_url' => Mock::url()]);
            self::fail('A code is single-use.');
        } catch (OAuthException $e) {
            self::assertSame('invalid_grant', $e->errorCode);
            self::assertSame(400, $e->statusCode);
        }
    }

    public function test_client_secret_post_sends_the_client_in_the_body(): void
    {
        $code = $this->approvedCode($start);
        Apps::exchangeCode(['client_id' => self::CLIENT_ID, 'client_secret' => self::SECRET, 'code' => $code, 'redirect_uri' => self::RETURN, 'code_verifier' => $start->codeVerifier], ['base_url' => Mock::url(), 'client_auth' => 'post']);

        $sent = self::requestsOf('oauth-token');
        parse_str((string) $sent[0]['body'], $form);
        self::assertSame(self::CLIENT_ID, $form['client_id']);
        self::assertArrayNotHasKey('authorization', $sent[0]['headers']);
    }

    /**
     * @return iterable<string, array{string, int}>
     */
    public static function oauthErrors(): iterable
    {
        foreach (self::contract()['oauth']['endpoints'][1]['errors'] as $error) {
            yield (string) $error['error'] => [(string) $error['error'], (int) $error['http']];
        }
    }

    #[DataProvider('oauthErrors')]
    public function test_every_exchange_error_is_an_oauth_exception(string $code, int $status): void
    {
        $result = self::attempt(fn(array $o) => Apps::exchangeCode(self::params(), ['base_url' => Mock::url(), 'headers' => ['X-Mock-Error' => $code]]));

        self::assertInstanceOf(OAuthException::class, $result);
        self::assertSame($code, $result->errorCode);
        self::assertSame($status, $result->statusCode);
        self::assertNotNull($result->description());
    }

    public function test_the_client_secret_never_shows_in_an_exception_trace(): void
    {
        foreach (['basic', 'post'] as $auth) {
            $result = self::attempt(fn(array $o) => Apps::exchangeCode(self::params(), ['base_url' => Mock::url(), 'headers' => ['X-Mock-Error' => 'invalid_client'], 'client_auth' => $auth]));

            self::assertInstanceOf(OAuthException::class, $result);
            $dump = self::sdkDump($result);
            self::assertStringNotContainsString(self::SECRET, $dump);
            self::assertStringNotContainsString(base64_encode(self::CLIENT_ID.':'.self::SECRET), $dump);
        }
    }

    public function test_too_many_requests_is_a_rate_limit(): void
    {
        $result = self::attempt(fn(array $o) => Apps::exchangeCode(self::params(), ['base_url' => Mock::url(), 'headers' => ['X-Mock-Error' => 'too_many_requests'], 'max_retries' => 0]));

        self::assertInstanceOf(RateLimitException::class, $result);
    }

    public function test_the_exchange_is_never_retried_once_sent(): void
    {
        $result = self::attempt(fn(array $o) => Apps::exchangeCode(self::params(), ['base_url' => Mock::url(), 'headers' => ['X-Mock-Fault' => '500']]));
        self::assertInstanceOf(ServerException::class, $result);
        self::assertCount(1, self::requestsOf('oauth-token'));
        Recorder::observe('oauth', 'apps.exchangeCode', 'after_500', 1);
        Recorder::observe('oauth', 'apps.exchangeCode', 'key_sent', false);

        Mock::reset();
        $this->fault = 'timeout';
        $options = $this->mockOptions();
        $result = self::attempt(fn(array $o) => Apps::exchangeCode(self::params(), $options));
        self::assertInstanceOf(TimeoutException::class, $result);
        self::assertStringContainsString('secret shown once', $result->getMessage());
        self::assertCount(1, self::requestsOf('oauth-token'));
        Recorder::observe('oauth', 'apps.exchangeCode', 'after_timeout', 1);

        Mock::reset();
        $this->fault = 'pre';
        $result = self::attempt(fn(array $o) => Apps::exchangeCode(self::params(), $options));
        self::assertInstanceOf(AppConnection::class, $result, 'a failure before sending is retried: nothing reached Tawked');
        Recorder::observe('oauth', 'apps.exchangeCode', 'before_send', 'retried');
    }

    /**
     * @return array<string, string>
     */
    private static function params(): array
    {
        return [
            'client_id' => self::CLIENT_ID,
            'client_secret' => self::SECRET,
            'code' => 'mock-code-'.bin2hex(random_bytes(4)),
            'redirect_uri' => self::RETURN,
            'code_verifier' => Apps::newVerifier(),
        ];
    }

    private function approvedCode(mixed &$start): string
    {
        $start = Apps::authorizeUrl(['client_id' => self::CLIENT_ID, 'redirect_uri' => self::RETURN, 'base_url' => Mock::url()]);
        $response = (new Client(['allow_redirects' => false, 'http_errors' => false]))->get($start->url);
        self::assertSame(302, $response->getStatusCode());
        parse_str((string) parse_url($response->getHeaderLine('Location'), PHP_URL_QUERY), $query);
        self::assertSame($start->state, $query['state']);
        self::assertIsString($query['code']);

        return $query['code'];
    }
}
