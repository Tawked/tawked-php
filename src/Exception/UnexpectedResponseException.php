<?php

declare(strict_types=1);

namespace Tawked\Exception;

/**
 * The response was not the API's JSON: a proxy's page, an empty body, or a path the API
 * does not route (Laravel's own `{"message": ...}`). `errorCode` is `unexpected_response`.
 */
class UnexpectedResponseException extends ApiException {}
