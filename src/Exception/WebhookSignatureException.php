<?php

declare(strict_types=1);

namespace Tawked\Exception;

use RuntimeException;
use Throwable;

/**
 * A webhook delivery failed verification. `reason()` says why: `missing_header`,
 * `no_secret_for_scheme`, `timestamp_out_of_tolerance`, `signature_mismatch`, `malformed_header`
 * or `malformed_body`. Answer the delivery with a 400.
 */
class WebhookSignatureException extends RuntimeException implements TawkedException
{
    public const MISSING_HEADER = 'missing_header';

    public const NO_SECRET_FOR_SCHEME = 'no_secret_for_scheme';

    public const TIMESTAMP_OUT_OF_TOLERANCE = 'timestamp_out_of_tolerance';

    public const SIGNATURE_MISMATCH = 'signature_mismatch';

    public const MALFORMED_HEADER = 'malformed_header';

    public const MALFORMED_BODY = 'malformed_body';

    public function __construct(string $message, private readonly string $reason = self::SIGNATURE_MISMATCH, ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }

    public function reason(): string
    {
        return $this->reason;
    }
}
