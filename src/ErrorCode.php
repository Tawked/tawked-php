<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked;

/**
 * Every `error` code the Tawked API answers, with its HTTP status. Compare them with
 * `$exception->errorCode`. Generated from the API contract (sdks/contract/api.json).
 */
final class ErrorCode
{
    /** HTTP 400. */
    public const INVALID_JSON = 'invalid_json';

    /** HTTP 400. */
    public const INVALID_REQUEST = 'invalid_request';

    /** HTTP 400. Partner key used with no `application` (or `service`) in the request body. */
    public const SERVICE_REQUIRED = 'service_required';

    /** HTTP 401. */
    public const KEY_EXPIRED = 'key_expired';

    /** HTTP 401. */
    public const UNAUTHORIZED = 'unauthorized';

    /** HTTP 402. */
    public const INSUFFICIENT_CREDITS = 'insufficient_credits';

    /** HTTP 403. The application is not approved yet, the account is banned, or a partner disabled this application. All four causes share this one response. */
    public const ACCOUNT_NOT_ACTIVE = 'account_not_active';

    /** HTTP 403. */
    public const APPLICATION_KEY_REQUIRED = 'application_key_required';

    /** HTTP 403. The application is auto-paused by the pumping guard (a spike in distinct new destinations within an hour). The owner or an admin must resume it. */
    public const APPLICATION_PAUSED = 'application_paused';

    /** HTTP 403. */
    public const EMAIL_EARLY_ACCESS = 'email_early_access';

    /** HTTP 403. */
    public const INSUFFICIENT_SCOPE = 'insufficient_scope';

    /** HTTP 403. */
    public const IP_NOT_ALLOWED = 'ip_not_allowed';

    /** HTTP 403. A sending key limited to one domain sent from another (the message names the domain), or its domain was removed: it sends from that domain alone. */
    public const KEY_DOMAIN_MISMATCH = 'key_domain_mismatch';

    /** HTTP 403. */
    public const LIVE_KEY_REQUIRED = 'live_key_required';

    /** HTTP 403. */
    public const PARTNER_SUSPENDED = 'partner_suspended';

    /** HTTP 403. Only Tawked's team lifts this address: it complained again after a lift, the team suppressed it, or another application of the account did. */
    public const SUPPRESSION_TEAM_ONLY = 'suppression_team_only';

    /** HTTP 403. */
    public const TEMPLATE_NOT_MANAGED = 'template_not_managed';

    /** HTTP 403. A live key on the simulated receive: a live key receives real mail at its addresses. Call it with the application's test key (tk_test_). */
    public const TEST_KEY_REQUIRED = 'test_key_required';

    /** HTTP 403. WhatsApp is turned off for this application. Message support if you think this is a mistake. */
    public const WHATSAPP_NOT_ENABLED = 'whatsapp_not_enabled';

    /** HTTP 404. The application keeps no copy of this message: turn previews on in the console (Email → Settings); a message sent before that has none. */
    public const CONTENT_NOT_KEPT = 'content_not_kept';

    /** HTTP 404. */
    public const NOT_FOUND = 'not_found';

    /** HTTP 404. Partner key, but `application`/`service` does not match any of that partner's provisioned applications. */
    public const SERVICE_NOT_FOUND = 'service_not_found';

    /** HTTP 409. The application is archived. Restore it first; reads still work. */
    public const APPLICATION_ARCHIVED = 'application_archived';

    /** HTTP 409. No draft was saved yet: there is nothing to publish. Save one with `PUT /v1/chat/attendant/draft` first. */
    public const ATTENDANT_NO_DRAFT = 'attendant_no_draft';

    /** HTTP 409. Switched on before any publish. Publish a menu first. */
    public const ATTENDANT_UNPUBLISHED = 'attendant_unpublished';

    /** HTTP 409. */
    public const CHAT_NOT_READY = 'chat_not_ready';

    /** HTTP 409. The received email this delivery carried was deleted: the delivery keeps its status but not its words, and is not sent again. */
    public const DELIVERY_ERASED = 'delivery_erased';

    /** HTTP 409. The delivery is still being retried: only a final one (delivered or failed) is sent again. */
    public const DELIVERY_PENDING = 'delivery_pending';

    /** HTTP 409. The application holds its most domains: remove one first. */
    public const DOMAIN_LIMIT = 'domain_limit';

    /** HTTP 409. The domain is not verified yet: its defaults and its tracking wait until it is. */
    public const DOMAIN_NOT_VERIFIED = 'domain_not_verified';

