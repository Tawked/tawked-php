<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Integrations\Larm;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->integrations->larm->consent`: the Account API's `integrations.larm.consent` operations.
 */
final class ConsentApi extends AccountNamespace
{
    /**
     * Say (`attested: true`) or withdraw that the Larm store's checkout collects its customers'
     * consent to marketing messages; answers the consent as it stands. `PUT
     * /v1/applications/{application}/integrations/larm/consent`.
     *
     * Permission `integrations:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Sent once: retried only
     * when the request never left (or the key is still in progress). After a timeout the exception
     * carries the `Idempotency-Key`: call again with it, never a new one. After a 5xx Tawked
     * released the key: check whether it took effect before calling again.
     *
     * @param string $application `application` in the path
     * @param array{attested: bool}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation integrations.larm.consent.update
     */
    public function update(string $application, array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'PUT',
            path: '/v1/applications/'.self::segment($application, 'application').'/integrations/larm/consent',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, []),
        ));
    }
}
