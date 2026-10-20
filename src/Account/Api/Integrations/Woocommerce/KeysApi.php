<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Integrations\Woocommerce;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->integrations->woocommerce->keys`: the Account API's `integrations.woocommerce.keys` operations.
 */
final class KeysApi extends AccountNamespace
{
    /**
     * Make the WooCommerce plugin's key (`mode`: live, or test while the application is in review);
     * answers its plaintext once, and the `human_action` that has the person install the plugin and
     * paste it into its settings. A key of that mode it held before is revoked. `POST
     * /v1/applications/{application}/integrations/woocommerce/keys`.
     *
     * Permission `integrations:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Never retried: its
     * answer holds a secret shown once (120 s timeout). After a failure, find what it made by its
     * id, then rotate or revoke it.
     *
     * @param string $application `application` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation integrations.woocommerce.keys.create
     */
    public function create(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/integrations/woocommerce/keys',
            options: $options,
            retry: Transport::NEVER,
            timeout: 120.0,
            secret: true,
        ));
    }
}
