<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Integrations;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Account\Api\Integrations\Woocommerce\KeysApi;
use Tawked\Http\Transport;

/**
 * `$account->integrations->woocommerce`: the Account API's `integrations.woocommerce` operations.
 */
final class WoocommerceApi extends AccountNamespace
{
    public readonly KeysApi $keys;

    public function __construct(Transport $transport)
    {
        parent::__construct($transport);
        $this->keys = new KeysApi($transport);
    }

    /**
     * Disconnect the WooCommerce plugin: every key it holds is revoked and the store stops sending
     * codes and order updates until it gets a new key. Answers how many keys were revoked. `DELETE
     * /v1/applications/{application}/integrations/woocommerce`.
     *
     * Permission `integrations:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Sent once: retried only
     * when the request never left (or the key is still in progress). After a timeout the exception
     * carries the `Idempotency-Key`: call again with it, never a new one. After a 5xx Tawked
     * released the key: check whether it took effect before calling again.
     *
     * @param string $application `application` in the path
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation integrations.woocommerce.disconnect
     */
    public function disconnect(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'DELETE',
            path: '/v1/applications/'.self::segment($application, 'application').'/integrations/woocommerce',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
        ));
    }

    /**
     * Read where the WooCommerce plugin stands on the application: the plugin's version and
     * download, the live and test keys it holds (prefix, last use), which key may be made now, its
     * last call, what WhatsApp order updates need, its `state` (none, waiting, connected) and
     * `next`. `GET /v1/applications/{application}/integrations/woocommerce`.
     *
     * Permission `integrations:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation integrations.woocommerce.show
     */
    public function show(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/integrations/woocommerce',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }
}
