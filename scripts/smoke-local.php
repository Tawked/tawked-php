<?php

/*
 * The smoke run against the LOCAL app: this package
 * calls Tawked's app running locally with the keys `php artisan sdk:smoke-keys` wrote, in the order every
 * language follows. Never Verify (the local SMS gateways are live), never a key on the screen, never
 * an address that is not loopback.
 *
 *   TAWKED_SMOKE_BASE_URL=http://127.0.0.1:8000 TAWKED_SMOKE_KEYS=.build/smoke-keys.json composer smoke:local
 */

declare(strict_types=1);

use Tawked\Exception\ApprovalRequiredException;
use Tawked\Exception\InsufficientCreditsException;
use Tawked\TawkedAccountClient;
use Tawked\TawkedClient;
use Tawked\Webhook;

require dirname(__DIR__).'/vendor/autoload.php';

$repo = dirname(__DIR__, 3);
$baseUrl = getenv('TAWKED_SMOKE_BASE_URL') ?: 'http://127.0.0.1:8000';
$keysPath = getenv('TAWKED_SMOKE_KEYS') ?: '.build/smoke-keys.json';

$host = trim((string) parse_url($baseUrl, PHP_URL_HOST), '[]');
if (! in_array($host, ['127.0.0.1', 'localhost', '::1'], true)) {
    fwrite(STDERR, "Refused: {$baseUrl} is not a loopback address. The smoke run only calls the local app.\n");
    exit(2);
}
$keys = json_decode((string) file_get_contents(str_starts_with($keysPath, '/') ? $keysPath : $repo.'/'.$keysPath), true, flags: JSON_THROW_ON_ERROR);
$contract = json_decode((string) file_get_contents($repo.'/sdks/contract/api.json'), true, flags: JSON_THROW_ON_ERROR);
$example = static function (string $id) use ($contract): array {
    foreach ($contract['endpoints'] as $endpoint) {
        if ($endpoint['id'] === $id) {
            return $endpoint['sdk']['example'];
        }
    }
    throw new RuntimeException("no endpoint {$id} in the contract");
};

$passed = 0;
$step = static function (string $label, callable $run) use (&$passed): void {
    try {
        $note = $run();
        $passed++;
        fwrite(STDOUT, "pass  {$label}".($note !== null && $note !== '' ? ": {$note}" : '')."\n");
    } catch (Throwable $e) {
        // The message only: an SDK exception never carries a key, and nothing here prints one.
        fwrite(STDERR, "FAIL  {$label}: ".$e::class.': '.$e->getMessage()."\n");
        exit(1);
    }
};
$stamp = (string) (int) (microtime(true) * 1000);
$options = ['base_url' => $baseUrl];

// 1. e-mail: send → get → list (fake SES).
$email = new TawkedClient($keys['email']['key'], $options);
$emailId = null;
$step('email.messages.send', static function () use ($email, $stamp, &$emailId): string {
    try {
        $sent = $email->email->messages->send([
            'to' => ['sara@example.com'],
            'subject' => 'تجربة الحزمة المحلية',
            'text' => 'رسالة تجربة من حزمة PHP.',
            'tags' => [['name' => 'smoke', 'value' => 'php']],
        ], ['idempotency_key' => "smoke-php-email-{$stamp}"]);
        $emailId = $sent->id;

        return (string) $sent->status;
    } catch (InsufficientCreditsException) {
        return 'answered 402 insufficient_credits (the demo account has no balance left; the request reached the app and was refused as documented)';
    }
});
if ($emailId !== null) {
    $step('email.messages.get', static fn() => (string) $email->email->messages->get($emailId)->status);
}
$step('email.messages.list', static fn() => count($email->email->messages->list(['limit' => 5])->data).' on the first page');

// 2. WhatsApp: templates list, message send → get (fake Meta).
$whatsapp = new TawkedClient($keys['whatsapp']['key'], $options);
$template = null;
$step('whatsapp.templates.list', static function () use ($whatsapp, &$template): string {
    $templates = $whatsapp->whatsapp->templates->list()->data;
    foreach ($templates as $t) {
        if ($t->status === 'APPROVED' && $t->placeholders->kind === 'none') {
            $template = $t;
            break;
        }
    }
    foreach ($template === null ? $templates : [] as $t) {
        if ($t->status === 'APPROVED') {
            $template = $t;
            break;
        }
    }

    return count($templates).' templates';
});
$messageId = null;
$step('whatsapp.messages.send', static function () use ($whatsapp, &$template, $stamp, &$messageId): string {
    if ($template === null) {
        throw new RuntimeException('no approved template on the demo number');
    }
    $names = [...$template->placeholders->header, ...$template->placeholders->body];
    $params = ['to' => '0550000000', 'template' => $template->name, 'lang' => $template->language];
    if ($names !== []) {
        $params['params'] = array_fill_keys(array_map('strval', $names), 'تجربة');
    }
    $sent = $whatsapp->whatsapp->messages->send($params, ['idempotency_key' => "smoke-php-wa-{$stamp}"]);
    $messageId = $sent->id;

    return (string) $sent->status;
});
$step('whatsapp.messages.get', static fn() => (string) $whatsapp->whatsapp->messages->get((string) $messageId)->status);

