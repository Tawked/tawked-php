<?php

declare(strict_types=1);

namespace Tawked\Tests\Unit;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use Tawked\Exception\TawkedException;
use Tawked\Exception\WebhookSignatureException;
use Tawked\Model\Webhook\ApplicationEventData;
use Tawked\Model\Webhook\BalanceLowData;
use Tawked\Model\Webhook\MessageEventData;
use Tawked\Model\Webhook\VerificationEventData;
use Tawked\Tests\TestCase;
use Tawked\Webhook;

final class WebhookTest extends TestCase
{
    private const SECRET = 'Zm9vYmFyLXRhd2tlZC10ZXN0LXNlY3JldC0wMDAx';

    private const TIMESTAMP = '1788265862201';

    /**
     * The contract's worked examples, computed by the API's own WebhookSigner.
     *
     * @return iterable<string, array{string, string, string, string}>
     */
    public static function workedExamples(): iterable
    {
        $webhooks = self::contract()['webhooks'];
        self::assertIsArray($webhooks);
        foreach ($webhooks['schemes'][0]['worked_examples'] as $example) {
            yield $example['label'] => [$example['secret'], $example['timestamp'], $example['body'], $example['signature']];
        }
    }

    public function test_the_contract_has_the_three_tawked_worked_examples(): void
    {
        self::assertCount(3, iterator_to_array(self::workedExamples()));
    }

    #[DataProvider('workedExamples')]
    public function test_sign_reproduces_the_worked_example(string $secret, string $timestamp, string $body, string $signature): void
    {
        self::assertSame($signature, Webhook::sign($body, $secret, $timestamp));
        self::assertSame($signature, Webhook::signLegacy($secret, $timestamp, $body));
    }

    #[DataProvider('workedExamples')]
    public function test_construct_event_accepts_the_worked_example(string $secret, string $timestamp, string $body, string $signature): void
    {
        $event = Webhook::constructEvent($body, [
            'tawked-timestamp' => $timestamp,
            'tawked-signature' => $signature,
        ], $secret, 300, (int) $timestamp + 1000);

        $decoded = json_decode($body, true);
        self::assertIsArray($decoded);
        self::assertSame($decoded['event'], $event->event);
        self::assertSame($decoded['data'], $event->data);
        self::assertSame($decoded, $event->toArray());
        self::assertSame($timestamp, $event->timestamp);
        self::assertFalse($event->test);
        self::assertSame('2026-09-01T12:31:02.201+00:00', $event->sentAt?->format('Y-m-d\TH:i:s.vP'));
    }

    public function test_the_verified_example_is_typed(): void
    {
        [$secret, $timestamp, $body, $signature] = iterator_to_array(self::workedExamples(), false)[0];

        $data = Webhook::constructEvent($body, ['tawked-timestamp' => $timestamp, 'tawked-signature' => $signature], $secret, null)->typedData();

        self::assertInstanceOf(VerificationEventData::class, $data);
        self::assertSame('verified', $data->status);
        self::assertSame('order-42', $data->reference);
        self::assertSame(1, $data->attempts);
        self::assertNull($data->externalId);
    }

    public function test_the_arabic_example_reads_its_reason(): void
    {
        [$secret, $timestamp, $body, $signature] = iterator_to_array(self::workedExamples(), false)[2];

        $data = Webhook::constructEvent($body, ['tawked-timestamp' => $timestamp, 'tawked-signature' => $signature], $secret, null)->typedData();

        self::assertInstanceOf(ApplicationEventData::class, $data);
        self::assertSame('متجر/88', $data->externalId);
        self::assertSame('الاسم يدّعي علامة محمية.', $data->reason);
        self::assertSame('rejected', $data->status);
    }

    public function test_message_and_balance_events_are_typed_and_unknown_ones_are_not(): void
    {
        $message = $this->event(['event' => 'message.failed', 'data' => ['id' => 'm1', 'status' => 'failed', 'application_id' => 'a', 'to' => '+9665•• ••• 000', 'template' => 't', 'lang' => 'ar', 'reference' => null, 'price_halalas' => 0, 'error' => ['code' => '131026', 'message' => 'x'], 'occurred_at' => '2026-09-06T09:00:03.000Z', 'created_at' => '2026-09-06T09:00:00.000Z'], 'sent_at' => self::TIMESTAMP]);
        $balance = $this->event(['event' => 'balance.low', 'data' => ['balance_halalas' => 4200, 'threshold_halalas' => 10000], 'sent_at' => self::TIMESTAMP]);
        $unknown = $this->event(['event' => 'something.new', 'data' => [], 'test' => true, 'sent_at' => self::TIMESTAMP]);

        $messageData = $message->typedData();
        self::assertInstanceOf(MessageEventData::class, $messageData);
        self::assertSame('131026', $messageData->error?->code);
        $balanceData = $balance->typedData();
        self::assertInstanceOf(BalanceLowData::class, $balanceData);
        self::assertSame(4200, $balanceData->balanceHalalas);
        self::assertNull($unknown->typedData());
        self::assertTrue($unknown->test);
    }

    public function test_a_wrong_secret_or_a_changed_body_is_refused(): void
    {
        [$secret, $timestamp, $body, $signature] = iterator_to_array(self::workedExamples(), false)[0];

        foreach ([
            [$body, $signature, $timestamp, 'another-secret'],
            [str_replace('order-42', 'order-43', $body), $signature, $timestamp, $secret],
            [$body, $signature, (string) ((int) $timestamp + 1), $secret],
            [$body, str_repeat('0', 64), $timestamp, $secret],
        ] as $i => [$payload, $sig, $ts, $key]) {
            try {
                self::check($payload, $sig, $ts, $key, null);
                self::fail("Case {$i} was accepted.");
            } catch (WebhookSignatureException $e) {
                self::assertInstanceOf(TawkedException::class, $e);
            }
        }
    }

