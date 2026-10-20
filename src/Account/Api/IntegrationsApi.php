<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Account\Api\Integrations\AppsApi;
use Tawked\Account\Api\Integrations\LarmApi;
use Tawked\Account\Api\Integrations\SupabaseApi;
use Tawked\Account\Api\Integrations\WoocommerceApi;
use Tawked\Http\Transport;

/**
 * `$account->integrations`: the Account API's `integrations` operations.
 */
final class IntegrationsApi extends AccountNamespace
{
    public readonly AppsApi $apps;

    public readonly LarmApi $larm;

    public readonly SupabaseApi $supabase;

    public readonly WoocommerceApi $woocommerce;

    public function __construct(Transport $transport)
    {
        parent::__construct($transport);
        $this->apps = new AppsApi($transport);
        $this->larm = new LarmApi($transport);
        $this->supabase = new SupabaseApi($transport);
        $this->woocommerce = new WoocommerceApi($transport);
    }

    /**
     * Read an application's integrations at a glance: its Salla or Zid store, a Larm store linked
     * to it, Supabase phone sign-in (for who reads Verify), the WooCommerce plugin's state (none,
     * waiting, connected) and the connected apps. Open each with its own operation. `GET
     * /v1/applications/{application}/integrations`.
     *
     * Permission `integrations:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation integrations.show
     */
    public function show(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/integrations',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }
}
