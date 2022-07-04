<?php

namespace App\Exception;

use App\Model\Buyer;

class BidBelowReservePriceException extends \Exception
{
    public function __construct(
        private Buyer $buyer,
    ) {
        parent::__construct('The bid value is below the reserve price');
    }

    public function getBuyer(): Buyer
    {
        return $this->buyer;
    }
}
