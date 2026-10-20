<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Applications;

use Tawked\Account\AccountNamespace;
use Tawked\Account\AccountResponse;
use Tawked\Http\Transport;

/**
 * `$account->applications->appeals`: the Account API's `applications.appeals` operations.
 */
final class AppealsApi extends AccountNamespace
{
    /**
     * Appeal Tawked's rejection of the application with a `note` (and optionally a multipart
     * `document`, up to 10 MB): it goes back to review; answers the application and `next`. `POST
     * /v1/applications/{application}/appeal`.
     *
     * Permission `applications:write`. Sent once: retried only when the request never left (or the
     * key is still in progress). After a timeout the exception carries the `Idempotency-Key`: call
     * again with it, never a new one. After a 5xx Tawked released the key: check whether it took
     * effect before calling again.
     *
     * @param string $application `application` in the path
     * @param array{document?: \Tawked\Attachment|\SplFileInfo|\Psr\Http\Message\StreamInterface|resource|string|null, note: string}|array<string, mixed> $params
     * @param array{idempotency_key?: string, timeout?: float, max_retries?: int, headers?: array<string, string>}|array<string, mixed> $options
     *
     * @tawked-operation applications.appeals.create
     */
    public function create(string $application, array $params, array $options = []): AccountResponse
    {
        return AccountResponse::from($this->transport->send(
            method: 'POST',
            path: '/v1/applications/'.self::segment($application, 'application').'/appeal',
            options: $options,
            retry: Transport::WRITE_ONCE,
            timeout: 30.0,
            multipart: $params,
            files: ['document'],
        ));
    }
}
