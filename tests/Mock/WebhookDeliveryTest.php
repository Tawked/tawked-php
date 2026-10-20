<?php

declare(strict_types=1);

namespace Tawked\Tests\Mock;

use GuzzleHttp\Client;
use PHPUnit\Framework\Attributes\DataProvider;
use Tawked\Tests\Support\Mock;
use Tawked\Tests\TestCase;

/**
 * A webhook delivered over real HTTP by the mock (signed exactly as Tawked delivers it) to a PHP
 * endpoint written as an integrator writes it (receiver.php, Tawked\Webhook::constructEvent), in
 * both schemes, and in both at once (an endpoint with Standard signing on).
 */
final class WebhookDeliveryTest extends TestCase
{
    private const TAWKED_SECRET = 'mock-tawked-secret-0000000000000001';

    private const STANDARD_SECRET = 'whsec_AAECAwQFBgcICQoLDA0ODxAREhMUFRYXGBkaGxwdHh8=';

    /** @var resource|null */
    private static $receiver = null;

    private static string $receiverUrl = '';

    private static string $log = '';

    public static function setUpBeforeClass(): void
    {
        self::$log = (string) tempnam(sys_get_temp_dir(), 'tawked-received');
        $socket = stream_socket_server('tcp://127.0.0.1:0');
        self::assertIsResource($socket);
        $name = (string) stream_socket_get_name($socket, false);
        fclose($socket);
        $port = (int) substr($name, strrpos($name, ':') + 1);
        $env = [
            'TAWKED_WEBHOOK_SECRETS' => self::TAWKED_SECRET.','.self::STANDARD_SECRET,
            'TAWKED_RECEIVED_LOG' => self::$log,
            'PATH' => (string) getenv('PATH'),
        ];
        $process = proc_open([PHP_BINARY, '-S', "127.0.0.1:{$port}", __DIR__.'/receiver.php'], [1 => ['file', '/dev/null', 'w'], 2 => ['file', '/dev/null', 'w']], $pipes, null, $env);
        self::assertIsResource($process);
        self::$receiver = $process;
        for ($i = 0; $i < 100; $i++) {
            $probe = @fsockopen('127.0.0.1', $port, $errno, $errstr, 0.1);
            if (is_resource($probe)) {
                fclose($probe);
                break;
            }
            usleep(50_000);
        }
        self::$receiverUrl = "http://127.0.0.1:{$port}/webhook";
    }

    public static function tearDownAfterClass(): void
    {
        if (is_resource(self::$receiver)) {
            proc_terminate(self::$receiver);
            proc_close(self::$receiver);
        }
        if (is_file(self::$log)) {
            unlink(self::$log);
        }
    }

    /**
     * @return iterable<string, array{array<string, mixed>, string}>
     */
    public static function deliveries(): iterable
    {
        yield 'tawked, an application event' => [['scheme' => 'tawked', 'secret' => self::TAWKED_SECRET, 'event' => 'verification.verified'], 'Tawked\Model\Webhook\VerificationEventData'];
        yield 'tawked, a partner event' => [['scheme' => 'tawked', 'secret' => self::TAWKED_SECRET, 'event' => 'service.approved', 'side' => 'partner'], 'Tawked\Model\Webhook\ApplicationEventData'];
        yield 'tawked, an e-mail event' => [['scheme' => 'tawked', 'secret' => self::TAWKED_SECRET, 'event' => 'email.bounced'], 'Tawked\Model\Webhook\EmailEventData'];
        yield 'standard webhooks' => [['scheme' => 'standard_webhooks', 'secret' => self::STANDARD_SECRET, 'event' => 'email.delivered'], 'Tawked\Model\Webhook\EmailEventData'];
        yield 'both header sets' => [['scheme' => 'both', 'secret' => self::TAWKED_SECRET, 'standard_secret' => self::STANDARD_SECRET, 'event' => 'message.delivered'], 'Tawked\Model\Webhook\MessageEventData'];
    }

    /**
     * @param array<string, mixed> $delivery
     */
    #[DataProvider('deliveries')]
    public function test_a_delivery_signed_by_the_mock_verifies_at_the_receiver(array $delivery, string $typed): void
    {
        file_put_contents(self::$log, '');
        // A receiver holds its endpoint's secrets: the Tawked one for a tawked delivery, the whsec_ one for a
        // Standard Webhooks delivery, both for an endpoint that sends both header sets.
        $holds = ['tawked' => 'tawked', 'standard_webhooks' => 'standard'][$delivery['scheme']] ?? 'both';
        $answer = $this->deliver([...$delivery, 'url' => self::$receiverUrl.'?holds='.$holds]);

        self::assertSame(204, $answer['status'] ?? null, (string) file_get_contents(self::$log));
        $received = json_decode(trim((string) file_get_contents(self::$log)), true);
        self::assertIsArray($received);
        self::assertSame($delivery['event'], $received['event']);
        // An e-mail event of a message of several recipients carries `recipient`: its own class.
        $expected = is_array($received['data']) && array_key_exists('recipient', $received['data']) ? str_replace('EmailEventData', 'EmailRecipientEventData', $typed) : $typed;
        self::assertSame($expected, $received['typed']);
    }

    public function test_a_delivery_signed_with_another_secret_is_refused(): void
    {
        file_put_contents(self::$log, '');
        $answer = $this->deliver(['url' => self::$receiverUrl.'?holds=tawked', 'scheme' => 'tawked', 'secret' => 'another-secret-00000000000000000001', 'event' => 'message.read']);

        self::assertSame(400, $answer['status'] ?? null);
        self::assertStringContainsString('signature_mismatch', (string) file_get_contents(self::$log));
    }

    public function test_a_receiver_holding_a_whsec_secret_checks_standard_webhooks_alone_even_when_only_tawked_signature_arrives(): void
    {
        // the secrets choose the scheme, never the headers; nothing falls back to tawked-signature.
        file_put_contents(self::$log, '');
        $answer = $this->deliver(['url' => self::$receiverUrl.'?holds=both', 'scheme' => 'tawked', 'secret' => self::TAWKED_SECRET, 'event' => 'message.read']);

        self::assertSame(400, $answer['status'] ?? null);
        self::assertStringContainsString('missing_header', (string) file_get_contents(self::$log));
    }

    /**
     * @param array<string, mixed> $body
     * @return array<string, mixed>
     */
    private function deliver(array $body): array
    {
        $response = (new Client(['http_errors' => false]))->post(Mock::url().'/__mock/webhooks', ['json' => $body]);
        $decoded = json_decode((string) $response->getBody(), true);
        self::assertIsArray($decoded, (string) $response->getBody());
        $answer = $decoded['data'] ?? $decoded;
        self::assertIsArray($answer);
        $status = $answer['status'] ?? $answer['receiver_status'] ?? ($answer['response']['status'] ?? null);

        return ['status' => $status] + $answer;
    }
}
