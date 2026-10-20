<?php

declare(strict_types=1);

namespace Tawked\Model\Webhook;

/**
 * A verified delivery of an event this version of the SDK does not know yet: `$data` holds it as
 * sent. Never an exception, so a new event never breaks a receiver; update the SDK to type it.
 */
final class UnknownEvent extends WebhookEvent {}
