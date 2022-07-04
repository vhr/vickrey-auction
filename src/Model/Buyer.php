<?php

namespace App\Model;

class Buyer
{
    private string $name;
    private ?int $maxBid = null;
    /** @var Bid[] */
    private array $bids = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addBid(int $bid): self
    {
        if ($this->maxBid < $bid) {
            $this->maxBid = $bid;
        }

        $this->bids[] = $bid;

        return $this;
    }

    public function getBids(): array
    {
        return $this->bids;
    }

    public function getMaxBid(): ?int
    {
        return $this->maxBid;
    }
}
