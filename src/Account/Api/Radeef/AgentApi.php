<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Radeef;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->radeef->agent`: the Account API's `radeef.agent` operations.
 */
final class AgentApi extends AccountNamespace
{
    /**
     * Read Radeef (the AI agent answering this application's WhatsApp customers): its state (setup,
     * live, draft, off, paused_budget, paused_balance, locked, unavailable) and why, mode, name,
     * hours mode, what it knows and this month's spend. `GET
     * /v1/applications/{application}/radeef`.
     *
     * Permission `radeef:read`. Retried after a network failure, a timeout or a 5xx (a read).
     *
     * @param string $application `application` in the path
     * @param array{timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation radeef.agent.show
     */
    public function show(string $application, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'GET',
            path: '/v1/applications/'.self::segment($application, 'application').'/radeef',
            options: $options,
            retry: Transport::SAFE,
            timeout: 30.0,
        ));
    }
}
