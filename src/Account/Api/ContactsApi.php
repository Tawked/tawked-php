<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Account\Api;

use Tawked\Account\AccountNamespace;
use Tawked\Account\Api\Contacts\ImportsApi;
use Tawked\Account\Api\Contacts\OptInApi;
use Tawked\Account\Api\Contacts\TagsApi;
use Tawked\Http\Transport;

/**
 * `$account->contacts`: the Account API's `contacts` operations.
 */
final class ContactsApi extends AccountNamespace
{
    public readonly \Tawked\Account\Api\Contacts\ContactsApi $contacts;

    public readonly ImportsApi $imports;

    public readonly OptInApi $optIn;

    public readonly TagsApi $tags;

    public function __construct(Transport $transport)
    {
        parent::__construct($transport);
        $this->contacts = new \Tawked\Account\Api\Contacts\ContactsApi($transport);
        $this->imports = new ImportsApi($transport);
        $this->optIn = new OptInApi($transport);
        $this->tags = new TagsApi($transport);
    }
}