// 3. The connected app: chat menus create → get → update → delete; attendant get and a draft.
$app = new TawkedClient($keys['connected_app']['key'], $options);
$menuId = null;
$step('chat.menus.create', static function () use ($app, $stamp, &$menuId): string {
    $menu = $app->chat->menus->create(['title' => mb_substr("تجربة {$stamp}", 0, 30), 'url' => 'https://example.com/tawked-smoke'], ['idempotency_key' => "smoke-php-menu-{$stamp}"]);
    $menuId = $menu->id;

    return $menuId;
});
$step('chat.menus.get', static fn() => $app->chat->menus->get((string) $menuId)->title);
$step('chat.menus.update', static fn() => $app->chat->menus->update((string) $menuId, ['title' => 'تجربة معدّلة'])->title);
$step('chat.menus.delete', static fn() => $app->chat->menus->delete((string) $menuId)->deleted ? 'deleted' : 'not deleted');
$step('chat.attendant.get', static fn() => $app->chat->attendant->get()->state);
$step('chat.attendant.saveDraft', static fn() => $app->chat->attendant->saveDraft($example('chat-attendant-draft')['body'], ['idempotency_key' => "smoke-php-attendant-{$stamp}"])::class);

// 4. Partner: proved against sdks/mock-server only. A partner key cannot be narrowed, and even the
// sandbox one texts its owner's phone through the live local gateways (sdks/smoke/README.md).

// 5. Account: me.show, operations.search, one consequential call stopped at its 428 (never approved).
$account = new TawkedAccountClient($keys['account']['token'], $options + ['language' => 'en']);
$step('me.show', static fn() => implode(', ', array_slice(array_keys((array) $account->me->show()->data), 0, 3)));
$step('operations.search', static fn() => count($account->operations->search()->items()).' on the first page');
$step('email.keys.create stops at 428', static function () use ($account, $keys): string {
    if (($keys['account']['approvals'] ?? 'off') === 'off') {
        throw new RuntimeException('the smoke token must have approvals on, or the consequential call would run');
    }
    try {
        $account->email->keys->create($keys['account']['application_id']);
    } catch (ApprovalRequiredException $e) {
        if ($e->approvalId() !== null && ($e->humanAction()['url'] ?? null) !== null) {
            return 'approval '.($e->approval()['status'] ?? 'pending');
        }
        throw $e;
    }
    throw new RuntimeException('a consequential call ran without approval');
});

// 6. Webhooks: a delivery signed by the app's own signer (App\Partners\WebhookSigner), verified by the SDK.
$step('webhooks.verify (signed by the app)', static function () use ($repo, $stamp): string {
    $secret = "smoke-php-{$stamp}-secret-0000000000";
    $timestamp = (string) (int) (microtime(true) * 1000);
    $body = json_encode(['event' => 'service.approved', 'data' => ['external_id' => 'متجر/88', 'status' => 'active'], 'sent_at' => $timestamp], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    $code = "echo App\\Partners\\WebhookSigner::sign(base64_decode('".base64_encode($secret)."'), '{$timestamp}', base64_decode('".base64_encode($body)."'));";
    $out = [];
    exec('cd '.escapeshellarg($repo).' && '.escapeshellarg(getenv('PHP') ?: PHP_BINARY).' artisan tinker --execute='.escapeshellarg($code).' 2>/dev/null', $out);
    $signature = trim((string) end($out));
    if (preg_match('/^[0-9a-f]{64}$/', $signature) !== 1) {
        throw new RuntimeException("the app's signer did not answer");
    }
    if (! Webhook::verify($body, ['tawked-timestamp' => $timestamp, 'tawked-signature' => $signature], $secret)) {
        throw new RuntimeException("the SDK refused the app's signature");
    }

    return "the app's bytes verify";
});

fwrite(STDOUT, "\n{$passed} steps passed against {$baseUrl}; no Verify call was made.\n");
