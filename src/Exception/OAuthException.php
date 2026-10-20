<?php

declare(strict_types=1);

namespace Tawked\Exception;

use Tawked\Internal\Data;

/**
 * The OAuth exchange refused (`{error, error_description}`): `invalid_client` (401), `invalid_grant`,
 * `invalid_request` or `unsupported_grant_type` (400). `invalid_grant` after a timeout means the
 * connection may have been made: ask the merchant to approve the app again (a new request replaces it).
 */
class OAuthException extends ApiException
{
    /** The server's `error_description`. */
    public function description(): ?string
    {
        return Data::nullableString($this->body, 'error_description');
    }
}