    /** HTTP 409. Another application holds this domain, verified. */
    public const DOMAIN_TAKEN = 'domain_taken';

    /** HTTP 409. The account's request for Email waits for Tawked's team. */
    public const EMAIL_ACCESS_PENDING = 'email_access_pending';

    /** HTTP 409. The account has not asked for Email yet: ask from the application's Email area in the console. */
    public const EMAIL_ACCESS_REQUIRED = 'email_access_required';

    /** HTTP 409. The application's Email product is off: turn it on in the console. */
    public const EMAIL_PRODUCT_OFF = 'email_product_off';

    /** HTTP 409. The application has five endpoints, the most it may have: delete one first. */
    public const ENDPOINT_LIMIT_REACHED = 'endpoint_limit_reached';

    /** HTTP 409. The endpoint is off: turn it on first. */
    public const ENDPOINT_OFF = 'endpoint_off';

    /** HTTP 409. */
    public const IDEMPOTENCY_IN_PROGRESS = 'idempotency_in_progress';

    /** HTTP 409. */
    public const IDEMPOTENCY_KEY_REUSED = 'idempotency_key_reused';

    /** HTTP 409. This Idempotency-Key already made a secret, which is shown once and never again: the endpoint or the rotation exists; read it, or rotate again with a new key. */
    public const IDEMPOTENCY_SECRET_NOT_REPLAYABLE = 'idempotency_secret_not_replayable';

    /** HTTP 409. The message is no longer `scheduled`: it was sent, failed or canceled. The body adds `status`, its status now. Nothing changed. */
    public const MESSAGE_NOT_SCHEDULED = 'message_not_scheduled';

    /** HTTP 409. The application has no verified sender domain yet (or does not have Tawked Email). Add one in the application's **Email** area and add its one TXT record. */
    public const NO_VERIFIED_DOMAIN = 'no_verified_domain';

    /** HTTP 409. */
    public const NO_WHATSAPP_NUMBER = 'no_whatsapp_number';

    /** HTTP 409. The verification is already in a terminal state. The response includes the current `status`. */
    public const NOT_CANCELABLE = 'not_cancelable';

    /** HTTP 409. The verification is already in a terminal state (verified, failed, expired or canceled). The response includes the current `status`. */
    public const NOT_RESENDABLE = 'not_resendable';

    /** HTTP 409. Only a failed delivery can be retried; `status` says what this one is (`pending` runs on its own, `delivered` is done). */
    public const NOT_RETRYABLE = 'not_retryable';

    /** HTTP 409. A WhatsApp number is attached to the application, so it cannot be archived: the inbox belongs to the team that runs it. Disconnect the number first. */
    public const NUMBER_CONNECTED = 'number_connected';

    /** HTTP 409. */
    public const NUMBER_DISCONNECTED = 'number_disconnected';

    /** HTTP 409. The received e-mail is still being read (`status` `received`). Forward it once it is `ready`, a few seconds later. */
    public const RECEIVED_NOT_READY = 'received_not_ready';

    /** HTTP 409. A replay of this endpoint is still running: one at a time. */
    public const REPLAY_RUNNING = 'replay_running';

    /** HTTP 409. */
    public const SENDING_BLOCKED = 'sending_blocked';

    /** HTTP 409. An import of this application is still running: one at a time. */
    public const SUPPRESSION_IMPORT_RUNNING = 'suppression_import_running';

    /** HTTP 409. Another template of the application has this alias. */
    public const TEMPLATE_ALIAS_TAKEN = 'template_alias_taken';

    /** HTTP 409. */
    public const TEMPLATE_IN_USE = 'template_in_use';

    /** HTTP 409. The application has 100 templates, the most it may have (Tawked's limit). */
    public const TEMPLATE_LIMIT = 'template_limit';

    /** HTTP 409. The template has no draft to publish. */
    public const TEMPLATE_NO_DRAFT = 'template_no_draft';

    /** HTTP 409. */
    public const TEMPLATE_NOT_EDITABLE = 'template_not_editable';

    /** HTTP 409. The tracking subdomain already has records that are not the tracking edge's: choose another label. */
    public const TRACKING_SUBDOMAIN_TAKEN = 'tracking_subdomain_taken';

    /** HTTP 409. Open and click tracking cannot be switched on yet. */
    public const TRACKING_UNAVAILABLE = 'tracking_unavailable';

