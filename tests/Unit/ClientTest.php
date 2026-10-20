<?php

declare(strict_types=1);

namespace Tawked\Tests\Unit;

use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Tawked\Exception\ConnectionException;
use Tawked\Exception\InvalidArgumentException;
use Tawked\IdempotencyKey;
use Tawked\TawkedClient;
use Tawked\Tests\TestCase;
use Tawked\Version;

final class ClientTest extends TestCase
{
    public function test_a_key_is_required(): void
    {
        $previous = getenv('TAWKED_API_KEY');
        putenv('TAWKED_API_KEY');
        unset($_ENV['TAWKED_API_KEY'], $_SERVER['TAWKED_API_KEY']);

        try {
            $this->expectException(InvalidArgumentException::class);
            new TawkedClient('   ');
        } finally {
            if ($previous !== false) {
                putenv("TAWKED_API_KEY={$previous}");
            }
        }
    }

    public function test_the_key_is_read_from_the_environment_when_not_given(): void
    {
        putenv('TAWKED_API_KEY='.self::KEY);

        try {
            $client = new TawkedClient(null, ['http_client' => $this->recorder([self::json(200, ['data' => []])])]);
            $client->whatsapp->templates->list();
        } finally {
            putenv('TAWKED_API_KEY');
        }

        self::assertSame('Bearer '.self::KEY, $this->recorded[0]->getHeaderLine('Authorization'));
    }

    public function test_the_key_never_shows_in_a_dump(): void
    {
        $client = new TawkedClient(self::KEY);

        self::assertStringNotContainsString(self::KEY, print_r($client, true));
        self::assertStringNotContainsString(self::KEY, print_r($client->verify, true));
        ob_start();
        var_dump($client);
        $dump = (string) ob_get_clean();
        self::assertStringNotContainsString(self::KEY, $dump);
        self::assertStringContainsString('[hidden]', $dump);

        // var_export() and an (array) cast skip __debugInfo, so the key must not sit in a property.
        self::assertStringNotContainsString(self::KEY, var_export($client, true));
        self::assertStringNotContainsString(self::KEY, var_export($client->verify, true));
        self::assertStringNotContainsString(self::KEY, print_r((array) $client, true));
    }

    public function test_the_key_never_shows_in_a_dump_of_a_connection_error(): void
    {
        // A real Guzzle failure: its exception holds the request with the Authorization header.
        $client = new TawkedClient(self::KEY, ['base_url' => 'http://127.0.0.1:1', 'max_retries' => 0, 'connect_timeout' => 2]);

        try {
            $client->verify->get('b7e5c2b0-9c1a-4e2f-8f2a-3a6b0e9d1c44');
            self::fail('Expected a ConnectionException.');
        } catch (ConnectionException $e) {
            self::assertStringNotContainsString(self::KEY, self::sdkDump($e));
            self::assertStringNotContainsString(self::KEY, var_export($e, true));
            self::assertStringNotContainsString(self::KEY, (string) $e);
        }
    }

    public function test_unknown_or_malformed_options_are_refused(): void
    {
        foreach ([
            ['baseUrl' => 'https://tawked.com'],
            ['base_url' => 'tawked.com'],
            ['timeout' => 0],
            ['timeout' => '30'],
            ['max_retries' => -1],
            ['http_client' => new \stdClass()],
            ['headers' => 'x'],
            ['sleep' => 'not a function at all'],
        ] as $options) {
            try {
                new TawkedClient(self::KEY, $options);
                self::fail('Accepted '.json_encode($options));
            } catch (InvalidArgumentException) {
                $this->addToAssertionCount(1);
            }
        }
    }

    public function test_a_malformed_idempotency_key_or_unknown_call_option_is_refused_before_sending(): void
    {
        $tawked = $this->client([]);

        foreach ([['idempotency_key' => ''], ['idempotency_key' => str_repeat('a', 129)], ['idempotency_key' => 'has space'], ['idempotency_key' => 'مفتاح'], ['idempotencyKey' => 'k'], ['timeout' => -1], ['max_retries' => 1.5]] as $options) {
            try {
                $tawked->verify->start(['to' => '+966551234567'], $options);
                self::fail('Accepted '.json_encode($options));
            } catch (InvalidArgumentException) {
                $this->addToAssertionCount(1);
            }
        }
        self::assertSame([], $this->history);
    }

    public function test_base_url_and_headers_apply_and_cannot_replace_the_key(): void
    {
        $tawked = $this->client([self::json(200, ['data' => []])], [
            'base_url' => 'http://127.0.0.1:4010/',
            'headers' => ['X-Team' => 'growth', 'Authorization' => 'Bearer tk_live_other'],
        ]);

        $tawked->whatsapp->templates->list([], ['headers' => ['X-Trace' => 't1', 'authorization' => 'nope']]);

        self::assertSame('http://127.0.0.1:4010/v1/whatsapp/templates', (string) $this->request()->getUri());
        self::assertSame('growth', $this->request()->getHeaderLine('X-Team'));
        self::assertSame('t1', $this->request()->getHeaderLine('X-Trace'));
        self::assertSame('Bearer '.self::KEY, $this->request()->getHeaderLine('Authorization'));
        self::assertSame('tawked-php/'.Version::VERSION.' (php/'.PHP_VERSION.'; api '.\Tawked\Contract::API_VERSION.')', $this->request()->getHeaderLine('User-Agent'));
    }

    public function test_any_psr18_client_works(): void
    {
        $client = new TawkedClient(self::KEY, ['http_client' => $this->recorder([self::json(201, ['id' => 'v1', 'status' => 'pending', 'expires_at' => '2026-09-01T12:34:56.789Z'])])]);

        $started = $client->verify->start(['to' => '+966551234567']);

        self::assertSame('v1', $started->id);
        self::assertSame('https://tawked.com/v1/verify/start', (string) $this->recorded[0]->getUri());
    }

    public function test_idempotency_keys_are_uuids_and_fit_the_api(): void
    {
        $a = IdempotencyKey::generate();
        $b = IdempotencyKey::generate();

        self::assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $a);
        self::assertNotSame($a, $b);
    }

    public function test_the_version_matches_the_changelog(): void
    {
        self::assertStringContainsString('## '.Version::VERSION, (string) file_get_contents(__DIR__.'/../../CHANGELOG.md'));
    }

    /** @var list<RequestInterface> */
    private array $recorded = [];

    /**
     * A bare PSR-18 client (not Guzzle).
     *
     * @param list<ResponseInterface> $responses
     */
    private function recorder(array $responses): ClientInterface
    {
        $this->recorded = [];
        $test = $this;

        return new class ($responses, $test) implements ClientInterface {
            /**
             * @param list<ResponseInterface> $responses
             */
            public function __construct(private array $responses, private ClientTest $test) {}

            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                $this->test->record($request);

                return array_shift($this->responses) ?? (new HttpFactory())->createResponse(500);
            }
        };
    }

    /**
     * @internal
     */
    public function record(RequestInterface $request): void
    {
        $this->recorded[] = $request;
    }

    public function test_a_bare_response_factory_answer_is_handled(): void
    {
        $client = new TawkedClient(self::KEY, ['http_client' => $this->recorder([new Response(204)])]);

        self::assertSame([], $client->verify->cancel('x')->toArray());
    }
}
