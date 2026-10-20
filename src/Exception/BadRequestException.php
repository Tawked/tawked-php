<?php

declare(strict_types=1);

namespace Tawked\Exception;

/**
 * 400: invalid_request, invalid_json, service_required. `fields()` names the refused fields where the API lists them.
 */
class BadRequestException extends ApiException {}
