<?php

declare(strict_types=1);

namespace Tawked\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Psr\Http\Message\RequestInterface;
use Tawked\Attachment;
use Tawked\Contract;
use Tawked\Exception\ApprovalRequiredException;
use Tawked\Exception\GoneException;
use Tawked\Exception\IdempotencyException;
use Tawked\Exception\InvalidArgumentException;
use Tawked\Exception\PermissionDeniedException;
use Tawked\Exception\SecretNotReplayableException;
use Tawked\TawkedAccountClient;
use Tawked\TawkedClient;
use Tawked\Tests\TestCase;

/**
 * The hand-written core beside the generated surface: encodings, key modes, the Account API client,
 * its approvals, attachments and the in-progress window.
 */
final class CoreTest extends TestCase
{
    private const TOKEN = 'tk_acct_dGVzdHRva2VuMDAwMDAwMDAwMDAwMDAwMA';

    private const ACCEPTED = ['id' => 'm1', 'status' => 'accepted', 'from' => ['name' => null, 'address' => 'orders@acme.sa'], 'to' => 'sara@example.com', 'subject' => 'x', 'reference' => null, 'price_halalas' => 1, 'created_at' => null];

    public function test_an_arabic_subject_is_sent_as_utf8_bytes(): void
    {
        $tawked = $this->client([self::json(202, self::ACCEPTED)]);

        $tawked->email->messages->send(['from' => 'orders@acme.sa', 'to' => 'sara@example.com', 'subject' => 'طلبك رقم ١٠٤٢ في الطريق', 'text' => 'شكرًا لك']);

        $raw = (string) $this->request()->getBody();
        self::assertStringContainsString('"subject":"طلبك رقم ١٠٤٢ في الطريق"', $raw);
        self::assertStringNotContainsString('\u', $raw);
        self::assertSame('طلبك رقم ١٠٤٢ في الطريق', $this->sentJson()['subject']);
    }

    public function test_an_empty_object_is_sent_as_an_object_never_a_list(): void
    {
        $tawked = $this->client([self::json(202, self::ACCEPTED)]);

        $tawked->email->messages->send(['from' => 'orders@acme.sa', 'to' => 'sara@example.com', 'subject' => 'x', 'text' => 'y', 'headers' => []]);

        self::assertStringContainsString('"headers":{}', (string) $this->request()->getBody());
    }

    public function test_dates_go_with_their_offset_and_attachments_as_the_wire_object(): void
    {
        $tawked = $this->client([self::json(202, self::ACCEPTED)]);

        $tawked->email->messages->send([
            'from' => 'orders@acme.sa',
            'to' => ['sara@example.com'],
            'subject' => 'x',
            'text' => 'y',
            'scheduled_at' => new \DateTimeImmutable('2026-10-23T09:00:00+03:00'),
            'attachments' => [Attachment::fromBytes('%PDF-1.4', 'invoice-1042.pdf')],
        ], ['idempotency_key' => str_repeat('k', 200)]);

        $sent = $this->sentJson();
        self::assertSame('2026-10-23T09:00:00.000+03:00', $sent['scheduled_at']);
        self::assertSame([['filename' => 'invoice-1042.pdf', 'content' => base64_encode('%PDF-1.4'), 'content_type' => 'application/pdf']], $sent['attachments']);
        // Email takes keys up to 256 characters (the contract's own bound for it).
        self::assertSame(str_repeat('k', 200), $this->request()->getHeaderLine('Idempotency-Key'));
    }

    public function test_a_key_longer_than_the_endpoint_takes_is_refused_before_sending(): void
    {
        $tawked = $this->client([]);

        $this->expectExceptionMessage('`Idempotency-Key` must be 1-128 printable ASCII characters');
        $tawked->verify->start(['to' => '0551234567'], ['idempotency_key' => str_repeat('k', 129)]);
    }

    public function test_an_attachment_over_the_bound_is_refused_before_sending(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Attachment::fromBytes(str_repeat('a', Contract::LIMITS['email_attachments_max_bytes'] + 1), 'big.bin');
    }

    public function test_a_batch_over_its_bound_is_refused_before_sending(): void
    {
        $tawked = $this->client([]);

        try {
            $tawked->email->messages->sendBatch(array_fill(0, 101, ['subject' => 'x']));
            self::fail('A batch of 101 was sent.');
        } catch (InvalidArgumentException $e) {
            self::assertStringContainsString('`emails` must hold 1 to 100 items', $e->getMessage());
        }
        self::assertSame([], $this->history);
    }

