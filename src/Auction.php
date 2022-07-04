<?php

namespace App;

use App\Exception\BidBelowReservePriceException;
use App\Exception\MoreThanOneWinnerException;
use App\Exception\NoSecondBuyerException;
use App\Exception\NoWinnerException;
use App\Model\Buyer;
use App\Model\Session;

class Auction
{
    public function __construct(
        private Session $session,
    ) {
    }

    public function bid(Buyer $buyer, int $bid): self
    {
        if ($this->session->getReservePrice() > $bid) {
            throw new BidBelowReservePriceException($buyer);
        }

        $buyer->addBid($bid);

        return $this;
    }

    public function winner(): Buyer
    {
        /** @var Buyer|null */
        $winner = null;
        $maxBid = $this->session->getReservePrice();

        foreach ($this->session->getBuyers() as $buyer) {
            if ($maxBid <= $buyer->getMaxBid()) {
                if ($winner && $winner->getMaxBid() == $buyer->getMaxBid()) {
                    throw new MoreThanOneWinnerException();
                }

                $winner = $buyer;
            }
        }

        if (!$winner) {
            throw new NoWinnerException();
        }

        return $winner;
    }

    public function winningPrice(Buyer $winner): int
    {
        $price = null;

        foreach ($this->session->getBuyers() as $buyer) {
            if (
                $price > $buyer->getMaxBid() ||
                $this->session->getReservePrice() > $buyer->getMaxBid() ||
                spl_object_id($winner) == spl_object_id($buyer)
            ) {
                continue;
            }

            $price = $buyer->getMaxBid();
        }

        if (!$price) {
            throw new NoSecondBuyerException();
        }

        return $price;
    }

    public function getBuyer(string $name): ?Buyer
    {
        return $this->session->getBuyers()[$name] ?? null;
    }
}
