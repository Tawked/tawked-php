<?php

declare(strict_types=1);

namespace Tawked\Exception;

use Tawked\Http\PendingRequest;

/**
 * 428 approval_required (the Account API): a consequential call waits for its person's yes in the
 * console. Relay `humanAction()['say']` and its `url`, then
 * `$account->approvals->waitAndRetry($e)` reads the approval until it is approved and sends the
 * same request again with the same Idempotency-Key and `Tawked-Approval`. Never retried by itself.
 */
class ApprovalRequiredException extends ApiException
{
    /** @internal the request to send again, set by the transport */
    public ?PendingRequest $pending = null;

    /** The approval's id. */
    public function approvalId(): ?string
    {
        $approval = $this->approval();

        return is_string($approval['id'] ?? null) ? $approval['id'] : null;
    }
}
