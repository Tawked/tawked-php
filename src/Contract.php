<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked;

/**
 * The contract's constants: the API version, the key prefixes, what a test key does, the
 * limits an SDK checks before sending, and the e-mail headers the server refuses.
 */
final class Contract
{
    /** The API version the SDK was generated from (sent in the User-Agent; the API has no request-time versioning). */
    public const API_VERSION = '2026-09';

    /**
     * Each kind of key by its prefix, the longest first: The longest matching prefix wins (tk_partner_test_ before tk_partner_).
     *
     * @var array<string, string>
     */
    public const KEY_PREFIXES = [
        'partner_test' => 'tk_partner_test_',
        'partner' => 'tk_partner_',
        'account' => 'tk_acct_',
        'live' => 'tk_live_',
        'test' => 'tk_test_',
    ];

    /**
     * What a test key (tk_test_, tk_partner_test_) does, per the contract: it is not a sandbox that sends nothing.
     *
     * @var list<string>
     */
    public const SANDBOX_NOTES = [
        'Created by you, before application approval: on the application\'s API keys page (Create a key, type Test), or made for you by the first send from Verify → Test in the console.',
        'Calls the exact same endpoints with the exact same request/response shapes as a live key.',
        'Sends real messages, but only to one number: the phone the account owner verified at signup. No other destination can be added.',
        'Every message carries an unremovable [TEST]/[تجريبي] stamp.',
        'Charged like any other send, from the same prepaid balance, and capped at 10 lifetime sends per application so testing cannot consume the whole welcome credit.',
        'WhatsApp has no sandbox: `/v1/whatsapp/*` refuses every test key, with `403 live_key_required` (or `403 insufficient_scope` for a test key with custom access, which never holds Notifications).',
    ];

    /** Lifetime sends of a test key per application. */
    public const SANDBOX_SEND_CAP = 10;

    /**
     * Limits checked before sending.
     *
     * @var array<string, int>
     */
    public const LIMITS = [
        'email_attachments_max_bytes' => 10485760,
        'email_attachments_max_items' => 10,
        'email_batch_max_items' => 100,
        'partner_batch_max_items' => 100,
    ];

    /**
     * The custom e-mail headers the server refuses (422 header_not_allowed); exported for adapters that
     * warn early. The server's answer is the one truth; no SDK keeps an allow-list.
     *
     * @var list<string>
     */
    public const EMAIL_DENIED_HEADERS = [
        'from',
        'sender',
        'to',
        'cc',
        'bcc',
        'reply-to',
        'subject',
        'date',
        'message-id',
        'return-path',
        'mime-version',
        'content-type',
        'content-transfer-encoding',
        'content-disposition',
        'content-id',
        'content-description',
        'dkim-signature',
        'domainkey-signature',
        'authentication-results',
        'received',
        'received-spf',
        'feedback-id',
        'errors-to',
        'bounces-to',
    ];
}
