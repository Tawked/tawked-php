<?php

declare(strict_types=1);

namespace Tawked\Exception;

/**
 * 403 insufficient_scope: the key was not granted this call. `requiredScope()` reads the
 * scope the call needs from the `WWW-Authenticate` header.
 */
class InsufficientScopeException extends PermissionDeniedException {}
