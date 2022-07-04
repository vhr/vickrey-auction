<?php

namespace App\Model;

class Session
{
    private int $reservePrice;
    /** @var Buyer[] */
    private array $buyers = [];

    public function __construct(int $reservePrice)
    {
        $this->reservePrice = $reservePrice;
    }

    public function getReservePrice(): int
    {
        return $this->reservePrice;
    }

    public function addBuyer(Buyer $buyer): self
    {
        $this->buyers[$buyer->getName()] = $buyer;

        return $this;
    }

    public function getBuyers(): array
    {
        return $this->buyers;
    }
}
