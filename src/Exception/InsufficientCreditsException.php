<?php

declare(strict_types=1);

namespace Tawked\Exception;

use Tawked\Internal\Data;

/**
 * 402 insufficient_credits: the prepaid balance is below the price. Top up in the console.
 * The WhatsApp send also says the balance and the price.
 */
class InsufficientCreditsException extends ApiException
{
    public function balanceHalalas(): ?int
    {
        return Data::nullableInt($this->body, 'balance_halalas');
    }

    public function priceHalalas(): ?int
    {
        return Data::nullableInt($this->body, 'price_halalas');
    }
}
