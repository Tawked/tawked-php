# Changelog

`tawked/tawked-php` is the PHP client for Tawked (PHP 8.1+, any framework): Verify, WhatsApp, Tawked
Email, Chat for connected apps, the Partner API, the Account API, connected apps' OAuth and webhook
signatures. All notable changes are recorded here. The package follows
[Semantic Versioning](https://semver.org); before 1.0 a minor may break.

## 0.1.0 - 2026-10-21

The first release, for API version 2026-09, covering every Tawked product. Versioned in lockstep
with `@tawked/sdk` and the Python `tawked`.

### Every product

- `Tawked\TawkedClient` with `verify` (start, check, get, resend, cancel), `whatsapp` (messages
  send and get; templates list, get, create, update, delete), `email` (messages send, sendBatch,
  list, iterate, get, update, cancel; checkKey), `chat` (menus list, create, get, update, delete;
  attendant get, saveDraft, publish, setState, updateSettings) and `partner` (profile, screening,
  applications, usage, verifications, events, webhook and its deliveries).
- `Tawked\TawkedAccountClient`: every operation of the Account API (`tk_acct_` tokens), named by
  its operation id (`$account->email->senders->check($application, $domain)`), answers as
  `AccountResponse` (`data`, `next`, `humanAction`, `untrusted()`) and `AccountPage`, the 428 loop
  through `$account->approvals->waitAndRetry($e)` (the same request, the same key, with
  `Tawked-Approval`), and `language` sent as `Accept-Language`.
- `Tawked\OAuth\Apps`: `authorizeUrl()` (PKCE S256, a fresh state, no network call) and
  `exchangeCode()` for connected apps (Client Secret Basic or post, never retried, 120 s).
- The template calls: `get` for an application's live key; `create`, `update` and `delete` for a
  connected app's key alone (a business's own key throws an `InsufficientScopeException` naming
  `notifications:templates`).

### Generated from the contract

- Every service, model, error code and webhook event is generated from Tawked's API contract,
  including the models `Tawked\Model\Email\*`, `Tawked\Model\Chat\*`, `Tawked\Model\OAuth\*`,
  `Webhook\EmailEventData` and `Webhook\EmailRecipientEventData`.
- `Tawked\Contract`: the API version, the key prefixes, what a test key does (`SANDBOX_NOTES`), the
  limits checked before sending and the e-mail headers the server refuses.
- `Tawked\Webhook\Events`: every webhook event and the class of its data.

### Retries, idempotency and timeouts

- Each method follows its retry class from the contract: `safe` (reads), `idempotent` (the server
  keeps the key through a failure: retried under the same key), `write_once` (retried only when
  the request never left, or while the first request with the key is still running) and `never`.
  A Verify start, a WhatsApp send, a template write and a provision are `write_once`: after a
  timeout or a 5xx they are not sent again, even under a key; the exception names the key first.
- An `idempotent` or `write_once` call with no `idempotency_key` gets one minted per call and kept
  for every attempt; it is on `meta()->idempotencyKey` and on every exception
  (`idempotencyKey()`).
- `idempotency_in_progress` is waited on for up to 30 s (1, 2, 4, then every 5 seconds;
  `in_progress_wait`), then an `IdempotencyException` opens on the key.
- A 429 with `Retry-After` (seconds or an HTTP date) of 60 s or less is waited on by every class
  but `never`, whatever its code; a longer one, and a business limit (no `Retry-After`), fail at
  once.
- Each call's own timeout: 30 s an attempt, 120 s for a batch, a secret and the OAuth exchange
  (the `timeout` option overrides it). Email takes keys up to 256 characters, the rest 128.
- The User-Agent is `tawked-php/<version> (php/<version>; api 2026-09)`.

### Errors

- Typed exceptions by the code's documented status, `Tawked\ErrorCode` with every code, and
  `TimeoutException` (a `ConnectionException`), `SecretNotReplayableException` (`id()`,
  `originalStatus()`), `GoneException` (410), `ApprovalRequiredException` (428), `OAuthException`.
- `ApiException` has `status()`, `code()`, `idempotencyKey()`, `retryAfter()`, `requiredScope()`,
  `resourceStatus()`, and on the Account API `next()`, `humanAction()`, `approval()`, `needs()`,
  `didYouMean()`; `ConnectionException` has `idempotencyKey()` and `mayHaveBeenApplied()`.
- `fieldErrors()`, `usedBy()` and `currentStatus()` for the template writes' refusals.

### Webhooks

- Both schemes in one call: `tawked-signature` and Standard Webhooks (a `whsec_` secret, used by
  Tawked Email webhook endpoints with Standard signing on). The secret decides the scheme; a list
  of secrets for a rotation.
- `Webhook::verify($payload, $headers, $secrets, $options)` answers true or false;
  `assertValid()` throws a `WebhookSignatureException` with `reason()`; `constructEvent()` answers a
  `WebhookEvent` whose `typedData()` is typed by the event, or an `UnknownEvent` for an event this
  version does not know. `sign($payload, $secret, $timestamp, $options)` and `testHeaders()` for a
  receiver's tests.

### Also

- `Tawked\Attachment` (a path, bytes or a stream) for e-mail attachments and multipart uploads;
  refused above 10 MiB before sending.
- `keyMode()` names the key by its prefix (no "is test" boolean: a test key sends real, charged
  messages to the owner's own number).
- Typed, read-only response objects with `toArray()` and `meta()`; Guzzle 7 or 8 by default, or
  any PSR-18 client. The key never shows in a dump of the client or of an error.
