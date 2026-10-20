<?php

declare(strict_types=1);

namespace Tawked\Exception;

/**
 * 429. `too_many_requests` is the per-key limit (120 a minute, a fixed window: wait for the
 * next minute); on e-mail, `rate_limited` is the account's per second (Retry-After: 1). Both carry
 * a Retry-After, which the client waits on when it is 60 s or less. Elsewhere rate_limited,
 * ip_rate_limited, resend_limit_reached, sandbox_quota_exceeded, daily_cap_reached and
 * spend_cap_reached are business limits with no Retry-After, not worth retrying soon.
 */
class RateLimitException extends ApiException {}
