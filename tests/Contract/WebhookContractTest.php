<?php

declare(strict_types=1);

namespace Tawked\Tests\Contract;

use PHPUnit\Framework\Attributes\DataProvider;
use Tawked\Exception\WebhookSignatureException;
use Tawked\Model\Model;
use Tawked\Model\Webhook\UnknownEvent;
use Tawked\Model\Webhook\WebhookEvent;
use Tawked\Tests\TestCase;
use Tawked\Webhook;
use Tawked\Webhook\Events;

/**
 * From the contract (tests/fixtures/api.json): every worked example of both schemes and
 * the Standard Webhooks published vector, every verification case of `webhooks.verification`
 * (cross-scheme, rotation, units, tolerance, both header sets), and every event typed.
 */
final class WebhookContractTest extends TestCase
{
    /**
     * @return array<string, mixed>
     */
    private static function webhooks(): array
    {
        $webhooks = self::contract()['webhooks'];
        self::assertIsArray($webhooks);

        /** @var array<string, mixed> $webhooks */
        return $webhooks;
    }

    /**
     * @return iterable<string, array{string, array<string, mixed>}>
     */
    public static function workedExamples(): iterable
    {
        $schemes = self::webhooks()['schemes'];
        self::assertIsArray($schemes);
        foreach ($schemes as $scheme) {
            self::assertIsArray($scheme);
            foreach ((array) $scheme['worked_examples'] as $i => $example) {
                self::assertIsArray($example);
                yield $scheme['name'].' #'.$i.' '.substr((string) $example['label'], 0, 40) => [(string) $scheme['name'], $example];
            }
        }
    }

    /**
     * @param array<string, mixed> $example
     */
    #[DataProvider('workedExamples')]
    public function test_sign_reproduces_every_worked_example_and_verify_accepts_it(string $scheme, array $example): void
    {
        $body = (string) $example['body'];
        $secret = (string) $example['secret'];
        $timestamp = (string) $example['timestamp'];
        if ($scheme === 'tawked') {
            self::assertSame($example['signature'], Webhook::sign($body, $secret, $timestamp));
            self::assertSame($example['signature'], Webhook::signLegacy($secret, $timestamp, $body));
            $headers = ['tawked-timestamp' => $timestamp, 'tawked-signature' => (string) $example['signature']];
            $now = (int) $timestamp;
        } else {
            $id = (string) $example['id'];
            self::assertSame($example['signature'], Webhook::sign($body, $secret, $timestamp, ['id' => $id]));
            $headers = ['webhook-id' => $id, 'webhook-timestamp' => $timestamp, 'webhook-signature' => (string) $example['signature']];
            $now = (int) $timestamp * 1000;
        }
        self::assertTrue(Webhook::verify($body, $headers, $secret, ['now' => $now]));
        // One byte changed fails.
        self::assertFalse(Webhook::verify($body.' ', $headers, $secret, ['now' => $now]));
        // Out of tolerance fails.
        self::assertFalse(Webhook::verify($body, $headers, $secret, ['now' => $now + 301_000]));
        self::assertTrue(Webhook::verify($body, $headers, $secret, ['now' => $now + 301_000, 'tolerance' => null]));
    }

    public function test_the_standard_webhooks_published_vector_is_among_the_examples(): void
    {
        $found = false;
        foreach (self::workedExamples() as [$scheme, $example]) {
            if ($scheme === 'standard_webhooks' && $example['signature'] === 'v1,g0hM9SsE+OTPJTGt/tmIKtSyZlE3uFJELVlNIOLJ1OE=') {
                $found = true;
            }
        }
        self::assertTrue($found);
    }

    /**
     * @return array{body: string, headers: array<string, string>, now_seconds: int, cases: list<array{label: string, secrets: list<string>, expected: array{valid: bool, scheme: string}}>}
     */
    private static function bothHeaders(): array
    {
        /** @var array{body: string, headers: array<string, string>, now_seconds: int, cases: list<array{label: string, secrets: list<string>, expected: array{valid: bool, scheme: string}}>} $vector */
        $vector = json_decode((string) file_get_contents(__DIR__.'/../fixtures/both-headers.json'), true, 512, JSON_THROW_ON_ERROR);

        return $vector;
    }

    /**
     * @return iterable<string, array{array{label: string, secrets: list<string>, expected: array{valid: bool, scheme: string}}}>
     */
    public static function bothHeadersCases(): iterable
    {
        foreach (self::bothHeaders()['cases'] as $case) {
            yield $case['label'] => [$case];
        }
    }