    /** HTTP 410. The stored copy behind the raw message, the attachment or the bodies is gone, so it cannot be downloaded or forwarded. The e-mail's details stay readable. */
    public const RECEIVED_CONTENT_GONE = 'received_content_gone';

    /** HTTP 413. A forward carries at most 10 attachments and 10 MiB in all, a Tawked limit. Download the files and send them yourself. */
    public const FORWARD_TOO_LARGE = 'forward_too_large';

    /** HTTP 413. `html` and `text` together are larger than 512 KiB. */
    public const MESSAGE_TOO_LARGE = 'message_too_large';

    /** HTTP 413. */
    public const REQUEST_TOO_LARGE = 'request_too_large';

    /** HTTP 422. */
    public const ATTACHMENT_INVALID = 'attachment_invalid';

    /** HTTP 422. */
    public const ATTACHMENT_TYPE_BLOCKED = 'attachment_type_blocked';

    /** HTTP 422. The attachments together are larger than 10 MiB once decoded. */
    public const ATTACHMENTS_TOO_LARGE = 'attachments_too_large';

    /** HTTP 422. The draft has problems; `problems` lists them as `{ code, choice_id, field, max }`. Nothing was published. Fix the draft and publish again. */
    public const ATTENDANT_NOT_READY = 'attendant_not_ready';

    /** HTTP 422. */
    public const BATCH_INVALID = 'batch_invalid';

    /** HTTP 422. */
    public const CONSENT_OPTED_OUT = 'consent_opted_out';

    /** HTTP 422. */
    public const CONTENT_BLOCKED = 'content_blocked';

    /** HTTP 422. The address carries a user name or a password. */
    public const CREDENTIALS = 'credentials';

    /** HTTP 422. Not a domain name. */
    public const DOMAIN_INVALID = 'domain_invalid';

    /** HTTP 422. A public mailbox's domain (gmail.com, outlook.com and the like) cannot send: use the business's own domain. */
    public const DOMAIN_PUBLIC = 'domain_public';

    /** HTTP 422. Tawked's own domains cannot be added. */
    public const DOMAIN_TAWKED = 'domain_tawked';

    /** HTTP 422. The e-mail carries an attachment that could run a program (`risky`); it is never forwarded. */
    public const FORWARD_RISKY_ATTACHMENT = 'forward_risky_attachment';

    /** HTTP 422. */
    public const HEADER_NOT_ALLOWED = 'header_not_allowed';

    /** HTTP 422. */
    public const INVALID_DESTINATION = 'invalid_destination';

    /** HTTP 422. The address has no valid host name. */
    public const INVALID_HOST = 'invalid_host';

    /** HTTP 422. */
    public const INVALID_PARAMS = 'invalid_params';

    /** HTTP 422. The address is an IP address: use a host name. */
    public const IP_ADDRESS = 'ip_address';

    /** HTTP 422. The application already has 10 menus, counting the client's own and every app's. `limit` says how many. Nothing was added. */
    public const MENU_LIMIT_REACHED = 'menu_limit_reached';

    /** HTTP 422. The address is not https. */
    public const NOT_HTTPS = 'not_https';

    /** HTTP 422. The host is private or local, unreachable from the internet. */
    public const PRIVATE_HOST = 'private_host';

    /** HTTP 422. */
    public const RECIPIENT_SUPPRESSED = 'recipient_suppressed';

    /** HTTP 422. A sandbox (tk_test_) key on a channel that cannot carry the [TEST] stamp (WhatsApp's fixed Meta template has no room for it). */
    public const SANDBOX_UNSUPPORTED_CHANNEL = 'sandbox_unsupported_channel';

    /** HTTP 422. Sandbox key, and the destination is not the phone the account owner verified at signup. */
    public const SANDBOX_UNVERIFIED_DESTINATION = 'sandbox_unverified_destination';

    /** HTTP 422. `from.address` is not on a verified domain of this application: another domain, a domain still pending, or one whose records are gone. */
    public const SENDER_NOT_VERIFIED = 'sender_not_verified';

    /** HTTP 422. The file is not a CSV file Tawked can read. */
    public const SUPPRESSION_IMPORT_INVALID_FILE = 'suppression_import_invalid_file';

    /** HTTP 422. The file is larger than an import takes. */
    public const SUPPRESSION_IMPORT_TOO_LARGE = 'suppression_import_too_large';

    /** HTTP 422. No address given is valid, or `addresses` is not a list. */
    public const SUPPRESSION_INVALID_ADDRESSES = 'suppression_invalid_addresses';

