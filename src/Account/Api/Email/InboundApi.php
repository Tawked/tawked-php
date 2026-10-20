<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api\Email;

use Tawked\Account\AccountNamespace;
use Tawked\Account\Api\Email\Inbound\AddressesApi;
use Tawked\Http\Transport;

/**
 * `$account->email->inbound`: the Account API's `email.inbound` operations.
 */
final class InboundApi extends AccountNamespace
{
    public readonly AddressesApi $addresses;

    public function __construct(Transport $transport)
    {
        parent::__construct($transport);
        $this->addresses = new AddressesApi($transport);
    }
}
