<p>
  <a href="https://tawked.com">
    <picture>
      <source media="(prefers-color-scheme: dark)" srcset="https://docs.tawked.com/logo/dark.svg">
      <img alt="Tawked" src="https://docs.tawked.com/logo/light.svg" width="172" height="48">
    </picture>
  </a>
</p>

# Tawked SDK for PHP

[![Packagist](https://img.shields.io/packagist/v/tawked/tawked-php)](https://packagist.org/packages/tawked/tawked-php) [![PHP](https://img.shields.io/packagist/dependency-v/tawked/tawked-php/php)](https://packagist.org/packages/tawked/tawked-php) [![CI](https://github.com/tawked/tawked-php/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/tawked/tawked-php/actions/workflows/ci.yml) [![License: MIT](https://img.shields.io/badge/license-MIT-blue)](https://github.com/tawked/tawked-php/blob/main/LICENSE)

The official Tawked SDK for PHP. Use it to send SMS OTP verification codes to Saudi mobile numbers and check them, send WhatsApp Business API template messages from your business's own number, send transactional email from your own domain, and verify Tawked's signed webhooks, from any PHP 8.1+ application. It also covers Tawked Chat for connected apps, the Partner API and the Account API. It uses Guzzle 7 or 8, or any PSR-18 HTTP client. On Laravel, use [tawked/laravel](https://packagist.org/packages/tawked/laravel), built on this package.

Tawked (توكيد) is a Saudi messaging platform at [tawked.com](https://tawked.com) and a Meta Tech Provider: businesses send WhatsApp Business API notifications from their own number and SMS OTP verification codes, and answer customers in Tawked Chat, a shared WhatsApp inbox for teams. Tawked Email, in early access, sends a business's mail from its own domain.

[Documentation](https://docs.tawked.com) · [API reference](https://docs.tawked.com/api-reference/verify/start-a-verification) · [Changelog](https://github.com/tawked/tawked-php/blob/main/CHANGELOG.md) · [Pricing](https://tawked.com/en/pricing)

## Installation

```bash
composer require tawked/tawked-php
```

## Before you start

- [Sign up](https://tawked.com/en/signup) and create an application in the console.
- Create an API key on your application's API keys page: `tk_live_…`, or `tk_test_…` before the application is approved (see Testing). [Authentication](https://docs.tawked.com/authentication) explains keys and their scopes.
- Put the key in the `TAWKED_API_KEY` environment variable of your server.
- Every product draws on one prepaid balance in Saudi riyals: see [pricing](https://tawked.com/en/pricing). Partner keys (`tk_partner_…`) and account tokens (`tk_acct_…`) are described in their own sections.

## Quick start

With Composer's autoloader loaded (`require 'vendor/autoload.php';`):

```php
$tawked = new Tawked\TawkedClient(); // reads TAWKED_API_KEY

// 1. Send a code by SMS. Any Saudi mobile format works: 0512345678, +966512345678, ٠٥١٢٣٤٥٦٧٨.
$started = $tawked->verify->start(['to' => '0512345678'], ['idempotency_key' => 'signin-7781']);

// 2. Check the code the person typed.
$result = $tawked->verify->check(['id' => $started->id, 'code' => '482913']);
echo $result->verified ? 'signed in' : $result->status;
```

A wrong or late code is an answer, not an exception: read `verified`. Keep the key on your server: see Security below.

## What you can do

| Task | Call | Docs |
|---|---|---|
| Send an SMS OTP code to a Saudi mobile number | `$tawked->verify->start()` | [Quickstart](https://docs.tawked.com/quickstart) |
| Check the code the user typed | `$tawked->verify->check()` | [Sign-in flow](https://docs.tawked.com/verify/sign-in-flow) |
| Send a WhatsApp template message from your number | `$tawked->whatsapp->messages->send()` | [Messages](https://docs.tawked.com/whatsapp/messages) |
| Read a WhatsApp message's status | `$tawked->whatsapp->messages->get()` | [Messages](https://docs.tawked.com/whatsapp/messages) |
| List your approved WhatsApp templates | `$tawked->whatsapp->templates->list()` | [Templates](https://docs.tawked.com/whatsapp/templates) |
| Send a transactional email from your domain | `$tawked->email->messages->send()` | [Sending email](https://docs.tawked.com/email/messages) |
| Send up to 100 emails in one call | `$tawked->email->messages->sendBatch()` | [Sending email](https://docs.tawked.com/email/messages) |
| Verify a webhook and read its event | `Tawked\Webhook::constructEvent()` | [Webhooks](https://docs.tawked.com/webhooks) |
| Add your app's menu to Tawked Chat (connected app) | `$connection->chat->menus->create()` | [Chat menus](https://docs.tawked.com/chat-menus) |
| Connect your app to a business's account | `Tawked\OAuth\Apps::authorizeUrl()`, `exchangeCode()` | [Connected apps](https://docs.tawked.com/integrations/connected-apps) |
| Open a Verify application per merchant (platforms) | `$partner->partner->applications->create()` | [Partner API](https://docs.tawked.com/partners/overview) |
| Run anything the console does, for a script or an AI agent | `new Tawked\TawkedAccountClient()` | [Account API](https://docs.tawked.com/account-api/overview) |

## Requirements

| Requirement | Version |
|---|---|
| PHP | 8.1 or newer (tested on 8.1 to 8.5) |
| Extensions | `json` |
| HTTP client | Guzzle 7.5+ or 8 by default, or any PSR-18 client |
| Frameworks | any; Laravel users install `tawked/laravel` |

## Configuration

`new Tawked\TawkedClient()` with no key reads `TAWKED_API_KEY`, and `TAWKED_BASE_URL` when it is set. Pass the key yourself as the first argument, and options as the second:

<!-- lint-only: makes the client the other examples use -->
```php
$tawked = new Tawked\TawkedClient(getenv('TAWKED_API_KEY'), [
    'timeout' => 15,          // seconds per attempt; by default 30, or 120 for a batch,
                              // a secret or the OAuth exchange
    'connect_timeout' => 5,   // 10 by default
    'max_retries' => 3,       // 2 by default; 0 turns retries off
    'in_progress_wait' => 30, // how long a keyed write waits on idempotency_in_progress
    // 'base_url' => 'https://tawked.com',   // the default
    // 'http_client' => $psr18Client,        // any PSR-18 client; a Guzzle client by default
    // 'headers' => ['X-Team' => 'checkout'], // sent on every request
]);
```

The last argument of every method takes `idempotency_key`, `timeout`, `max_retries` and `headers` for that one call.

## Tawked Verify

One-time codes by SMS to Saudi mobile numbers, with an application's API key. Tawked makes the code, sends it and checks it: you never see the code.

```php
// $tawked = new Tawked\TawkedClient(); as in Quick start
$started = $tawked->verify->start(
    ['to' => '0551234567', 'reference' => 'signin-7781'],
    ['idempotency_key' => 'signin-7781'],
);

// Later, with the code the user typed:
$result = $tawked->verify->check(['id' => $started->id, 'code' => $code]);

if ($result->verified) {
    // signed in
} else {
    // $result->status: invalid_code, too_many_attempts, expired, failed or canceled
    // $result->attemptsRemaining on invalid_code
}
```

Also `$tawked->verify->get($id)`, `resend($id)` and `cancel($id)`. Destinations take any Saudi mobile format (`+966512345678`, `0512345678`, `512345678`, Arabic digits): see [number formats](https://docs.tawked.com/destinations).

## WhatsApp Business API

Meta-approved template messages to your customers from the application's own WhatsApp number, with the application's live API key.

```php
// $tawked = new Tawked\TawkedClient(); as in Quick start
$sent = $tawked->whatsapp->messages->send([
    'to' => '+966512345678',
    'template' => 'order_shipped',
    'lang' => 'ar',
    'params' => ['name' => 'سارة', 'order' => '1042'],
    'reference' => 'order-1042',
], ['idempotency_key' => 'order-1042-shipped']);

$message = $tawked->whatsapp->messages->get($sent->id); // accepted, sent, delivered, read or failed
$templates = $tawked->whatsapp->templates->list();      // each with its id, the placeholders it needs and `managed`
$template = $tawked->whatsapp->templates->get($templates->data[0]->id);
```

Creating, editing and deleting a template take a **connected app's key**: the key a business's approved connection hands your app (see Connected apps). A business's own key, full access included, is refused with `insufficient_scope` (an `InsufficientScopeException` whose `requiredScope()` is `notifications:templates`).

```php
// In your app: the key of one business's connection.
$connection = new Tawked\TawkedClient($connectionKey);

$created = $connection->whatsapp->templates->create([
    'name' => 'order_shipped_v1',
    'language' => 'ar',
    'category' => 'UTILITY',
    'body' => 'طلبك رقم {{1}} في الطريق إليك.',
    'buttons' => [['type' => 'URL', 'text' => 'تتبع الطلب', 'url' => 'https://flowers.example/orders/{{1}}']],
    'examples' => ['body:1' => '1042', 'button:0' => '1042'], // one sample per placeholder: Meta reviews with them
], ['idempotency_key' => 'template-order-shipped-v1']);

// The whole new content, without the name and the language. Meta reviews the change again.
$connection->whatsapp->templates->update($created->id, [
    'category' => 'UTILITY',
    'body' => 'طلبك رقم {{1}} خرج للتوصيل.',
    'examples' => ['body:1' => '1042'],
]);

$connection->whatsapp->templates->delete($created->id)->deleted; // true
```

A refused template write says why on the exception:

```php
use Tawked\ErrorCode;
use Tawked\Exception\ApiException;

// $connection is the connected app's client from the block above; $templateId one of its templates
try {
    $connection->whatsapp->templates->delete($templateId);
} catch (ApiException $e) {
    match ($e->errorCode) {
        ErrorCode::INSUFFICIENT_SCOPE => null,                   // not a connected app's key
        ErrorCode::TEMPLATE_IN_USE => $e->usedBy(),              // ['campaign', 'automation', 'store_notice']
        ErrorCode::TEMPLATE_NOT_EDITABLE => $e->currentStatus(), // the template's status, META_SAMPLE or MEDIA_HEADER
        ErrorCode::TEMPLATE_REFUSED => $e->apiMessage(),         // Meta's own sentence
        ErrorCode::INVALID_REQUEST => $e->fieldErrors(),         // ['body' => ['placeholderEdges']] or ['name' => ['nameReserved']]
        ErrorCode::TEMPLATE_NOT_MANAGED => null,                 // your app changes only the templates it created
        default => throw $e,
    };
}
```

## Tawked Email (Early Access)

Tawked's team opens Email for an account on request, from the console. Until then every Email call answers `email_early_access`. Once it is open, an application's API key sends transactional email from an address on one of the application's verified domains.

```php
use Tawked\Attachment;

// $tawked = new Tawked\TawkedClient(); as in Quick start
$sent = $tawked->email->messages->send([
    'from' => ['name' => 'متجر أكمي', 'address' => 'orders@acme.sa'],
    'to' => 'sara@example.com',
    'subject' => 'طلبك رقم 1042 قيد التجهيز',
    'html' => '<p>استلمنا طلبك رقم 1042.</p>',
    'text' => 'استلمنا طلبك رقم 1042.',
    'reference' => 'order-1042',
    'attachments' => [Attachment::fromBytes('%PDF-1.4 ...', 'invoice-1042.pdf')],
], ['idempotency_key' => 'order-1042-confirmation']);

$sent->id;
$sent->status; // accepted
$tawked->email->messages->get($sent->id)->status; // accepted, sent, delivered, bounced or failed
```

`Attachment::fromPath()`, `fromBytes()` and `fromStream()` make an attachment. Tawked accepts up to 10 attachments and 10 MiB of attachments per email; the SDK refuses a file larger than 10 MiB before sending. A send to one address with no `cc`, `bcc`, `tags` or `scheduled_at` answers with `to` as a string; any other send answers with lists and `recipients`:

```php
// $tawked = new Tawked\TawkedClient(); as in Quick start
$sent = $tawked->email->messages->send([
    'from' => ['name' => 'Acme', 'address' => 'orders@acme.sa'],
    'to' => ['sara@example.com', 'Omar <omar@example.com>'],
    'cc' => 'accounts@acme.sa',
    'subject' => 'Your order is on its way',
    'text' => 'Order 1042 left our warehouse.',
    'tags' => [['name' => 'kind', 'value' => 'shipping']],
    'headers' => ['X-Order' => '1042'],
    'scheduled_at' => new DateTimeImmutable('+2 hours'),
], ['idempotency_key' => 'order-1042-shipped-mail']);

// A scheduled email can move or be canceled until it goes.
$tawked->email->messages->update($sent->id, ['scheduled_at' => new DateTimeImmutable('+3 hours')]);
$tawked->email->messages->cancel($sent->id);
```

Tawked accepts up to 100 emails in one batch, under one key for the whole batch; the SDK refuses more before sending:

```php
// $tawked = new Tawked\TawkedClient(); as in Quick start
$from = ['name' => 'Acme', 'address' => 'orders@acme.sa'];

$batch = $tawked->email->messages->sendBatch([
    [
        'from' => $from,
        'to' => 'sara@example.com',
        'subject' => 'Order 1042',
        'text' => 'Thank you.',
    ],
    [
        'from' => $from,
        'to' => 'omar@example.com',
        'subject' => 'Order 1043',
        'text' => 'Thank you.',
    ],
], [], ['idempotency_key' => 'nightly-receipts-2026-10-20']);

foreach ($tawked->email->messages->iterate(['status' => 'bounced']) as $message) {
    // every bounced message of the last 30 days, page after page
}

$tawked->email->checkKey()->canSend; // can this key send (and sign in to the SMTP relay)?
```

The server's `422 header_not_allowed` (with `header`) is the one truth about custom headers; `Tawked\Contract::EMAIL_DENIED_HEADERS` lists them so you can warn ahead of time. The SMTP relay for apps that only speak SMTP is described in [SMTP](https://docs.tawked.com/email/smtp).

The rest of Email is on `$tawked->email` too:

| What | Methods |
|---|---|
| Sending domains and their DNS records | `domains->create()`, `list()`, `iterate()`, `get()`, `update()`, `verify()`, `delete()` |
| Hosted templates, sent with `'template' => ['id' => ..., 'variables' => [...]]` | `templates->create()`, `list()`, `iterate()`, `get()`, `update()`, `publish()`, `delete()`, `templates->versions->list()`, `get()`, `restore()` |
| Addresses held back after a bounce or a complaint | `suppressions->add()`, `list()`, `iterate()`, `get()`, `remove()`, `suppressions->imports->create()`, `get()` |
| Figures by day, week or month | `metrics->get()` |
| The copy of a sent message, while the application keeps previews | `messages->content($id)` |

[Senders](https://docs.tawked.com/email/senders) explains domains and their records.

### Receiving email

A live key holding `email:inbound` (a switch of its own on the key form) reads the email your application receives under `$tawked->email->inbound`: `list()` and `iterate()`, `get()`, `raw()` and `attachments->get()` as bytes, `delete()`, `forward()`, and `addresses->list()` for the receiving addresses. A test key simulates one with `$tawked->email->inbound->test()`: one `email.received` webhook in test mode, nothing stored. The model names the raw message `rawMessage`, since every model's `raw` is its JSON.

```php
// $tawked = new Tawked\TawkedClient(); as in Quick start, with a key holding email:inbound
foreach ($tawked->email->inbound->iterate(['spam' => 'false']) as $received) {
    $email = $tawked->email->inbound->get($received->id);
}

$tawked->email->inbound->forward(
    '0199f3a1-5c2e-7d40-9a1b-2c3d4e5f6a7b',
    ['to' => ['ops@acme.sa']],
    ['idempotency_key' => 'forward-1042'],
);
```

## Tawked Chat for connected apps

The conversation menus of a business's Tawked Chat and its auto-attendant, with a connected app's key (see Connected apps).

```php
// The key of one business's connection (see Connected apps).
$connection = new Tawked\TawkedClient($connectionKey);

$menu = $connection->chat->menus->create([
    'title' => 'طلباتي',
    'url' => 'https://app.example.com/tawked/orders',
], ['idempotency_key' => 'menu-orders']);

$connection->chat->menus->update($menu->id, ['title' => 'طلبات العميل']);
$connection->chat->menus->list();
$connection->chat->menus->delete($menu->id);

$attendant = $connection->chat->attendant->get();
$connection->chat->attendant->setState(['enabled' => true]);
```

`chat->attendant->saveDraft(['menu' => [...]])` saves a menu (a menu that breaks a rule is saved with its `problems`), `publish()` makes the draft live and `updateSettings(['mode' => ...])` changes how it answers. More in [Tawked Chat for apps](https://docs.tawked.com/chat).

## Connected apps

Your app asks a business to connect it, with your app's client id and secret from the console: the business approves in its own console, and your app gets a key for the one application they chose.

```php
use Tawked\OAuth\Apps;

$start = Apps::authorizeUrl([
    'client_id' => $clientId,
    'redirect_uri' => 'https://app.example/tawked/return',
]);
// Keep $start->state and $start->codeVerifier in the signed-in user's session; redirect to $start->url.
```

On the return, check `state` first, then exchange the code. The code is single-use, so the exchange is never retried (it waits up to 120 seconds); it sends Client Secret Basic by default, or `['client_auth' => 'post']` for the body:

<!-- lint-only: runs on the return of the authorize page, with its ?code= and the session -->
```php
if (! hash_equals($_SESSION['tawked_state'], $_GET['state'] ?? '')) {
    exit('state mismatch');
}
$connection = Apps::exchangeCode([
    'client_id' => $clientId,
    'client_secret' => getenv('TAWKED_APP_SECRET'),
    'code' => $_GET['code'],
    'redirect_uri' => 'https://app.example/tawked/return',
    'code_verifier' => $_SESSION['tawked_verifier'],
]);
$connection->accessToken;           // the connection's key, shown once: store it now
$connection->application->nameAr;   // the application the business chose
```

`invalid_grant` after a timeout means the connection may have been made: ask the business to approve the app again (a new request replaces it).

## Partner API

For platforms that resell Tawked Verify to their merchants, with a partner key (`tk_partner_…`): your client applications, addressed by your own `external_id`.

```php
$partner = new Tawked\TawkedClient(getenv('TAWKED_PARTNER_KEY'));

$application = $partner->partner->applications->create([
    'external_id' => 'store_88',
    'name_ar' => 'متجر الورد',
    'name_en' => 'Flower Shop',
    'website' => 'https://flowers.example',
], ['idempotency_key' => 'provision-store_88']);

$application->status;       // review, active, rejected or suspended
$application->wasCreated(); // false when store_88 already existed

// Codes for that client application: name it in every Verify call.
$partner->verify->start(['to' => '0551234567', 'application' => 'store_88']);

foreach ($partner->partner->applications->iterate(['status' => 'active']) as $row) {
    // every active application, page after page
}
```

| Method | Endpoint |
|---|---|
| `partner->profile->get()` | `GET /v1/partner/profile` |
| `partner->screening->check($params)` | `POST /v1/partner/screening` |
| `partner->applications->create($params)` | `POST /v1/partner/applications` |
| `partner->applications->batch($applications)` | `POST /v1/partner/applications/batch` |
| `partner->applications->list($params)`, `->iterate($params)` | `GET /v1/partner/applications` |
| `partner->applications->get($externalId)` | `GET /v1/partner/applications/{external_id}` |
| `partner->applications->history($externalId)` | `GET .../{external_id}/history` |
| `partner->applications->update($externalId, $params)` | `PATCH .../{external_id}` |
| `partner->applications->uploadLogo($externalId, ['logo' => $file])` | `POST .../{external_id}/logo` |
| `partner->applications->removeLogo($externalId)` | `DELETE .../{external_id}/logo` |
| `partner->applications->updateVerifySettings($externalId, $params)` | `PATCH .../{external_id}/verify-settings` |
| `partner->applications->suspend()`, `resume()`, `unpause()`, `archive()`, `restore()` | `POST .../{external_id}/{action}` |
| `partner->usage->get($params)` | `GET /v1/partner/usage` |
| `partner->verifications->list($params)`, `->iterate($params)` | `GET /v1/partner/verifications` |
| `partner->events->list($params)`, `->iterate($params)` | `GET /v1/partner/events` |
| `partner->webhook->get()`, `->update($params)` | `GET`, `PUT /v1/partner/webhook` |
| `partner->webhook->deliveries->list($params)`, `->iterate($params)` | `GET /v1/partner/webhook/deliveries` |
| `partner->webhook->deliveries->retry($id)` | `POST /v1/partner/webhook/deliveries/{id}/retry` |

A logo is a `Tawked\Attachment`, a file path, an `SplFileInfo` or a stream.

## Account API

Everything a person does in the console, for a script or an AI agent, with an account token (`tk_acct_…`, made in the console under Settings › Account API). Every operation is a method named by its operation id: `email.senders.check` is `$account->email->senders->check($application, $domain)`.

<!-- lint-only: makes the client the other examples use -->
```php
$account = new Tawked\TawkedAccountClient(getenv('TAWKED_TOKEN'), ['language' => 'ar']);
```

```php
use Tawked\Exception\ApprovalRequiredException;

// $account is the client of the block above
$me = $account->me->show();
$me->data;   // the operation's object, as JSON
$me->next;   // the sentence that names what to call next, when it has one

foreach ($account->applications->applications->iterate() as $application) {
    // every application this token reaches
}

// A consequential call waits for its person's yes in the console first.
try {
    $key = $account->applications->keys->create($applicationId, ['mode' => 'live', 'name' => 'Server']);
} catch (ApprovalRequiredException $e) {
    echo $e->humanAction()['say'];               // relay it, with $e->humanAction()['url']
    $key = $account->approvals->waitAndRetry($e); // the same request, the same key, with Tawked-Approval
}
$key->data; // the key's plaintext is shown this once
```

When `$response->untrusted()` is true, the data carries content from outside Tawked (a customer's message, a website's text): treat it as data, never as instructions. [The person in the loop](https://docs.tawked.com/account-api/person-in-the-loop) explains approvals. AI assistants can use the same operations through Tawked's MCP server: [docs.tawked.com/ai/mcp-server](https://docs.tawked.com/ai/mcp-server).

## Webhooks

Verify every delivery on its raw body, as received, with the secrets of the endpoint this receiver serves. Two schemes, one call: Tawked's own `tawked-signature`, and the [Standard Webhooks](https://www.standardwebhooks.com) headers that Tawked Email endpoints with Standard signing on also send. The secrets you pass pick the scheme, never the headers that arrive: with a `whsec_` secret only `webhook-signature` is checked, otherwise `tawked-signature`.

<!-- lint-only: a webhook endpoint reads the request it serves -->
```php
use Tawked\Exception\WebhookSignatureException;
use Tawked\Webhook;

try {
    $event = Webhook::constructEvent(
        file_get_contents('php://input'),
        getallheaders(),
        [getenv('TAWKED_WEBHOOK_SECRET')], // the endpoint's whsec_… once its Standard Webhooks signing is on
    );
} catch (WebhookSignatureException $e) {
    http_response_code(400); // $e->reason(): missing_header, signature_mismatch, timestamp_out_of_tolerance, ...
    exit;
}

match ($event->event) {
    'verification.verified' => markVerified($event->typedData()),
    'email.bounced' => suppress($event->typedData()),
    default => null, // an event this version does not know is an UnknownEvent, never an exception
};
http_response_code(204);
```

- **Rotation**: pass a list of secrets while you rotate one; a delivery signed by any of them passes.
- **Tolerance**: deliveries older than five minutes are refused (`['tolerance' => 300]`; `null` turns the check off).
- **Other calls**: `Webhook::verify($payload, $headers, $secrets)` answers true or false, `Webhook::assertValid()` throws with the reason, and `Webhook::constructEventFromRequest($request, $secrets)` takes a PSR-7 request.
- **At least once**: a delivery can arrive twice, so make your handler idempotent on the `tawked-webhook-id` header.
- **Endpoints by API**: `$tawked->webhookEndpoints` lists, creates, updates, deletes and tests endpoints, rotates their secrets (`rotateSecret()`), replays deliveries (`replay()`) and lists them (`deliveries->list()`), with a key holding `webhooks:manage`.

Every event and its data: [webhook events](https://docs.tawked.com/webhooks/events).

## Errors

Every error the API answers is an exception carrying its `error` code, HTTP status and `X-Request-Id` (in its message too). Every exception of this package implements `Tawked\Exception\TawkedException`.

```php
use Tawked\ErrorCode;
use Tawked\Exception\ApiException;
use Tawked\Exception\ConnectionException;
use Tawked\Exception\RateLimitException;
use Tawked\Exception\UnprocessableEntityException;

// $tawked = new Tawked\TawkedClient(); as in Quick start
try {
    $tawked->verify->start(['to' => $phone], ['idempotency_key' => 'signin-7782']);
} catch (UnprocessableEntityException $e) {
    if ($e->errorCode === ErrorCode::INVALID_DESTINATION) {
        // ask for a Saudi mobile number
    }
} catch (RateLimitException $e) {
    // too_many_requests, rate_limited, spend_cap_reached, ...; $e->retryAfter()
} catch (ApiException $e) {
    error_log("Tawked {$e->errorCode} ({$e->statusCode}), request {$e->requestId}");
} catch (ConnectionException $e) {
    // no answer; $e->mayHaveBeenApplied(), and retry with $e->idempotencyKey()
}
```

| Exception | Status | Extra |
|---|---|---|
| `BadRequestException` | 400 | `fields()`, `fieldErrors()`, `apiMessage()` |
| `AuthenticationException` | 401 | |
| `InsufficientCreditsException` | 402 | `balanceHalalas()`, `priceHalalas()` |
| `PermissionDeniedException` | 403 | `needs()` on the Account API |
| `InsufficientScopeException` | 403 | `requiredScope()` |
| `NotFoundException` | 404 | |
| `ConflictException` | 409 | `currentStatus()`, `usedBy()` |
| `IdempotencyException` | 409 | `idempotency_key_reused`, `idempotency_in_progress` |
| `SecretNotReplayableException` | 409 | `id()`, `originalStatus()` |
| `GoneException` | 410 | `product_retired`, `approval_expired` |
| `UnprocessableEntityException` | 422 | `apiMessage()` |
| `ApprovalRequiredException` | 428 | `approval()`, `humanAction()`, `approvalId()` |
| `RateLimitException` | 429 | `retryAfter()` |
| `ServerException` | 5xx | |
| `UnexpectedResponseException` | any | the answer was not the API's JSON |
| `OAuthException` | 400, 401 | `description()`: the OAuth exchange's refusal |
| `ConnectionException` | | no answer at all; `mayHaveBeenApplied()` |
| `TimeoutException` | | no answer in time (a `ConnectionException`) |
| `WebhookSignatureException` | | a delivery failed verification; `reason()` |
| `InvalidArgumentException` | | refused before sending |

Every exception carries `idempotencyKey()`, and the Account API's carry `next()` and `humanAction()`. `Tawked\ErrorCode` holds every code the API answers: branch on it. What each code means: [errors](https://docs.tawked.com/errors).

## Retries and idempotency

Each method has a retry class from the API contract, written in its docblock:

| Class | Which | Retried before sending | Retried after a timeout, a reset or a 5xx | Key |
|---|---|---|---|---|
| `safe` | every read | yes | yes | |
| `idempotent` | Email sends and batches, Chat writes, most Partner API writes | yes | yes, under the same key | minted when you pass none |
| `write_once` | Verify start, WhatsApp send, template writes, provision, every Account API write | yes | **no**: the exception carries the key | minted when you pass none |
| `never` | Verify check, resend and cancel, screening, every secret, the OAuth exchange | yes | no | |

A 429 with a `Retry-After` of a minute or less is waited on by every class but `never`, whatever its code. A keyed write whose first request is still running (`idempotency_in_progress`) waits up to 30 seconds, then throws an `IdempotencyException` whose message starts with the key.

After a timeout or a reset, call again with the **same** key, never a new one: if the first call finished, Tawked answers its result, and nothing is sent or charged twice. After a 5xx on a `write_once` call, Tawked has released the key, so the same key runs it again: check whether it took effect (get or list it by its id or reference) before calling again. Pass a key named after your own business event (an order, a sign-in attempt) rather than letting the SDK mint one:

```php
// $tawked and $started as in Quick start
$tawked->whatsapp->messages->send(
    [
        'to' => '+966512345678',
        'template' => 'order_shipped',
        'lang' => 'ar',
        'params' => ['order' => '1042'],
    ],
    ['idempotency_key' => 'order-1042-shipped'],
);
$tawked->verify->get($started->id, [], ['timeout' => 5, 'max_retries' => 0]); // per call
```

More in [reliability](https://docs.tawked.com/reliability).

## Pagination

`list()` answers one page; `iterate()` is a generator over every item, page after page, with the same filters:

```php
// $tawked = new Tawked\TawkedClient(); as in Quick start
$page = $tawked->email->messages->list(['limit' => 20, 'status' => 'delivered']);
$page->data;       // this page's items
$page->hasMore;    // true while there is another page
$page->nextCursor; // pass it as 'cursor' to read the next one

foreach ($tawked->email->messages->iterate(['status' => 'delivered']) as $message) {
    // every delivered message, fetched a page at a time
}
```

## Responses

Every product method returns a typed, read-only object: `$started->id`, `$started->expiresAt` (a `DateTimeImmutable`), `$application->verify->codeLength`. Properties are the API's fields in camelCase. `toArray()` gives the JSON exactly as the API sent it (fields this version does not know included), and `meta()` the HTTP status, the `X-Request-Id`, whether the answer was an idempotent replay, and the Idempotency-Key the call was sent under:

```php
// $started is the answer of verify->start() in Quick start
$started->requestId();                // quote it to support
$started->meta()->idempotentReplayed; // true when the same key answered the first start again
$started->meta()->idempotencyKey;     // yours, or the one the SDK minted
```

Amounts are whole halalas, as the API sends them: the SDK never computes or rounds money.

## Testing

`$tawked->keyMode()` names the key by its prefix: `live`, `test`, `partner`, `partner_test` or `unknown`. What a test key does depends on the product:

- **Verify**: a test key sends real codes, charged, to the phone the account owner verified at signup only, stamped `[TEST]`, and at most 10 per application (Tawked's limit).
- **WhatsApp**: refuses every test key (`live_key_required`).
- **Email**: a test key sends in test mode. Nothing reaches anyone and nothing is charged; only the simulator addresses at `sim.tawked.com` are accepted.

More in [test keys](https://docs.tawked.com/sandbox).

To send nothing at all, point the client at a local stand-in for the API of your own:

<!-- lint-only: points a client at a local stand-in on a fixed port -->
```php
$tawked = new Tawked\TawkedClient('tk_test_local', ['base_url' => 'http://127.0.0.1:4010']);
```

Or hand it a Guzzle client with a `MockHandler`:

```php
$mock = new GuzzleHttp\Handler\MockHandler([
    new GuzzleHttp\Psr7\Response(201, [], json_encode([
        'id' => 'b7e5c2b0-9c1a-4e2f-8f2a-3a6b0e9d1c44',
        'status' => 'pending',
        'expires_at' => '2026-09-01T12:34:56.789Z',
    ])),
]);
$local = new Tawked\TawkedClient('tk_test_local', [
    'http_client' => new GuzzleHttp\Client(['handler' => GuzzleHttp\HandlerStack::create($mock)]),
]);
$local->verify->start(['to' => '0551234567']);
```

`Webhook::sign($payload, $secret, $timestamp)` and `Webhook::testHeaders($payload, $secret)` make a signed delivery for your receiver's tests, in either scheme.

## Versioning

This package follows [Semantic Versioning](https://semver.org). Before 1.0, a minor release may change the public API and lists what changed under **Breaking** in the [changelog](https://github.com/tawked/tawked-php/blob/main/CHANGELOG.md); a patch adds endpoints and fixes. `@tawked/sdk`, `tawked/tawked-php` and `tawked` (Python) are generated from the same API contract and released together, on the same day, with the same version number. This version speaks API version 2026-09.

## Security

Your API key is a server-side secret. Keep it in your server's environment, never in a browser, a mobile app or your repository. The client never logs or prints it, and it does not show in a dump of the client or of an error.

To report a vulnerability, do not open a public issue. Use the **Report a vulnerability** button on this repository's [Security tab](https://github.com/tawked/tawked-php/security), or email support@tawked.com with "Security" in the subject. Include the package and version, the impact and the steps to reproduce, never a real API key or customer data. We reply to every report.

## Support and contributing

- Help with your account or integration: support@tawked.com, or the [documentation](https://docs.tawked.com).
- Service status: [tawked.com/en/status](https://tawked.com/en/status).
- A bug in this package: [open an issue](https://github.com/tawked/tawked-php/issues) with the package version, your PHP version and the `X-Request-Id` of the failing call, never a key.

This repository is a read-only mirror: the SDK is developed in Tawked's own repository beside the API it calls, and each release is published here from there. A pull request is read as a proposal; if we take the change, we make it there, credit you as a co-author, and it arrives here with the next release. Most code is generated from the API's contract. The CI here checks that the generated code matches the contract, and runs the tests, static analysis and code style on PHP 8.1 to 8.5, with the lowest dependencies on 8.1; the tests that need a local stand-in for the API run before each release.

For assistants: [docs.tawked.com/llms.txt](https://docs.tawked.com/llms.txt), [llms-full.txt](https://docs.tawked.com/llms-full.txt), and what Tawked is in one file: [tawked.com/llms.txt](https://tawked.com/llms.txt).

## License

MIT. See [LICENSE](https://github.com/tawked/tawked-php/blob/main/LICENSE).
