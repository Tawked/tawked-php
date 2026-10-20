<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api;

use Tawked\Account\AccountNamespace;
use Tawked\Http\Transport;

/**
 * `$account->members`: the Account API's `members` operations.
 */
final class MembersApi extends AccountNamespace
{
    public readonly \Tawked\Account\Api\Members\MembersApi $members;

    public function __construct(Transport $transport)
    {
        parent::__construct($transport);
        $this->members = new \Tawked\Account\Api\Members\MembersApi($transport);
    }
}