    /** HTTP 422. A complaint is lifted with a reason: `recipient_asked`, `reported_by_mistake` or `other` with a note. */
    public const SUPPRESSION_LIFT_REASON_REQUIRED = 'suppression_lift_reason_required';

    /** HTTP 422. More than 1000 addresses in one call (Tawked's limit). */
    public const SUPPRESSION_TOO_MANY = 'suppression_too_many';

    /** HTTP 422. The alias is not lowercase letters, digits, `-` and `_`, up to 64, or it is shaped like a uuid. */
    public const TEMPLATE_ALIAS_INVALID = 'template_alias_invalid';

    /** HTTP 422. */
    public const TEMPLATE_CATEGORY_NOT_ALLOWED = 'template_category_not_allowed';

    /** HTTP 422. */
    public const TEMPLATE_INVALID = 'template_invalid';

    /** HTTP 422. The template exists but Meta has not approved it; `message` carries Meta's status (PENDING, REJECTED, PAUSED, …). */
    public const TEMPLATE_NOT_APPROVED = 'template_not_approved';

    /** HTTP 422. No template of the application has this id or alias (`field: "template.id"`). */
    public const TEMPLATE_NOT_FOUND = 'template_not_found';

    /** HTTP 422. The template has never been published: publish it, or send its draft with a test key (`"version": "draft"`). */
    public const TEMPLATE_NOT_PUBLISHED = 'template_not_published';

    /** HTTP 422. */
    public const TEMPLATE_REFUSED = 'template_refused';

    /** HTTP 422. A value that is not a string or a number, or longer than 2000 characters. */
    public const TEMPLATE_VARIABLE_INVALID = 'template_variable_invalid';

    /** HTTP 422. A variable the template needs has no value and no fallback (`field: "template.variables.<KEY>"`). */
    public const TEMPLATE_VARIABLE_MISSING = 'template_variable_missing';

    /** HTTP 422. A variable the template does not declare. */
    public const TEMPLATE_VARIABLE_UNKNOWN = 'template_variable_unknown';

    /** HTTP 422. `template.variables` is not an object (over SMTP: `550 5.7.1 template_variables_invalid`). */
    public const TEMPLATE_VARIABLES_INVALID = 'template_variables_invalid';

    /** HTTP 422. */
    public const TEST_MODE_RECIPIENT = 'test_mode_recipient';

    /** HTTP 422. The tracking subdomain is not a DNS label (letters, digits and `-`, up to 63), or it is the return path's. */
    public const TRACKING_SUBDOMAIN_INVALID = 'tracking_subdomain_invalid';

    /** HTTP 422. No template with that name (and language, when given) on the number's WhatsApp Business Account. `GET /v1/whatsapp/templates` lists what exists. */
    public const UNKNOWN_TEMPLATE = 'unknown_template';

    /** HTTP 422. */
    public const UNSUPPORTED_CHANNEL = 'unsupported_channel';

    /** HTTP 422. */
    public const UNSUPPORTED_TEMPLATE = 'unsupported_template';

    /** HTTP 422. An event the application's products do not send, or the endpoint does not receive (`field`, and `field_index` in a list). */
    public const WEBHOOK_EVENT_UNKNOWN = 'webhook_event_unknown';

    /** HTTP 429. */
    public const DAILY_CAP_REACHED = 'daily_cap_reached';

    /** HTTP 429. The caller passed `client_ip`, and that address sent more starts in the last hour than the application's `max_per_ip_per_hour` allows. */
    public const IP_RATE_LIMITED = 'ip_rate_limited';

    /** HTTP 429. */
    public const RATE_LIMITED = 'rate_limited';

    /** HTTP 429. This verification already used its 3 resends. */
    public const RESEND_LIMIT_REACHED = 'resend_limit_reached';

    /** HTTP 429. The application's lifetime sandbox send cap (10) is reached. */
    public const SANDBOX_QUOTA_EXCEEDED = 'sandbox_quota_exceeded';

    /** HTTP 429. Today's charges (Riyadh calendar day) plus this send would pass the application's `daily_spend_cap_halalas`. */
    public const SPEND_CAP_REACHED = 'spend_cap_reached';

    /** HTTP 429. Ten imports a day for an application (Tawked's limit). */
    public const SUPPRESSION_IMPORT_DAILY = 'suppression_import_daily';

    /** HTTP 429. Test mode sends at most 1000 e-mails a day for an application (Tawked's limit). It starts again at midnight, Riyadh time. */
    public const TEST_MODE_LIMIT_REACHED = 'test_mode_limit_reached';

