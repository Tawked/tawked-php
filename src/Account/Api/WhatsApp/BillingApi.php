<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\WhatsApp;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->whatsapp->billing`: the Account API's `whatsapp.billing` operations.
 */
final class BillingApi extends AccountNamespace
{
    /**
     * Ask Meta again whether the number's WhatsApp Business Account has a payment method; answers
     * `billing_state` (ready, missing or unknown), and while it is missing the `human_action` that
     * adds one at Meta. `POST /v1/applications/{application}/whatsapp/billing/check`.
     *
     * Permission `whatsapp:write`. Sent once: retried only when the request never left (or the key
     * is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * @param string $application `application` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation whatsapp.billing.check
     */
    public function check(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/whatsapp/billing/check',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }
}
