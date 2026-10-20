<?php

declare(strict_types=1);

namespace Tawked\Exception;

use Throwable;

/**
 * Every exception this package throws implements it: catch it to catch them all.
 */
interface TawkedException extends Throwable {}