    /** HTTP 429. */
    public const TOO_MANY_REQUESTS = 'too_many_requests';

    /** HTTP 500. */
    public const INTERNAL_ERROR = 'internal_error';

    /** HTTP 502. The delivery provider call threw. The charge is refunded automatically. */
    public const SEND_FAILED = 'send_failed';

    /** HTTP 503. */
    public const CHAT_UNAVAILABLE = 'chat_unavailable';

    /** HTTP 503. */
    public const EMAIL_PAUSED = 'email_paused';

    /** HTTP 503. */
    public const EMAIL_PLATFORM_BUSY = 'email_platform_busy';

    /** HTTP 503. The sending service cannot be reached now: nothing changed; try again later. */
    public const EMAIL_UNAVAILABLE = 'email_unavailable';

    /** HTTP 503. Tawked Email is holding every new send for a while, a rare platform state. Nothing was recorded or charged; retry later. */
    public const SERVICE_UNAVAILABLE = 'service_unavailable';

    /** HTTP 503. The file store refused the write, so nothing was recorded. Retry shortly. */
    public const STORAGE_UNAVAILABLE = 'storage_unavailable';

    /** HTTP 503. */
    public const TEMPLATES_UNAVAILABLE = 'templates_unavailable';

    /** Not an API code: the response was not the API's JSON. */
    public const UNEXPECTED_RESPONSE = 'unexpected_response';