    public function test_key_mode_reads_the_prefix_longest_first(): void
    {
        self::assertSame('live', (new TawkedClient('tk_live_x'))->keyMode());
        self::assertSame('test', (new TawkedClient('tk_test_x'))->keyMode());
        self::assertSame('partner', (new TawkedClient('tk_partner_x'))->keyMode());
        self::assertSame('partner_test', (new TawkedClient('tk_partner_test_x'))->keyMode());
        self::assertSame('unknown', (new TawkedClient('sk_other'))->keyMode());
        self::assertSame('account', (new TawkedAccountClient(self::TOKEN))->keyMode());
        self::assertNotSame([], Contract::SANDBOX_NOTES);
    }

    public function test_the_account_client_takes_an_account_token_only(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new TawkedAccountClient('tk_live_not_an_account_token');
    }

    public function test_the_account_client_sends_its_language_and_reads_the_envelope(): void
    {
        $account = $this->account([self::json(200, ['data' => ['id' => 'u1', 'name' => 'Sara'], 'next' => 'Call applications.list.'], ['Tawked-Untrusted-Content' => 'true'])], ['language' => 'ar']);

        $me = $account->me->show();

        self::assertSame(['id' => 'u1', 'name' => 'Sara'], $me->data);
        self::assertSame('Call applications.list.', $me->next);
        self::assertTrue($me->untrusted());
        self::assertSame('ar', $this->request()->getHeaderLine('Accept-Language'));
        self::assertSame('Bearer '.self::TOKEN, $this->request()->getHeaderLine('Authorization'));
        self::assertStringNotContainsString(self::TOKEN, print_r($account, true));
    }

    public function test_an_account_write_mints_its_key_and_is_sent_once_after_a_5xx(): void
    {
        $account = $this->account([self::json(500, ['error' => 'internal_error', 'message' => 'x', 'next' => 'Retry later.'])]);

        try {
            $account->applications->applications->create(['name_ar' => 'متجر', 'name_en' => 'Shop', 'industry' => 'retail', 'website_url' => 'https://shop.example']);
            self::fail('No exception thrown.');
        } catch (\Tawked\Exception\ServerException $e) {
            self::assertSame($this->request()->getHeaderLine('Idempotency-Key'), $e->idempotencyKey());
            self::assertSame('Retry later.', $e->next());
        }
        self::assertCount(1, $this->history);
    }

    public function test_wait_and_retry_polls_then_sends_the_same_request_with_the_approval(): void
    {
        $required = self::json(428, [
            'error' => 'approval_required',
            'message' => 'Waits for its person.',
            'next' => 'Relay human_action.',
            'approval' => ['id' => 'ap_1', 'status' => 'pending', 'operation' => 'account.sessions.revoke', 'expires_at' => '2026-10-20T12:00:00Z'],
            'human_action' => ['reason' => 'approve_request', 'url' => 'https://tawked.com/x', 'say' => 'Approve it.', 'then' => 'approvals.show'],
        ]);
        $account = $this->account([
            $required,
            self::json(200, ['data' => ['id' => 'ap_1', 'status' => 'pending']]),
            self::json(200, ['data' => ['id' => 'ap_1', 'status' => 'approved']]),
            self::json(200, ['data' => ['revoked' => 3]]),
        ]);

        try {
            $account->account->sessions->revoke(['idempotency_key' => 'revoke-1']);
            self::fail('No 428.');
        } catch (ApprovalRequiredException $e) {
            self::assertSame('ap_1', $e->approvalId());
            self::assertSame('Approve it.', $e->humanAction()['say'] ?? null);
            $waits = [];
            $result = $account->approvals->waitAndRetry($e, ['poll_every' => 30, 'sleep' => function (float $s) use (&$waits): void {
                $waits[] = $s;
            }]);
            self::assertSame(['revoked' => 3], $result->data);
            self::assertSame([30.0], $waits);
        }
        self::assertSame('/v1/approvals/ap_1', $this->request(1)->getUri()->getPath());
        self::assertSame('revoke-1', $this->request(3)->getHeaderLine('Idempotency-Key'));
        self::assertSame('ap_1', $this->request(3)->getHeaderLine('Tawked-Approval'));
        self::assertSame($this->request(0)->getUri()->getPath(), $this->request(3)->getUri()->getPath());
        self::assertSame((string) $this->request(0)->getBody(), (string) $this->request(3)->getBody());
    }