    /**
     * one real delivery carrying both header sets. The scheme is chosen by the
     * secrets given, never by the headers present, and never falls back from one scheme to the other.
     *
     * @param array{label: string, secrets: list<string>, expected: array{valid: bool, scheme: string}} $case
     */
    #[DataProvider('bothHeadersCases')]
    public function test_a_delivery_with_both_header_sets_is_verified_by_the_scheme_the_secrets_choose(array $case): void
    {
        $vector = self::bothHeaders();
        $options = ['now' => $vector['now_seconds'] * 1000];
        $expected = $case['expected'];

        self::assertSame($expected['valid'], Webhook::verify($vector['body'], $vector['headers'], $case['secrets'], $options));
        self::assertSame($expected['valid'], Webhook::verify($vector['body'], $vector['headers'], $case['secrets'], $options + ['scheme' => $expected['scheme']]));
        // The chosen scheme alone decides: the other scheme's headers taken away, the answer does not change.
        $own = array_filter($vector['headers'], static fn(string $name): bool => $expected['scheme'] === 'tawked' ? ! str_starts_with($name, 'webhook-') : $name !== 'tawked-signature', ARRAY_FILTER_USE_KEY);
        self::assertSame($expected['valid'], Webhook::verify($vector['body'], $own, $case['secrets'], $options));
        try {
            $event = Webhook::constructEvent($vector['body'], $vector['headers'], $case['secrets'], $options);
            self::assertTrue($expected['valid']);
            self::assertSame('email.delivered', $event->event);
            self::assertTrue($event->isKnown());
        } catch (WebhookSignatureException $e) {
            self::assertFalse($expected['valid']);
            self::assertSame('signature_mismatch', $e->reason());
        }
    }

    /**
     * @return iterable<string, array{array<string, mixed>}>
     */
    public static function verificationCases(): iterable
    {
        $verification = self::webhooks()['verification'];
        self::assertIsArray($verification);
        foreach ((array) $verification['cases'] as $case) {
            self::assertIsArray($case);
            yield (string) $case['label'] => [$case];
        }
    }

    /**
     * @param array<string, mixed> $case
     */
    #[DataProvider('verificationCases')]
    public function test_every_verification_case_of_the_contract(array $case): void
    {
        /** @var array<string, string> $headers */
        $headers = $case['headers'];
        /** @var list<string> $secrets */
        $secrets = $case['secrets'];
        $options = ['now' => (int) $case['now_ms']];

        self::assertSame($case['valid'], Webhook::verify((string) $case['body'], $headers, $secrets, $options));
        try {
            Webhook::assertValid((string) $case['body'], $headers, $secrets, $options);
            self::assertTrue($case['valid']);
        } catch (WebhookSignatureException $e) {
            self::assertFalse($case['valid']);
            self::assertSame($case['reason'], $e->reason());
        }
        // Headers are read in any case, and from $_SERVER's HTTP_ names.
        $server = [];
        foreach ($headers as $name => $value) {
            $server['HTTP_'.strtoupper(str_replace('-', '_', $name))] = $value;
        }
        self::assertSame($case['valid'], Webhook::verify((string) $case['body'], $server, $secrets, $options));
    }

    public function test_a_whsec_secret_is_never_tried_on_the_tawked_scheme(): void
    {
        $secret = 'whsec_AAECAwQFBgcICQoLDA0ODxAREhMUFRYXGBkaGxwdHh8=';
        $timestamp = '1788265862201';
        $body = '{"event":"balance.low","data":{"balance_halalas":1,"threshold_halalas":2},"sent_at":"1788265862201"}';
        $forged = hash_hmac('sha256', $timestamp.'.'.$body, $secret);
        $headers = ['tawked-timestamp' => $timestamp, 'tawked-signature' => $forged];

        self::assertFalse(Webhook::verify($body, $headers, $secret, ['now' => (int) $timestamp]));
        $this->expectException(\Tawked\Exception\InvalidArgumentException::class);
        Webhook::sign($body, $secret, $timestamp, ['scheme' => 'tawked']);
    }

    public function test_test_headers_verify_in_both_schemes(): void
    {
        $body = '{"event":"message.read","data":{"id":"x"},"sent_at":"1"}';
        foreach (['tawked-test-secret-0000000000000001', 'whsec_AAECAwQFBgcICQoLDA0ODxAREhMUFRYXGBkaGxwdHh8='] as $secret) {
            self::assertTrue(Webhook::verify($body, Webhook::testHeaders($body, $secret), $secret));
        }
    }

