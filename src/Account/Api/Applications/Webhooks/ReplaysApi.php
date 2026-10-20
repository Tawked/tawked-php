<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Applications\Webhooks;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->applications->webhooks->replays`: the Account API's `applications.webhooks.replays` operations.
 */
final class ReplaysApi extends AccountNamespace
{
    /**
     * One replay of a webhook endpoint: its `state` (queued, running, done), how many deliveries
     * `matched` and how many were `queued` again, its window. `GET
     * /v1/applications/{application}/webhooks/{endpoint}/replays/{replay}`.
     *
     * Permission `applications:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param string $endpoint `endpoint` in the path
     * @param string $replay `replay` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation applications.webhooks.replays.show
     */
    public function show(string $application, string $endpoint, string $replay, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/webhooks/'.self::segment($endpoint, 'endpoint').'/replays/'.self::segment($replay, 'replay'),
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }
}
