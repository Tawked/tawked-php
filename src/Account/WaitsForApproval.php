<?php

declare(strict_types=1);

namespace Tawked\Account;

use Tawked\Exception\ApprovalRequiredException;
use Tawked\Exception\InvalidArgumentException;

/**
 * `$account->approvals->waitAndRetry($e)`: the 428 loop of a consequential call.
 *
 * @internal used by the generated approvals namespace
 */
trait WaitsForApproval
{
    /**
     * Reads the approval of a 428 every `poll_every` seconds until it is no longer pending, then sends
     * exactly the same request again with the same Idempotency-Key and `Tawked-Approval`. A declined,
     * expired or used approval answers its own code (approval_declined, approval_expired,
     * approval_used) as an exception; still pending after `timeout` seconds, the 428 is thrown again.
     *
     * @param array{poll_every?: int|float, timeout?: int|float, sleep?: callable(float): void} $options
     */
    public function waitAndRetry(ApprovalRequiredException $error, array $options = []): AccountResponse
    {
        $id = $error->approvalId();
        $pending = $error->pending;
        if ($id === null || $pending === null) {
            throw new InvalidArgumentException('This 428 carries no approval to wait on.');
        }
        $every = (float) ($options['poll_every'] ?? 30);
        $timeout = (float) ($options['timeout'] ?? 600);
        $sleep = $options['sleep'] ?? static function (float $seconds): void {
            if ($seconds > 0) {
                usleep((int) round($seconds * 1_000_000));
            }
        };
        if ($every <= 0 || $timeout < 0) {
            throw new InvalidArgumentException('`poll_every` must be positive and `timeout` 0 or more.');
        }

        $waited = 0.0;
        while (true) {
            $status = $this->show($id)->toArray()['status'] ?? null;
            if ($status !== 'pending') {
                // Approved runs it; declined, expired, used or stale answer their own refusal.
                return AccountResponse::from($this->transport->replay($pending, $id));
            }
            if ($waited + $every > $timeout) {
                throw $error;
            }
            $sleep($every);
            $waited += $every;
        }
    }
}
