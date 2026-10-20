<?php

declare(strict_types=1);

namespace Tawked\Exception;

/**
 * 422: the request was understood but refused (invalid_destination, unknown_template, invalid_params, consent_opted_out, sandbox_unverified_destination, ...).
 */
class UnprocessableEntityException extends ApiException {}
