<?php

declare(strict_types=1);

namespace Tawked\Tests\Unit;

use DateTimeImmutable;
use GuzzleHttp\Psr7\Response;
use Tawked\Exception\InvalidArgumentException;
use Tawked\Model\Partner\Application;
use Tawked\Model\WhatsApp\Template;
use Tawked\Model\WhatsApp\TemplateSummary;
use Tawked\Tests\TestCase;

/**
 * What each method puts on the wire and the typed values it reads back.
 */
final class RequestsTest extends TestCase
{
    private const ID = 'b7e5c2b0-9c1a-4e2f-8f2a-3a6b0e9d1c44';

    public function test_verify_start_sends_the_params_as_json_and_types_the_answer(): void
    {
        $tawked = $this->client([self::json(201, ['id' => self::ID, 'status' => 'pending', 'expires_at' => '2026-09-01T12:34:56.789Z', 'reference' => 'order-42'], ['Idempotent-Replayed' => 'true'])]);

        $started = $tawked->verify->start(
            ['to' => '0551234567', 'lang' => 'en', 'reference' => 'order-42', 'client_ip' => '203.0.113.7', 'autofill' => 'web'],
            ['idempotency_key' => '  signin-7781  '],
        );

        self::assertSame(['to' => '0551234567', 'lang' => 'en', 'reference' => 'order-42', 'client_ip' => '203.0.113.7', 'autofill' => 'web'], $this->sentJson());
        self::assertSame('signin-7781', $this->request()->getHeaderLine('Idempotency-Key'));
        self::assertSame(self::ID, $started->id);
        self::assertSame('pending', $started->status);
        self::assertSame('order-42', $started->reference);
        self::assertEquals(new DateTimeImmutable('2026-09-01T12:34:56.789Z'), $started->expiresAt);
        self::assertSame(201, $started->meta()?->statusCode);
        self::assertTrue($started->meta()->idempotentReplayed);
        self::assertSame($started->meta()->requestId, $started->requestId());
    }

    public function test_verify_check_reads_a_wrong_code_as_a_result_not_an_error(): void
    {
        $tawked = $this->client([self::json(200, ['verified' => false, 'status' => 'invalid_code', 'attempts_remaining' => 2])]);

        $result = $tawked->verify->check(['id' => self::ID, 'code' => '000000']);

        self::assertSame(['id' => self::ID, 'code' => '000000'], $this->sentJson());
        self::assertFalse($result->verified);
        self::assertSame('invalid_code', $result->status);
        self::assertSame(2, $result->attemptsRemaining);
    }

    public function test_verify_check_leaves_attempts_remaining_null_when_the_api_omits_it(): void
    {
        $tawked = $this->client([self::json(200, ['verified' => true, 'status' => 'verified'])]);

        $result = $tawked->verify->check(['id' => self::ID, 'code' => '123456']);

        self::assertTrue($result->verified);
        self::assertNull($result->attemptsRemaining);
    }

    public function test_a_partner_key_names_the_application_in_the_body_or_in_the_query_on_a_get(): void
    {
        $verification = ['id' => self::ID, 'status' => 'pending', 'channel' => 'sms', 'to' => '+966551234567', 'reference' => null, 'attempts_remaining' => 3, 'resends_remaining' => 3, 'expires_at' => '2026-09-01T12:34:56.789Z', 'verified_at' => null, 'created_at' => '2026-09-01T12:29:56.789Z'];
        $tawked = $this->client([
            self::json(201, ['id' => self::ID, 'status' => 'pending', 'expires_at' => '2026-09-01T12:34:56.789Z']),
            self::json(200, $verification),
            self::json(200, ['id' => self::ID, 'status' => 'pending', 'expires_at' => '2026-09-01T12:39:56.789Z', 'resends_remaining' => 2]),
            self::json(200, ['id' => self::ID, 'status' => 'canceled']),
        ], [], self::PARTNER_KEY);

        $tawked->verify->start(['to' => '+966551234567', 'application' => 'store_88']);
        $read = $tawked->verify->get(self::ID, ['application' => 'store_88']);
        $resent = $tawked->verify->resend(self::ID, ['application' => 'store_88']);
        $canceled = $tawked->verify->cancel(self::ID, ['application' => 'store_88']);

        self::assertSame('store_88', $this->sentJson(0)['application']);
        self::assertSame(['application' => 'store_88'], $this->sentQuery(1));
        self::assertSame('', (string) $this->request(1)->getBody());
        self::assertSame(['application' => 'store_88'], $this->sentJson(2));
        self::assertSame(['application' => 'store_88'], $this->sentJson(3));
        self::assertNull($read->verifiedAt);
        self::assertSame(3, $read->attemptsRemaining);
        self::assertSame(2, $resent->resendsRemaining);
        self::assertSame('canceled', $canceled->status);
    }

