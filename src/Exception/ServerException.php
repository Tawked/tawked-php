<?php

declare(strict_types=1);

namespace Tawked\Exception;

/**
 * 5xx: internal_error, send_failed (the charge is refunded), storage_unavailable, templates_unavailable. Quote `requestId` to support.
 */
class ServerException extends ApiException {}
