<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Integrations\Larm;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->integrations->larm->messages`: the Account API's `integrations.larm.messages` operations.
 */
final class MessagesApi extends AccountNamespace
{
    /**
     * Switch one of the Larm store's messages on or off (`enabled`), or set which order statuses an
     * order update speaks about (`statuses`: completed, cancelled, refunded, each true or false);
     * answers whether it was on before. `PUT
     * /v1/applications/{application}/integrations/larm/messages/{kind}`.
     *
     * Permission `integrations:write`. Consequential: it waits for its person's yes (428
     * ApprovalRequiredException; `$account->approvals->waitAndRetry($e)`). Sent once: retried only
     * when the request never left (or the key is still in progress). After a timeout the exception
     * carries the `Idempotency-Key`: call again with it, never a new one. After a 5xx Tawked
     * released the key: check whether it took effect before calling again.
     *
     * @param string $application `application` in the path
     * @param string $kind `kind` in the path
     * @param array{enabled?: bool, statuses?: array<string, mixed>}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation integrations.larm.messages.update
     */
    public function update(string $application, string $kind, array $params = [], array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'PUT',
            path: '/v1/applications/'.self::segment($application, 'application').'/integrations/larm/messages/'.self::segment($kind, 'kind'),
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            json: self::body($params, ['statuses']),
        ));
    }
}
