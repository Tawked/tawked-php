<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Webhook;

/**
 * Every webhook event of the contract, with the class of its `data`
 * (Tawked\Model\WebhookEvent::typedData() reads it). An event this version does not know
 * comes back as Tawked\Model\Webhook\UnknownEvent, never an exception.
 */
final class Events
{
    public const BALANCE_LOW = 'balance.low';

    public const DOMAIN_CREATED = 'domain.created';

    public const DOMAIN_FAILED = 'domain.failed';

    public const DOMAIN_REMOVED = 'domain.removed';

    public const DOMAIN_VERIFIED = 'domain.verified';

    public const EMAIL_BOUNCED = 'email.bounced';

    public const EMAIL_CANCELED = 'email.canceled';

    public const EMAIL_CLICKED = 'email.clicked';

    public const EMAIL_COMPLAINED = 'email.complained';

    public const EMAIL_DELIVERED = 'email.delivered';

    public const EMAIL_DELIVERY_DELAYED = 'email.delivery_delayed';

    public const EMAIL_FAILED = 'email.failed';

    public const EMAIL_OPENED = 'email.opened';

    public const EMAIL_RECEIVED = 'email.received';

    public const EMAIL_SCHEDULED = 'email.scheduled';

    public const EMAIL_SENT = 'email.sent';

    public const EMAIL_SUPPRESSED = 'email.suppressed';

    public const MESSAGE_DELIVERED = 'message.delivered';

    public const MESSAGE_FAILED = 'message.failed';

    public const MESSAGE_READ = 'message.read';

    public const MESSAGE_SENT = 'message.sent';

    public const SERVICE_APPROVED = 'service.approved';

    public const SERVICE_ARCHIVED = 'service.archived';

    public const SERVICE_PAUSED = 'service.paused';

    public const SERVICE_REJECTED = 'service.rejected';

    public const SERVICE_RESTORED = 'service.restored';

    public const SERVICE_RESUMED = 'service.resumed';

    public const SERVICE_REVIEW_REQUESTED = 'service.review_requested';

    public const SERVICE_SUSPENDED = 'service.suspended';

    public const SERVICE_UNPAUSED = 'service.unpaused';

    public const SUPPRESSION_ADDED = 'suppression.added';

    public const SUPPRESSION_REMOVED = 'suppression.removed';

    public const VERIFICATION_EXPIRED = 'verification.expired';

    public const VERIFICATION_FAILED = 'verification.failed';

    public const VERIFICATION_VERIFIED = 'verification.verified';