    public function test_resend_and_cancel_send_no_body_with_an_application_key(): void
    {
        $tawked = $this->client([self::json(200, ['id' => self::ID, 'status' => 'canceled'])]);

        $tawked->verify->cancel(self::ID);

        self::assertSame('', (string) $this->request()->getBody());
        self::assertFalse($this->request()->hasHeader('Content-Type'));
    }

    public function test_path_ids_are_encoded_and_an_empty_one_is_refused_before_sending(): void
    {
        $tawked = $this->client([self::json(200, ['external_id' => 'store/88 ar', 'events' => []])], [], self::PARTNER_KEY);

        $tawked->partner->applications->history('store/88 ar');
        self::assertSame('/v1/partner/applications/store%2F88%20ar/history', $this->request()->getUri()->getPath());

        $this->expectException(InvalidArgumentException::class);
        $tawked->verify->get('  ');
    }

    public function test_whatsapp_send_keeps_arabic_and_slashes_unescaped_and_reads_the_offset_dates(): void
    {
        $tawked = $this->client([self::json(202, ['id' => 'm1', 'status' => 'accepted', 'to' => '+966550000000', 'template' => 'order_shipped', 'lang' => 'ar', 'reference' => null, 'price_halalas' => 9, 'created_at' => '2026-09-06T12:00:00+03:00'])]);

        $sent = $tawked->whatsapp->messages->send([
            'to' => '+966550000000',
            'template' => 'order_shipped',
            'params' => ['name' => 'سارة'],
            'header' => ['image' => ['link' => 'https://cdn.example/a/b.png']],
            'buttons' => [['index' => 0, 'parameter' => 'track/1042']],
        ]);

        $body = (string) $this->request()->getBody();
        self::assertStringContainsString('"name":"سارة"', $body);
        self::assertStringContainsString('https://cdn.example/a/b.png', $body);
        self::assertSame([['index' => 0, 'parameter' => 'track/1042']], $this->sentJson()['buttons']);
        self::assertSame(9, $sent->priceHalalas);
        self::assertSame('+03:00', $sent->createdAt?->format('P'));
        self::assertSame(202, $sent->meta()?->statusCode);
    }

    public function test_whatsapp_message_reads_a_failure(): void
    {
        $tawked = $this->client([self::json(200, ['id' => 'm1', 'status' => 'failed', 'to' => '+966550000000', 'template' => 't', 'lang' => 'ar', 'reference' => null, 'price_halalas' => 0, 'error' => ['code' => '131026', 'message' => 'Message undeliverable'], 'created_at' => '2026-09-06T12:00:00+03:00', 'sent_at' => null, 'delivered_at' => null, 'read_at' => null, 'failed_at' => '2026-09-06T12:00:03+03:00'])]);

        $message = $tawked->whatsapp->messages->get('m1', ['application' => 'store_88']);

        self::assertSame(['application' => 'store_88'], $this->sentQuery());
        self::assertSame('131026', $message->error?->code);
        self::assertSame('Message undeliverable', $message->error->message);
        self::assertNull($message->deliveredAt);
        self::assertNotNull($message->failedAt);
    }

    public function test_templates_are_typed_down_to_their_buttons(): void
    {
        $tawked = $this->client([self::json(200, ['data' => [['id' => '7000', 'name' => 'order_received_v1', 'language' => 'ar', 'category' => 'UTILITY', 'status' => 'APPROVED', 'placeholders' => ['kind' => 'named', 'header' => [], 'body' => ['name', 'order'], 'buttons' => [['index' => 0, 'type' => 'url']]], 'managed' => true]]])]);

        $templates = $tawked->whatsapp->templates->list();

        self::assertCount(1, $templates->data);
        self::assertInstanceOf(TemplateSummary::class, $templates->data[0]);
        self::assertSame('7000', $templates->data[0]->id);
        self::assertTrue($templates->data[0]->managed);
        self::assertSame(['name', 'order'], $templates->data[0]->placeholders->body);
        self::assertSame('url', $templates->data[0]->placeholders->buttons[0]->type);
    }