    public function test_a_declined_approval_answers_its_own_refusal(): void
    {
        $account = $this->account([
            self::json(428, ['error' => 'approval_required', 'next' => 'x', 'approval' => ['id' => 'ap_2', 'status' => 'pending', 'operation' => 'o', 'expires_at' => 'x'], 'human_action' => ['reason' => 'approve_request', 'url' => 'u', 'say' => 's', 'then' => null]]),
            self::json(200, ['data' => ['id' => 'ap_2', 'status' => 'declined']]),
            self::json(403, ['error' => 'approval_declined', 'next' => 'Do not send it again.']),
        ]);

        try {
            $account->account->sessions->revoke();
        } catch (ApprovalRequiredException $e) {
            $this->expectException(PermissionDeniedException::class);
            $this->expectExceptionMessage('approval_declined');
            $account->approvals->waitAndRetry($e, ['sleep' => static function (float $s): void {}]);
        }
    }

    public function test_a_still_pending_approval_throws_the_428_again_after_the_timeout(): void
    {
        $pending = self::json(200, ['data' => ['id' => 'ap_3', 'status' => 'pending']]);
        $account = $this->account([
            self::json(428, ['error' => 'approval_required', 'next' => 'x', 'approval' => ['id' => 'ap_3', 'status' => 'pending', 'operation' => 'o', 'expires_at' => 'x'], 'human_action' => ['reason' => 'approve_request', 'url' => 'u', 'say' => 's', 'then' => null]]),
            $pending, $pending, $pending,
        ]);

        try {
            $account->account->sessions->revoke();
        } catch (ApprovalRequiredException $e) {
            try {
                $account->approvals->waitAndRetry($e, ['poll_every' => 30, 'timeout' => 60, 'sleep' => static function (float $s): void {}]);
                self::fail('Still pending, it must throw.');
            } catch (ApprovalRequiredException $again) {
                self::assertSame($e, $again);
            }
        }
        self::assertCount(4, $this->history);
    }

    public function test_a_secret_answer_replayed_is_secret_not_replayable_with_its_id(): void
    {
        $account = $this->account([self::json(409, ['error' => 'idempotency_secret_not_replayable', 'message' => 'Find it by its id.', 'id' => 'key_9', 'original_status' => 201, 'next' => 'x'])]);

        try {
            $account->email->keys->create('app_1', ['idempotency_key' => 'smtp-key-1']);
            self::fail('No exception thrown.');
        } catch (SecretNotReplayableException $e) {
            self::assertInstanceOf(IdempotencyException::class, $e);
            self::assertSame('key_9', $e->id());
            self::assertSame(201, $e->originalStatus());
        }
    }

    public function test_the_in_progress_wait_ends_after_its_window_with_the_key_first(): void
    {
        $tawked = $this->client(array_fill(0, 20, self::json(409, ['error' => 'idempotency_in_progress'])), ['in_progress_wait' => 10]);

        try {
            $tawked->email->messages->send(['from' => 'orders@acme.sa', 'to' => 'sara@example.com', 'subject' => 'x', 'text' => 'y'], ['idempotency_key' => 'order-1042-shipped']);
            self::fail('No exception thrown.');
        } catch (IdempotencyException $e) {
            self::assertStringStartsWith("`Idempotency-Key order-1042-shipped` is still being processed: call again with `idempotency_key: 'order-1042-shipped'`", $e->getMessage());
        }
        self::assertSame([1.0, 2.0, 4.0, 3.0], $this->sleeps);
    }

    public function test_product_retired_is_gone(): void
    {
        $tawked = $this->client([self::json(410, ['error' => 'product_retired'])]);

        $this->expectException(GoneException::class);
        $tawked->verify->get('v1');
    }

    /**
     * @param list<\GuzzleHttp\Psr7\Response|\Throwable> $responses
     * @param array<string, mixed> $options
     */
    private function account(array $responses, array $options = []): TawkedAccountClient
    {
        $this->history = [];
        $this->handler = new MockHandler($responses);
        $stack = HandlerStack::create($this->handler);
        $stack->push(function (callable $next): callable {
            return function (RequestInterface $request, array $options) use ($next) {
                $this->history[] = $request;

                return $next($request, $options);
            };
        });

        return new TawkedAccountClient(self::TOKEN, ['http_client' => new Client(['handler' => $stack]), 'sleep' => function (float $s): void {
            $this->sleeps[] = $s;
        }, ...$options]);
    }
}