    public function test_re_encoded_json_is_not_the_body_as_received(): void
    {
        [$secret, $timestamp, $body, $signature] = iterator_to_array(self::workedExamples(), false)[2];
        self::check($body, $signature, $timestamp, $secret, null);

        $this->expectException(WebhookSignatureException::class);
        self::check((string) json_encode(json_decode($body)), $signature, $timestamp, $secret, null);
    }

    public function test_a_timestamp_outside_the_tolerance_is_refused(): void
    {
        [$secret, $timestamp, $body, $signature] = iterator_to_array(self::workedExamples(), false)[0];
        $ts = (int) $timestamp;

        self::check($body, $signature, $timestamp, $secret, 300, $ts + 299_000);
        self::check($body, $signature, $timestamp, $secret, 300, $ts - 299_000);
        self::check($body, $signature, $timestamp, $secret, null, $ts + 999_999_000);

        foreach ([$ts + 301_000, $ts - 301_000] as $now) {
            try {
                self::check($body, $signature, $timestamp, $secret, 300, $now);
                self::fail('An old delivery was accepted.');
            } catch (WebhookSignatureException $e) {
                self::assertSame('timestamp_out_of_tolerance', $e->reason());
            }
        }

        $this->expectException(WebhookSignatureException::class);
        self::check($body, $signature, $timestamp, $secret); // the real clock: 2026-09-01 is long gone
    }

    public function test_missing_headers_are_refused(): void
    {
        foreach ([['tawked-signature' => 'abc'], ['tawked-timestamp' => self::TIMESTAMP], ['tawked-timestamp' => 'yesterday', 'tawked-signature' => 'abc']] as $headers) {
            try {
                Webhook::constructEvent('{}', $headers, self::SECRET, null);
                self::fail('Accepted without the headers.');
            } catch (WebhookSignatureException) {
                $this->addToAssertionCount(1);
            }
        }
    }

    public function test_headers_are_read_in_any_case_as_arrays_or_from_server_variables(): void
    {
        [$secret, $timestamp, $body, $signature] = iterator_to_array(self::workedExamples(), false)[0];

        $fromArrays = Webhook::constructEvent($body, ['Tawked-Timestamp' => [$timestamp], 'TAWKED-SIGNATURE' => [strtoupper($signature)]], $secret, null);
        $fromServer = Webhook::constructEvent($body, ['HTTP_TAWKED_TIMESTAMP' => $timestamp, 'HTTP_TAWKED_SIGNATURE' => $signature], $secret, null);

        self::assertSame('verification.verified', $fromArrays->event);
        self::assertSame('verification.verified', $fromServer->event);
    }

    public function test_a_rotating_secret_accepts_either(): void
    {
        [$secret, $timestamp, $body, $signature] = iterator_to_array(self::workedExamples(), false)[0];

        self::check($body, $signature, $timestamp, ['the-new-secret', $secret], null);

        $this->expectException(WebhookSignatureException::class);
        self::check($body, $signature, $timestamp, ['the-new-secret', ''], null);
    }

    public function test_a_psr7_request_is_verified_and_its_body_left_readable(): void
    {
        [$secret, $timestamp, $body, $signature] = iterator_to_array(self::workedExamples(), false)[1];
        $request = new ServerRequest('POST', '/tawked/webhook', ['tawked-timestamp' => $timestamp, 'tawked-signature' => $signature, 'tawked-event' => 'service.approved'], $body);

        $event = Webhook::constructEventFromRequest($request, $secret, 300, (int) $timestamp);

        self::assertSame('service.approved', $event->event);
        self::assertSame($body, (string) $request->getBody());
    }

    public function test_a_signed_body_that_is_not_an_envelope_is_refused(): void
    {
        foreach (['not json', '[1,2]', '{"event":"x"}'] as $body) {
            try {
                Webhook::constructEvent($body, ['tawked-timestamp' => self::TIMESTAMP, 'tawked-signature' => Webhook::sign($body, self::SECRET, self::TIMESTAMP)], self::SECRET, null);
                self::fail("Accepted {$body}.");
            } catch (WebhookSignatureException) {
                $this->addToAssertionCount(1);
            }
        }
    }

    /**
     * The earlier verify() arguments, through assertValid() with the headers they name.
     *
     * @param string|list<string> $secret
     */
    private static function check(string $payload, string $signature, string $timestamp, string|array $secret, ?int $tolerance = Webhook::DEFAULT_TOLERANCE, ?int $now = null): void
    {
        $options = ['tolerance' => $tolerance];
        if ($now !== null) {
            $options['now'] = $now;
        }
        Webhook::assertValid($payload, ['tawked-timestamp' => $timestamp, 'tawked-signature' => $signature], $secret, $options);
    }

    /**
     * @param array<string, mixed> $envelope
     */
    private function event(array $envelope): \Tawked\Model\Webhook\WebhookEvent
    {
        $body = (string) json_encode($envelope, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return Webhook::constructEvent($body, [
            'tawked-timestamp' => self::TIMESTAMP,
            'tawked-signature' => Webhook::sign($body, self::SECRET, self::TIMESTAMP),
        ], self::SECRET, null);
    }
}