    public function test_a_template_is_read_in_full_by_its_id(): void
    {
        /** @var array<string, mixed> $body */
        $body = self::endpoints()['whatsapp-template']['examples'][0]['body'];
        $tawked = $this->client([self::json(200, $body)]);

        $template = $tawked->whatsapp->templates->get('7000');

        self::assertSame('GET', $this->request()->getMethod());
        self::assertSame('/v1/whatsapp/templates/7000', $this->request()->getUri()->getPath());
        self::assertSame('', $this->request()->getUri()->getQuery());
        self::assertSame('', (string) $this->request()->getBody());
        self::assertFalse($this->request()->hasHeader('Idempotency-Key'));
        self::assertInstanceOf(Template::class, $template);
        self::assertSame('7000', $template->id);
        self::assertSame('APPROVED', $template->status);
        self::assertSame('GREEN', $template->quality);
        self::assertNull($template->rejectedReason);
        self::assertCount(3, $template->components);
        self::assertSame('HEADER', $template->components[0]->type);
        self::assertSame('TEXT', $template->components[0]->format);
        self::assertNull($template->components[1]->format);
        self::assertSame([], $template->components[1]->buttons);
        self::assertSame('URL', $template->components[2]->buttons[0]->type);
        self::assertSame('https://flowers.example/orders/{{1}}', $template->components[2]->buttons[0]->url);
        self::assertNull($template->components[2]->buttons[0]->phoneNumber);
        self::assertSame(['header:1' => '1042', 'body:1' => '1042', 'body:2' => '250', 'button:0' => '1042'], $template->examples);
        self::assertSame('positional', $template->placeholders->kind);
        self::assertSame(['1', '2'], $template->placeholders->body);
        self::assertFalse($template->managed);
        self::assertSame(200, $template->meta()?->statusCode);
    }

    public function test_a_template_with_no_placeholders_reads_examples_as_an_empty_array(): void
    {
        $raw = '{"id":"7001","name":"store_open_v1","language":"ar","category":"UTILITY","status":"APPROVED","quality":null,"rejected_reason":null,'
            .'"components":[{"type":"BODY","format":null,"text":"متجرنا مفتوح الآن.","buttons":[]}],"examples":{},'
            .'"placeholders":{"kind":"none","header":[],"body":[],"buttons":[]},"managed":true}';
        $tawked = $this->client([new Response(200, ['Content-Type' => 'application/json'], $raw)]);

        $template = $tawked->whatsapp->templates->get('7001');

        self::assertSame([], $template->examples);
        self::assertNull($template->quality);
        self::assertSame('none', $template->placeholders->kind);
        self::assertTrue($template->managed);
        self::assertSame([], $template->toArray()['examples']);
    }

    public function test_a_template_is_created_from_its_text_under_an_idempotency_key(): void
    {
        $tawked = $this->client([self::json(201, ['id' => '7100', 'name' => 'order_shipped_v1', 'language' => 'ar', 'category' => 'UTILITY', 'status' => 'PENDING'])]);
        $params = [
            'name' => 'order_shipped_v1',
            'language' => 'ar',
            'category' => 'UTILITY',
            'header' => null,
            'body' => 'طلبك رقم {{1}} في الطريق إليك.',
            'buttons' => [['type' => 'URL', 'text' => 'تتبع الطلب', 'url' => 'https://flowers.example/orders/{{1}}']],
            'examples' => ['body:1' => '1042', 'button:0' => '1042'],
        ];

        $created = $tawked->whatsapp->templates->create($params, ['idempotency_key' => 'template-order-shipped']);

        self::assertSame('POST', $this->request()->getMethod());
        self::assertSame('/v1/whatsapp/templates', $this->request()->getUri()->getPath());
        self::assertSame('application/json', $this->request()->getHeaderLine('Content-Type'));
        self::assertSame('template-order-shipped', $this->request()->getHeaderLine('Idempotency-Key'));
        self::assertSame($params, $this->sentJson());
        self::assertStringContainsString('"body":"طلبك رقم {{1}} في الطريق إليك."', (string) $this->request()->getBody());
        self::assertStringContainsString('"url":"https://flowers.example/orders/{{1}}"', (string) $this->request()->getBody());
        self::assertSame('7100', $created->id);
        self::assertSame('order_shipped_v1', $created->name);
        self::assertSame('ar', $created->language);
        self::assertSame('UTILITY', $created->category);
        self::assertSame('PENDING', $created->status);
        self::assertSame(201, $created->meta()?->statusCode);
    }

