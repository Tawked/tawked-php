<?php

declare(strict_types=1);

// Generated from the Tawked SDK contract by scripts/generate.php; do not edit.

namespace Tawked\Model\Email;

use Tawked\Internal\Data;
use Tawked\Model\Model;

/**
 * An object of the Tawked API.
 */
final class EmailDomainReceiving extends Model
{
    public function __construct(
        public readonly string $addressId,
        /** The receiving subdomain, such as inbound.acme.sa. */
        public readonly string $domain,
        public readonly string $state,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $model = new self(
            Data::string($data, 'address_id'),
            Data::string($data, 'domain'),
            Data::string($data, 'state'),
        );
        $model->raw = $data;

        return $model;
    }
}
