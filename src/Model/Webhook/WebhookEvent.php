<?php

declare(strict_types=1);

namespace Tawked\Model\Webhook;

use DateTimeImmutable;
use Tawked\Internal\Data;
use Tawked\Model\Model;
use Tawked\Webhook\Events;

/**
 * One verified webhook delivery: `{event, data, test?, sent_at}`, and on an Email-group event
 * (email.*, domain.*, suppression.*) `type`, `timestamp` and, on a test key's event, `mode`.
 *
 * Deliveries are at least once (a failed one sent again from the console is a new delivery
 * with the same event and data): make your handler idempotent on the `tawked-webhook-id` header,
 * or on the event and `data['id']` (and `data['recipient']['id']` on an e-mail event that has one).
 */
class WebhookEvent extends Model
{
    /**
     * @param array<string, mixed> $data the event's data, as sent
     */
    final public function __construct(
        /** Such as verification.verified, message.delivered, email.bounced, service.approved or balance.low. */
        public readonly string $event,
        public readonly array $data,
        /** True on a console test event and on Tawked's test message to a new receiving address; never on a customer's event. */
        public readonly bool $test,
        /** The `tawked-timestamp` header: milliseconds since the epoch, as a string. */
        public readonly string $timestamp,
        public readonly ?DateTimeImmutable $sentAt,
        /** The body's `type` (equals `event`), on Email-group events only. */
        public readonly ?string $type = null,
        /** The body's `timestamp`: when the event happened (ISO 8601, UTC), on Email-group events only. */
        public readonly ?string $occurredAt = null,
        /** `test` on a test key's simulated event (nothing was sent or received); null otherwise. */
        public readonly ?string $mode = null,
    ) {}

    /**
     * @param array<string, mixed> $body
     */
    public static function fromArray(array $body, ?string $timestamp = null): static
    {
        $timestamp ??= Data::string($body, 'sent_at');
        $sentAt = ctype_digit($timestamp)
            ? DateTimeImmutable::createFromFormat('U.v', intdiv((int) $timestamp, 1000).'.'.str_pad((string) ((int) $timestamp % 1000), 3, '0', STR_PAD_LEFT))
            : false;

        $event = new static(
            Data::string($body, 'event'),
            Data::object($body, 'data') ?? [],
            Data::bool($body, 'test'),
            $timestamp,
            $sentAt === false ? null : $sentAt,
            Data::nullableString($body, 'type'),
            Data::nullableString($body, 'timestamp'),
            Data::nullableString($body, 'mode'),
        );
        $event->raw = $body;

        return $event;
    }

    /**
     * Whether a test key simulated the event (`"mode": "test"`): nothing was sent or received, and its
     * id is never stored. On `email.received` it is the only sign of a simulated e-mail.
     */
    public function isTestMode(): bool
    {
        return $this->mode === 'test';
    }

    /** Whether this version of the SDK knows the event (Tawked\Webhook\Events). */
    public function isKnown(): bool
    {
        return isset(Events::DATA[$this->event]);
    }

    /**
     * The data typed by its event (Tawked\Webhook\Events::DATA): VerificationEventData for
     * verification.*, MessageEventData for message.*, EmailEventData or EmailRecipientEventData for
     * email.*, ApplicationEventData for service.*, BalanceLowData for balance.low; null for an event
     * this version does not know (read `$data`).
     */
    public function typedData(): ?Model
    {
        $classes = Events::DATA[$this->event] ?? [];
        foreach ($classes as $class) {
            $required = Events::REQUIRED[$class];
            if (count($classes) === 1 || array_diff($required, array_keys($this->data)) === []) {
                return $class::fromArray($this->data);
            }
        }

        return $classes === [] ? null : $classes[count($classes) - 1]::fromArray($this->data);
    }
}