    /**
     * The HTTP status of each code.
     *
     * @var array<string, int>
     */
    public const STATUS = [
        self::INVALID_JSON => 400,
        self::INVALID_REQUEST => 400,
        self::SERVICE_REQUIRED => 400,
        self::KEY_EXPIRED => 401,
        self::UNAUTHORIZED => 401,
        self::INSUFFICIENT_CREDITS => 402,
        self::ACCOUNT_NOT_ACTIVE => 403,
        self::APPLICATION_KEY_REQUIRED => 403,
        self::APPLICATION_PAUSED => 403,
        self::EMAIL_EARLY_ACCESS => 403,
        self::INSUFFICIENT_SCOPE => 403,
        self::IP_NOT_ALLOWED => 403,
        self::KEY_DOMAIN_MISMATCH => 403,
        self::LIVE_KEY_REQUIRED => 403,
        self::PARTNER_SUSPENDED => 403,
        self::SUPPRESSION_TEAM_ONLY => 403,
        self::TEMPLATE_NOT_MANAGED => 403,
        self::TEST_KEY_REQUIRED => 403,
        self::WHATSAPP_NOT_ENABLED => 403,
        self::CONTENT_NOT_KEPT => 404,
        self::NOT_FOUND => 404,
        self::SERVICE_NOT_FOUND => 404,
        self::APPLICATION_ARCHIVED => 409,
        self::ATTENDANT_NO_DRAFT => 409,
        self::ATTENDANT_UNPUBLISHED => 409,
        self::CHAT_NOT_READY => 409,
        self::DELIVERY_ERASED => 409,
        self::DELIVERY_PENDING => 409,
        self::DOMAIN_LIMIT => 409,
        self::DOMAIN_NOT_VERIFIED => 409,
        self::DOMAIN_TAKEN => 409,
        self::EMAIL_ACCESS_PENDING => 409,
        self::EMAIL_ACCESS_REQUIRED => 409,
        self::EMAIL_PRODUCT_OFF => 409,
        self::ENDPOINT_LIMIT_REACHED => 409,
        self::ENDPOINT_OFF => 409,
        self::IDEMPOTENCY_IN_PROGRESS => 409,
        self::IDEMPOTENCY_KEY_REUSED => 409,
        self::IDEMPOTENCY_SECRET_NOT_REPLAYABLE => 409,
        self::MESSAGE_NOT_SCHEDULED => 409,
        self::NO_VERIFIED_DOMAIN => 409,
        self::NO_WHATSAPP_NUMBER => 409,
        self::NOT_CANCELABLE => 409,
        self::NOT_RESENDABLE => 409,
        self::NOT_RETRYABLE => 409,
        self::NUMBER_CONNECTED => 409,
        self::NUMBER_DISCONNECTED => 409,
        self::RECEIVED_NOT_READY => 409,
        self::REPLAY_RUNNING => 409,
        self::SENDING_BLOCKED => 409,
        self::SUPPRESSION_IMPORT_RUNNING => 409,
        self::TEMPLATE_ALIAS_TAKEN => 409,
        self::TEMPLATE_IN_USE => 409,
        self::TEMPLATE_LIMIT => 409,
        self::TEMPLATE_NO_DRAFT => 409,
        self::TEMPLATE_NOT_EDITABLE => 409,
        self::TRACKING_SUBDOMAIN_TAKEN => 409,
        self::TRACKING_UNAVAILABLE => 409,
        self::RECEIVED_CONTENT_GONE => 410,
        self::FORWARD_TOO_LARGE => 413,
        self::MESSAGE_TOO_LARGE => 413,
        self::REQUEST_TOO_LARGE => 413,
        self::ATTACHMENT_INVALID => 422,
        self::ATTACHMENT_TYPE_BLOCKED => 422,
        self::ATTACHMENTS_TOO_LARGE => 422,
        self::ATTENDANT_NOT_READY => 422,
        self::BATCH_INVALID => 422,
        self::CONSENT_OPTED_OUT => 422,
        self::CONTENT_BLOCKED => 422,
        self::CREDENTIALS => 422,
        self::DOMAIN_INVALID => 422,
        self::DOMAIN_PUBLIC => 422,
        self::DOMAIN_TAWKED => 422,
        self::FORWARD_RISKY_ATTACHMENT => 422,
        self::HEADER_NOT_ALLOWED => 422,
        self::INVALID_DESTINATION => 422,
        self::INVALID_HOST => 422,
        self::INVALID_PARAMS => 422,
        self::IP_ADDRESS => 422,
        self::MENU_LIMIT_REACHED => 422,
        self::NOT_HTTPS => 422,
        self::PRIVATE_HOST => 422,
        self::RECIPIENT_SUPPRESSED => 422,
        self::SANDBOX_UNSUPPORTED_CHANNEL => 422,
        self::SANDBOX_UNVERIFIED_DESTINATION => 422,
        self::SENDER_NOT_VERIFIED => 422,
        self::SUPPRESSION_IMPORT_INVALID_FILE => 422,
        self::SUPPRESSION_IMPORT_TOO_LARGE => 422,
        self::SUPPRESSION_INVALID_ADDRESSES => 422,
        self::SUPPRESSION_LIFT_REASON_REQUIRED => 422,
        self::SUPPRESSION_TOO_MANY => 422,
        self::TEMPLATE_ALIAS_INVALID => 422,
        self::TEMPLATE_CATEGORY_NOT_ALLOWED => 422,
        self::TEMPLATE_INVALID => 422,
        self::TEMPLATE_NOT_APPROVED => 422,
        self::TEMPLATE_NOT_FOUND => 422,
        self::TEMPLATE_NOT_PUBLISHED => 422,
        self::TEMPLATE_REFUSED => 422,
        self::TEMPLATE_VARIABLE_INVALID => 422,
        self::TEMPLATE_VARIABLE_MISSING => 422,
        self::TEMPLATE_VARIABLE_UNKNOWN => 422,
        self::TEMPLATE_VARIABLES_INVALID => 422,
        self::TEST_MODE_RECIPIENT => 422,
        self::TRACKING_SUBDOMAIN_INVALID => 422,
        self::UNKNOWN_TEMPLATE => 422,
        self::UNSUPPORTED_CHANNEL => 422,
        self::UNSUPPORTED_TEMPLATE => 422,
        self::WEBHOOK_EVENT_UNKNOWN => 422,
        self::DAILY_CAP_REACHED => 429,
        self::IP_RATE_LIMITED => 429,
        self::RATE_LIMITED => 429,
        self::RESEND_LIMIT_REACHED => 429,
        self::SANDBOX_QUOTA_EXCEEDED => 429,
        self::SPEND_CAP_REACHED => 429,
        self::SUPPRESSION_IMPORT_DAILY => 429,
        self::TEST_MODE_LIMIT_REACHED => 429,
        self::TOO_MANY_REQUESTS => 429,
        self::INTERNAL_ERROR => 500,
        self::SEND_FAILED => 502,
        self::CHAT_UNAVAILABLE => 503,
        self::EMAIL_PAUSED => 503,
        self::EMAIL_PLATFORM_BUSY => 503,
        self::EMAIL_UNAVAILABLE => 503,
        self::SERVICE_UNAVAILABLE => 503,
        self::STORAGE_UNAVAILABLE => 503,
        self::TEMPLATES_UNAVAILABLE => 503,
    ];
}
