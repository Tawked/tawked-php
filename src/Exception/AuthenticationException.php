<?php

declare(strict_types=1);

namespace Tawked\Exception;

/**
 * 401: unauthorized, key_expired. The key is missing, unknown, revoked, of the wrong kind or expired.
 */
class AuthenticationException extends ApiException {}