    /**
     * The data classes of each event, the most specific first (an e-mail event of a message of
     * several recipients carries `recipient`).
     *
     * @var array<string, list<class-string<\Tawked\Model\Model>>>
     */
    public const DATA = [
        self::BALANCE_LOW => [\Tawked\Model\Webhook\BalanceLowData::class],
        self::DOMAIN_CREATED => [\Tawked\Model\Webhook\DomainEventData::class],
        self::DOMAIN_FAILED => [\Tawked\Model\Webhook\DomainEventData::class],
        self::DOMAIN_REMOVED => [\Tawked\Model\Webhook\DomainEventData::class],
        self::DOMAIN_VERIFIED => [\Tawked\Model\Webhook\DomainEventData::class],
        self::EMAIL_BOUNCED => [\Tawked\Model\Webhook\EmailRecipientEventData::class, \Tawked\Model\Webhook\EmailEventData::class],
        self::EMAIL_CANCELED => [\Tawked\Model\Webhook\EmailRecipientEventData::class, \Tawked\Model\Webhook\EmailEventData::class],
        self::EMAIL_CLICKED => [\Tawked\Model\Webhook\EmailRecipientEventData::class, \Tawked\Model\Webhook\EmailEventData::class],
        self::EMAIL_COMPLAINED => [\Tawked\Model\Webhook\EmailRecipientEventData::class, \Tawked\Model\Webhook\EmailEventData::class],
        self::EMAIL_DELIVERED => [\Tawked\Model\Webhook\EmailRecipientEventData::class, \Tawked\Model\Webhook\EmailEventData::class],
        self::EMAIL_DELIVERY_DELAYED => [\Tawked\Model\Webhook\EmailRecipientEventData::class, \Tawked\Model\Webhook\EmailEventData::class],
        self::EMAIL_FAILED => [\Tawked\Model\Webhook\EmailRecipientEventData::class, \Tawked\Model\Webhook\EmailEventData::class],
        self::EMAIL_OPENED => [\Tawked\Model\Webhook\EmailRecipientEventData::class, \Tawked\Model\Webhook\EmailEventData::class],
        self::EMAIL_RECEIVED => [\Tawked\Model\Webhook\EmailReceivedEventData::class],
        self::EMAIL_SCHEDULED => [\Tawked\Model\Webhook\EmailRecipientEventData::class, \Tawked\Model\Webhook\EmailEventData::class],
        self::EMAIL_SENT => [\Tawked\Model\Webhook\EmailRecipientEventData::class, \Tawked\Model\Webhook\EmailEventData::class],
        self::EMAIL_SUPPRESSED => [\Tawked\Model\Webhook\EmailRecipientEventData::class, \Tawked\Model\Webhook\EmailEventData::class],
        self::MESSAGE_DELIVERED => [\Tawked\Model\Webhook\MessageEventData::class],
        self::MESSAGE_FAILED => [\Tawked\Model\Webhook\MessageEventData::class],
        self::MESSAGE_READ => [\Tawked\Model\Webhook\MessageEventData::class],
        self::MESSAGE_SENT => [\Tawked\Model\Webhook\MessageEventData::class],
        self::SERVICE_APPROVED => [\Tawked\Model\Webhook\ApplicationEventData::class],
        self::SERVICE_ARCHIVED => [\Tawked\Model\Webhook\ApplicationEventData::class],
        self::SERVICE_PAUSED => [\Tawked\Model\Webhook\ApplicationEventData::class],
        self::SERVICE_REJECTED => [\Tawked\Model\Webhook\ApplicationEventData::class],
        self::SERVICE_RESTORED => [\Tawked\Model\Webhook\ApplicationEventData::class],
        self::SERVICE_RESUMED => [\Tawked\Model\Webhook\ApplicationEventData::class],
        self::SERVICE_REVIEW_REQUESTED => [\Tawked\Model\Webhook\ApplicationEventData::class],
        self::SERVICE_SUSPENDED => [\Tawked\Model\Webhook\ApplicationEventData::class],
        self::SERVICE_UNPAUSED => [\Tawked\Model\Webhook\ApplicationEventData::class],
        self::SUPPRESSION_ADDED => [\Tawked\Model\Webhook\SuppressionAddedEventData::class],
        self::SUPPRESSION_REMOVED => [\Tawked\Model\Webhook\SuppressionRemovedEventData::class],
        self::VERIFICATION_EXPIRED => [\Tawked\Model\Webhook\VerificationEventData::class],
        self::VERIFICATION_FAILED => [\Tawked\Model\Webhook\VerificationEventData::class],
        self::VERIFICATION_VERIFIED => [\Tawked\Model\Webhook\VerificationEventData::class],
    ];

