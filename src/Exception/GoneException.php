<?php

declare(strict_types=1);

namespace Tawked\Exception;

/**
 * 410: the endpoint is gone (`product_retired`, the old `/v1/messages`), or on the Account API an
 * approval expired (`approval_expired`).
 */
class GoneException extends ApiException {}
