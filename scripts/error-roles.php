<?php

/*
 * Prints `code → role` for every error code of the contract: the
 * role of the exception this SDK throws for it, in the words of the spec's table. Every language
 * prints the same lines; the integrator compares them byte for byte.
 *
 *   php scripts/error-roles.php
 */

declare(strict_types=1);

use Tawked\ErrorCode;
use Tawked\Internal\Errors;

require dirname(__DIR__).'/vendor/autoload.php';

$lines = [];
foreach (ErrorCode::STATUS as $code => $status) {
    $lines[] = $code.' → '.Errors::ROLES[Errors::classFor($code, $status)];
}
sort($lines);
fwrite(STDOUT, implode("\n", $lines)."\n");