    /**
     * The fields each data class requires, to choose between two classes of one event.
     *
     * @var array<class-string<\Tawked\Model\Model>, list<string>>
     */
    public const REQUIRED = [
        \Tawked\Model\Webhook\ApplicationEventData::class => [
            'application_id',
            'external_id',
            'service_id',
        ],
        \Tawked\Model\Webhook\BalanceLowData::class => [
            'balance_halalas',
            'threshold_halalas',
        ],
        \Tawked\Model\Webhook\DomainEventData::class => [
            'authentication',
            'created_at',
            'defaults',
            'id',
            'name',
            'pending_until',
            'previous_state',
            'reason',
            'receiving',
            'records',
            'state',
            'tracking',
            'verified_at',
        ],
        \Tawked\Model\Webhook\EmailEventData::class => [
            'application_id',
            'bounce',
            'created_at',
            'error',
            'from',
            'id',
            'occurred_at',
            'price_halalas',
            'reference',
            'status',
            'subject',
            'to',
        ],
        \Tawked\Model\Webhook\EmailReceivedEventData::class => [
            'address',
            'attachments',
            'attachments_count',
            'authentication',
            'envelope_from',
            'from',
            'has_attachments',
            'id',
            'in_reply_to',
            'message_id',
            'parse',
            'received_at',
            'size',
            'spam',
            'status',
            'subject',
            'thread',
            'to',
        ],
        \Tawked\Model\Webhook\EmailRecipientEventData::class => [
            'application_id',
            'bounce',
            'created_at',
            'error',
            'from',
            'id',
            'occurred_at',
            'price_halalas',
            'recipient',
            'reference',
            'status',
            'subject',
            'tags',
            'to',
        ],
        \Tawked\Model\Webhook\MessageEventData::class => [
            'application_id',
            'created_at',
            'error',
            'id',
            'lang',
            'occurred_at',
            'price_halalas',
            'reference',
            'status',
            'template',
            'to',
        ],
        \Tawked\Model\Webhook\SuppressionAddedEventData::class => [
            'address',
            'application_id',
            'created_at',
            'email_id',
            'id',
            'reason',
            'source',
        ],
        \Tawked\Model\Webhook\SuppressionRemovedEventData::class => [
            'address',
            'application_id',
            'by',
            'id',
            'lift_reason',
            'reason',
            'removed_at',
        ],
        \Tawked\Model\Webhook\VerificationEventData::class => [
            'application_id',
            'attempts',
            'channel',
            'created_at',
            'expires_at',
            'id',
            'mode',
            'reference',
            'status',
            'to',
            'verified_at',
        ],
    ];

    /**
     * Which webhook sends each event: an application's, the partner account's, or both.
     *
     * @var array<string, string>
     */
    public const SIDE = [
        self::BALANCE_LOW => 'partner',
        self::DOMAIN_CREATED => 'application',
        self::DOMAIN_FAILED => 'application',
        self::DOMAIN_REMOVED => 'application',
        self::DOMAIN_VERIFIED => 'application',
        self::EMAIL_BOUNCED => 'application',
        self::EMAIL_CANCELED => 'application',
        self::EMAIL_CLICKED => 'application',
        self::EMAIL_COMPLAINED => 'application',
        self::EMAIL_DELIVERED => 'application',
        self::EMAIL_DELIVERY_DELAYED => 'application',
        self::EMAIL_FAILED => 'application',
        self::EMAIL_OPENED => 'application',
        self::EMAIL_RECEIVED => 'application',
        self::EMAIL_SCHEDULED => 'application',
        self::EMAIL_SENT => 'application',
        self::EMAIL_SUPPRESSED => 'application',
        self::MESSAGE_DELIVERED => 'application',
        self::MESSAGE_FAILED => 'application',
        self::MESSAGE_READ => 'application',
        self::MESSAGE_SENT => 'application',
        self::SERVICE_APPROVED => 'partner',
        self::SERVICE_ARCHIVED => 'partner',
        self::SERVICE_PAUSED => 'partner',
        self::SERVICE_REJECTED => 'partner',
        self::SERVICE_RESTORED => 'partner',
        self::SERVICE_RESUMED => 'partner',
        self::SERVICE_REVIEW_REQUESTED => 'partner',
        self::SERVICE_SUSPENDED => 'partner',
        self::SERVICE_UNPAUSED => 'partner',
        self::SUPPRESSION_ADDED => 'application',
        self::SUPPRESSION_REMOVED => 'application',
        self::VERIFICATION_EXPIRED => 'both',
        self::VERIFICATION_FAILED => 'both',
        self::VERIFICATION_VERIFIED => 'both',
    ];
}
