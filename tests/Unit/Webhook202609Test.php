<?php

declare(strict_types=1);

namespace Tawked\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use Tawked\Model\Webhook\UnknownEvent;
use Tawked\Tests\TestCase;
use Tawked\Webhook;

/**
 * Deliveries exactly as the API's webhook sender of version 2026-09 builds them (User-Agent
 * Tawked-Webhooks/2): the envelope {event, data} with sent_at (and "test": true on a test event),
 * signed with tawked-signature alone. No tawked-webhook-id, no Standard Webhooks headers, no type,
 * timestamp or mode in the body. Every release must keep accepting them.
 */
final class Webhook202609Test extends TestCase
{
    /** @return array{secret: string, deliveries: list<array{label: string, event: string, test: bool, now_ms: int, body: string, headers: array<string, string>}>} */
    private static function fixture(): array
    {
        /** @var array{secret: string, deliveries: list<array{label: string, event: string, test: bool, now_ms: int, body: string, headers: array<string, string>}>} */
        return json_decode((string) file_get_contents(__DIR__.'/../fixtures/webhooks-2026-09.json'), true, 512, JSON_THROW_ON_ERROR);
    }

    /** @return iterable<string, array{array{label: string, event: string, test: bool, now_ms: int, body: string, headers: array<string, string>}}> */
    public static function deliveries(): iterable
    {
        foreach (self::fixture()['deliveries'] as $i => $delivery) {
            yield $i.' '.$delivery['label'] => [$delivery];
        }
    }

    /** @param array{label: string, event: string, test: bool, now_ms: int, body: string, headers: array<string, string>} $delivery */
    #[DataProvider('deliveries')]
    public function test_a_delivery_verifies_and_is_typed(array $delivery): void
    {
        $secret = self::fixture()['secret'];
        $options = ['now' => $delivery['now_ms']];
        $lowercase = array_change_key_case($delivery['headers'], CASE_LOWER);

        self::assertTrue(Webhook::verify($delivery['body'], $delivery['headers'], $secret, $options));
        self::assertTrue(Webhook::verify($delivery['body'], $lowercase, ['a-new-secret', $secret], $options));

        $event = Webhook::constructEvent($delivery['body'], $lowercase, $secret, $options);
        $sent = json_decode($delivery['body'], true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($sent);
        self::assertNotInstanceOf(UnknownEvent::class, $event);
        self::assertTrue($event->isKnown());
        self::assertSame($delivery['event'], $event->event);
        self::assertSame($sent['data'], $event->data);
        self::assertSame($delivery['test'], $event->test);
        self::assertSame($delivery['headers']['tawked-timestamp'], $event->timestamp);
        self::assertNotNull($event->typedData());

        $changed = str_replace('"data":{', '"data":{"x":1,', $delivery['body']);
        self::assertFalse(Webhook::verify($changed, $lowercase, $secret, $options));
        self::assertFalse(Webhook::verify($delivery['body'], $lowercase, 'another-secret', $options));
    }
}