    public function test_a_template_write_mints_a_key_when_none_is_given(): void
    {
        $tawked = $this->client([self::json(201, ['id' => '7100', 'name' => 'a', 'language' => 'ar', 'category' => 'UTILITY', 'status' => 'PENDING'])]);

        $created = $tawked->whatsapp->templates->create(['name' => 'a', 'language' => 'ar', 'category' => 'UTILITY', 'body' => 'مرحبا']);

        self::assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $this->request()->getHeaderLine('Idempotency-Key'));
        self::assertSame($this->request()->getHeaderLine('Idempotency-Key'), $created->meta()?->idempotencyKey);
    }

    public function test_a_template_is_updated_with_its_whole_new_content(): void
    {
        /** @var array<string, mixed> $body */
        $body = self::endpoints()['whatsapp-template-update']['examples'][0]['body'];
        $tawked = $this->client([self::json(200, $body, ['Idempotent-Replayed' => 'true'])]);
        $params = ['category' => 'UTILITY', 'body' => 'استلمنا طلبك رقم {{1}} بقيمة {{2}} ريال، ونبلغك عند شحنه.', 'examples' => ['body:1' => '1042', 'body:2' => '250']];

        $template = $tawked->whatsapp->templates->update('7000', $params, ['idempotency_key' => 'template-7000-edit']);

        self::assertSame('PATCH', $this->request()->getMethod());
        self::assertSame('/v1/whatsapp/templates/7000', $this->request()->getUri()->getPath());
        self::assertSame('template-7000-edit', $this->request()->getHeaderLine('Idempotency-Key'));
        self::assertSame($params, $this->sentJson());
        self::assertInstanceOf(Template::class, $template);
        self::assertSame('PENDING', $template->status);
        self::assertNull($template->quality);
        self::assertTrue($template->meta()?->idempotentReplayed);
    }

    public function test_a_template_is_deleted_with_no_body(): void
    {
        $tawked = $this->client([self::json(200, ['id' => '7002', 'name' => 'promo_v1', 'language' => 'en', 'deleted' => true])]);

        $deleted = $tawked->whatsapp->templates->delete('7002', ['idempotency_key' => 'template-7002-delete']);

        self::assertSame('DELETE', $this->request()->getMethod());
        self::assertSame('/v1/whatsapp/templates/7002', $this->request()->getUri()->getPath());
        self::assertSame('', (string) $this->request()->getBody());
        self::assertFalse($this->request()->hasHeader('Content-Type'));
        self::assertSame('template-7002-delete', $this->request()->getHeaderLine('Idempotency-Key'));
        self::assertSame('7002', $deleted->id);
        self::assertSame('promo_v1', $deleted->name);
        self::assertSame('en', $deleted->language);
        self::assertTrue($deleted->deleted);
    }

    public function test_a_template_id_is_encoded_and_an_empty_one_is_refused_before_sending(): void
    {
        $tawked = $this->client([self::json(200, ['id' => '70/00'])]);
        $tawked->whatsapp->templates->get('70/00');
        self::assertSame('/v1/whatsapp/templates/70%2F00', $this->request()->getUri()->getPath());

        foreach ([
            fn() => $tawked->whatsapp->templates->get(' '),
            fn() => $tawked->whatsapp->templates->update('', ['category' => 'UTILITY', 'body' => 'مرحبا']),
            fn() => $tawked->whatsapp->templates->delete(''),
        ] as $call) {
            try {
                $call();
                self::fail('An empty template id was sent.');
            } catch (InvalidArgumentException $e) {
                self::assertStringContainsString('template_id', $e->getMessage());
            }
        }
        self::assertCount(1, $this->history);
    }

    public function test_provision_tells_created_from_existing(): void
    {
        $summary = self::endpoints()['partner-provision']['examples'][0]['body'];
        self::assertIsArray($summary);
        $tawked = $this->client([self::json(201, $summary), self::json(200, $summary)], [], self::PARTNER_KEY);

        $created = $tawked->partner->applications->create(['external_id' => 'store_88', 'name_ar' => 'متجر', 'name_en' => 'Shop', 'website' => 'https://shop.example', 'metadata' => ['plan' => 'pro']]);
        $existing = $tawked->partner->applications->create(['external_id' => 'store_88', 'name_ar' => 'متجر', 'name_en' => 'Shop', 'website' => 'https://shop.example']);

        self::assertTrue($created->wasCreated());
        self::assertFalse($existing->wasCreated());
        self::assertSame(['plan' => 'pro'], $this->sentJson(0)['metadata']);
        self::assertSame(6, $created->verify->codeLength);
        self::assertSame('clean', $created->screening?->result);
    }

    public function test_batch_takes_the_list_and_refuses_one_too_long_before_sending(): void
    {
        $answer = ['created' => 0, 'existing' => 0, 'refused' => 1, 'results' => [['index' => 0, 'external_id' => 'store_90', 'outcome' => 'refused', 'fields' => ['website'], 'application' => null]]];
        $tawked = $this->client([self::json(200, $answer)], [], self::PARTNER_KEY);

        $result = $tawked->partner->applications->batch([['external_id' => 'store_90']]);

        self::assertSame(['applications' => [['external_id' => 'store_90']]], $this->sentJson(0));
        self::assertSame(['website'], $result->results[0]->fields);
        self::assertNull($result->results[0]->application);

        try {
            $tawked->partner->applications->batch(array_fill(0, 101, ['external_id' => 'x']));
            self::fail('A batch of 101 was sent.');
        } catch (InvalidArgumentException $e) {
            self::assertStringContainsString('1 to 100', $e->getMessage());
        }
        self::assertCount(1, $this->history);
    }

    public function test_list_writes_booleans_dates_and_metadata_in_the_query(): void
    {
        $tawked = $this->client([
            self::json(200, ['total' => 0, 'applications' => []]),
            self::json(200, ['from' => '2026-08-01T00:00:00.000Z', 'to' => '2026-08-31T00:00:00.000Z', 'balance_halalas' => 1, 'applications' => []]),
        ], [], self::PARTNER_KEY);

        $tawked->partner->applications->list(['status' => 'active', 'disabled' => false, 'archived' => true, 'metadata' => ['plan' => 'pro'], 'limit' => 10, 'offset' => null]);
        $tawked->partner->usage->get(['from' => new DateTimeImmutable('2026-08-01T00:00:00Z'), 'to' => '2026-08-31']);

        self::assertSame('status=active&disabled=false&archived=true&metadata%5Bplan%5D=pro&limit=10', $this->request(0)->getUri()->getQuery());
        self::assertSame(['from' => '2026-08-01T00:00:00.000+00:00', 'to' => '2026-08-31'], $this->sentQuery(1));
    }

    public function test_the_logo_goes_as_multipart_from_a_path_a_resource_or_a_stream(): void
    {
        $row = self::endpoints()['partner-logo-upload']['examples'][0]['body'];
        self::assertIsArray($row);
        $jpeg = "\xFF\xD8\xFF\xE0fake-jpeg";
        $path = tempnam(sys_get_temp_dir(), 'tawked-logo');
        self::assertIsString($path);
        file_put_contents($path, $jpeg);
        $resource = fopen($path, 'rb');
        self::assertIsResource($resource);
        $tawked = $this->client([self::json(200, $row), self::json(200, $row), self::json(200, $row)], [], self::PARTNER_KEY);

        try {
            $application = $tawked->partner->applications->uploadLogo('store_88', ['logo' => $path], ['idempotency_key' => 'logo-1']);
            $tawked->partner->applications->uploadLogo('store_88', ['logo' => new \SplFileInfo($path)]);
            $tawked->partner->applications->uploadLogo('store_88', ['logo' => $resource]);
        } finally {
            fclose($resource);
            unlink($path);
        }

        foreach ([0, 1, 2] as $i) {
            $body = (string) $this->request($i)->getBody();
            self::assertStringContainsString('name="logo"; filename="'.basename($path).'"', $body);
            self::assertStringContainsString($jpeg, $body);
        }
        self::assertSame('logo-1', $this->request(0)->getHeaderLine('Idempotency-Key'));
        self::assertInstanceOf(Application::class, $application);
        self::assertNotNull($application->logoUrl);
    }

    public function test_a_logo_that_is_not_a_readable_file_is_refused_before_sending(): void
    {
        $tawked = $this->client([], [], self::PARTNER_KEY);

        $this->expectException(InvalidArgumentException::class);
        $tawked->partner->applications->uploadLogo('store_88', ['logo' => '/no/such/logo.png']);
    }

    public function test_suspend_resume_and_unpause_read_their_switch(): void
    {
        $tawked = $this->client([
            self::json(200, ['external_id' => 'store_88', 'disabled' => true]),
            self::json(200, ['external_id' => 'store_88', 'paused' => false]),
        ], [], self::PARTNER_KEY);

        $suspended = $tawked->partner->applications->suspend('store_88');
        $unpaused = $tawked->partner->applications->unpause('store_88');

        self::assertTrue($suspended->disabled);
        self::assertNull($suspended->paused);
        self::assertFalse($unpaused->paused);
        self::assertNull($unpaused->disabled);
        self::assertSame('', (string) $this->request(0)->getBody());
    }

    public function test_iterate_walks_the_offset_pages_of_applications(): void
    {
        $row = self::endpoints()['partner-show']['examples'][0]['body'];
        self::assertIsArray($row);
        $tawked = $this->client([
            self::json(200, ['total' => 3, 'applications' => [$row, $row]]),
            self::json(200, ['total' => 3, 'applications' => [$row]]),
        ], [], self::PARTNER_KEY);

        $all = iterator_to_array($tawked->partner->applications->iterate(['limit' => 2]), false);

        self::assertCount(3, $all);
        self::assertSame(['limit' => '2', 'offset' => '0'], $this->sentQuery(0));
        self::assertSame(['limit' => '2', 'offset' => '2'], $this->sentQuery(1));
        self::assertCount(2, $this->history);
    }

    public function test_iterate_walks_the_cursor_pages_of_verifications_events_and_deliveries(): void
    {
        $verification = self::endpoints()['partner-verifications']['examples'][0]['body']['data'][0];
        $event = self::endpoints()['partner-events']['examples'][0]['body']['data'][0];
        $delivery = self::endpoints()['partner-deliveries']['examples'][0]['body']['data'][0];
        $tawked = $this->client([
            self::json(200, ['data' => [$verification], 'next_cursor' => 'c1', 'has_more' => true]),
            self::json(200, ['data' => [$verification], 'next_cursor' => 'c2', 'has_more' => false]),
            self::json(200, ['data' => [$event], 'next_cursor' => 'e1', 'has_more' => true]),
            self::json(200, ['data' => [$event], 'next_cursor' => 'e2', 'has_more' => false]),
            self::json(200, ['data' => [$delivery], 'next_cursor' => 'd1']),
            self::json(200, ['data' => [$delivery], 'next_cursor' => null]),
        ], [], self::PARTNER_KEY);

        $verifications = iterator_to_array($tawked->partner->verifications->iterate(['status' => 'verified']), false);
        $events = iterator_to_array($tawked->partner->events->iterate(['since' => '2026-09-01T00:00:00Z']), false);
        $deliveries = iterator_to_array($tawked->partner->webhook->deliveries->iterate(), false);

        self::assertCount(2, $verifications);
        self::assertSame(['status' => 'verified', 'cursor' => 'c1'], $this->sentQuery(1));
        self::assertCount(2, $events);
        self::assertSame('e1', $this->sentQuery(3)['cursor']);
        self::assertCount(2, $deliveries);
        self::assertSame(['cursor' => 'd1'], $this->sentQuery(5));
        self::assertCount(6, $this->history);
    }

    public function test_webhook_update_sends_null_to_reset_the_events(): void
    {
        $answer = self::endpoints()['partner-webhook']['examples'][0]['body'];
        self::assertIsArray($answer);
        $tawked = $this->client([self::json(200, $answer)], [], self::PARTNER_KEY);

        $webhook = $tawked->partner->webhook->update(['events' => null, 'low_balance_threshold_halalas' => 0]);

        self::assertSame('{"events":null,"low_balance_threshold_halalas":0}', (string) $this->request()->getBody());
        self::assertSame('PUT', $this->request()->getMethod());
        self::assertTrue($webhook->configured);
        self::assertContains('balance.low', $webhook->defaultEvents);
    }

    public function test_an_empty_success_body_reads_as_an_empty_object(): void
    {
        $tawked = $this->client([new Response(200, ['X-Request-Id' => 'r1'], '')]);

        $result = $tawked->verify->cancel(self::ID);

        self::assertSame([], $result->toArray());
        self::assertSame('', $result->id);
        self::assertSame('r1', $result->requestId());
    }

    public function test_fields_the_api_adds_later_are_kept_in_to_array(): void
    {
        $tawked = $this->client([self::json(200, ['id' => self::ID, 'status' => 'canceled', 'new_field' => 'x'])]);

        $result = $tawked->verify->cancel(self::ID);

        self::assertSame('x', $result->toArray()['new_field']);
    }
}