    /**
     * Every event of the contract, application and partner, with its example data.
     *
     * @return iterable<string, array{string, array<string, mixed>}>
     */
    public static function events(): iterable
    {
        $webhooks = self::webhooks();
        foreach ((array) $webhooks['application']['examples'] as $example) {
            self::assertIsArray($example);
            yield 'application '.$example['event'] => [(string) $example['event'], (array) $example['data']];
        }
        foreach ((array) $webhooks['partner']['events'] as $event => $spec) {
            self::assertIsArray($spec);
            yield 'partner '.$event => [(string) $event, (array) ($spec['example'] ?? [])];
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    #[DataProvider('events')]
    public function test_construct_event_types_every_event_of_the_contract(string $event, array $data): void
    {
        $secret = 'tawked-test-secret-0000000000000001';
        $body = json_encode(['event' => $event, 'data' => $data, 'sent_at' => '1788265862201'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        $headers = Webhook::testHeaders($body, $secret, ['timestamp' => '1788265862201']);

        $parsed = Webhook::constructEvent($body, $headers, $secret, ['now' => 1788265862201]);

        self::assertNotInstanceOf(UnknownEvent::class, $parsed);
        self::assertTrue($parsed->isKnown());
        self::assertSame($event, $parsed->event);
        self::assertArrayHasKey($event, Events::DATA);
        $typed = $parsed->typedData();
        self::assertInstanceOf(Model::class, $typed);
        self::assertContains($typed::class, Events::DATA[$event]);
        self::assertSame($data, $typed->toArray());
    }

    public function test_every_event_name_of_the_contract_has_a_data_class(): void
    {
        $envelope = self::webhooks()['envelope'];
        self::assertIsArray($envelope);
        foreach ((array) $envelope['schema']['properties']['event']['enum'] as $event) {
            self::assertArrayHasKey((string) $event, Events::DATA, "{$event} has no data class");
        }
    }

    public function test_an_unknown_event_is_an_unknown_event_never_an_exception(): void
    {
        $secret = 'tawked-test-secret-0000000000000001';
        // A name no contract has (domain.verified became a typed event, typed by the test above).
        $body = '{"event":"domain.teleported","data":{"id":"d1","name":"acme.sa"},"sent_at":"1788265862201"}';
        $event = Webhook::constructEvent($body, Webhook::testHeaders($body, $secret, ['timestamp' => '1788265862201']), $secret, ['now' => 1788265862201]);

        self::assertInstanceOf(UnknownEvent::class, $event);
        self::assertInstanceOf(WebhookEvent::class, $event);
        self::assertFalse($event->isKnown());
        self::assertNull($event->typedData());
        self::assertSame(['id' => 'd1', 'name' => 'acme.sa'], $event->data);
    }

    public function test_a_signed_body_that_is_not_json_is_malformed_body(): void
    {
        $secret = 'tawked-test-secret-0000000000000001';
        $body = 'not json';
        try {
            Webhook::constructEvent($body, Webhook::testHeaders($body, $secret), $secret);
            self::fail('A body that is not JSON must be refused.');
        } catch (WebhookSignatureException $e) {
            self::assertSame('malformed_body', $e->reason());
        }
    }

    public function test_a_test_keys_simulated_email_received_is_known_by_its_envelopes_mode(): void
    {
        $application = self::webhooks()['application'];
        self::assertIsArray($application);
        $example = $application['test_mode_example'];
        self::assertIsArray($example);
        $secret = 'tawked-test-secret-0000000000000001';
        $body = (string) json_encode($example, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $event = Webhook::constructEvent($body, Webhook::testHeaders($body, $secret, ['timestamp' => (string) $example['sent_at']]), $secret, ['now' => (int) $example['sent_at']]);

        self::assertNotInstanceOf(UnknownEvent::class, $event);
        self::assertSame('email.received', $event->event);
        self::assertSame('test', $event->mode);
        self::assertTrue($event->isTestMode());
        self::assertSame('email.received', $event->type);
        self::assertSame($example['timestamp'], $event->occurredAt);
        self::assertSame((string) $example['sent_at'], $event->timestamp);
        self::assertArrayNotHasKey('mode', $event->data);
        self::assertFalse($event->test);
    }

    public function test_a_live_event_is_not_test_mode(): void
    {
        $secret = 'tawked-test-secret-0000000000000001';
        $body = '{"event":"email.delivered","data":{"id":"m1","status":"delivered"},"type":"email.delivered","timestamp":"2026-10-20T10:00:00.000Z","sent_at":"1788265862201"}';
        $event = Webhook::constructEvent($body, Webhook::testHeaders($body, $secret, ['timestamp' => '1788265862201']), $secret, ['now' => 1788265862201]);

        self::assertNull($event->mode);
        self::assertFalse($event->isTestMode());
    }

    public function test_a_standard_webhooks_envelope_reads_its_type(): void
    {
        $secret = 'whsec_AAECAwQFBgcICQoLDA0ODxAREhMUFRYXGBkaGxwdHh8=';
        $body = '{"type":"email.delivered","timestamp":"2026-10-20T10:00:00Z","data":{"id":"m1","status":"delivered"}}';
        $event = Webhook::constructEvent($body, Webhook::testHeaders($body, $secret, ['timestamp' => '1788265862']), $secret, ['now' => 1788265862000]);

        self::assertSame('email.delivered', $event->event);
        self::assertSame('1788265862000', $event->timestamp);
    }
}
