<?php

namespace App\Tests\Unit;

use App\Auction;
use App\Exception\BidBelowReservePriceException;
use App\Exception\MoreThanOneWinnerException;
use App\Exception\NoSecondBuyerException;
use App\Exception\NoWinnerException;
use App\Model\Buyer;
use App\Model\Session;
use PHPUnit\Framework\TestCase;

final class AuctionTest extends TestCase
{
    public function testWinnerAndWinningPrice(): void
    {
        $auction = new Auction(
            (new Session(100))
                ->addBuyer(new Buyer('A'))
                ->addBuyer(new Buyer('B'))
                ->addBuyer(new Buyer('C'))
                ->addBuyer(new Buyer('D'))
                ->addBuyer(new Buyer('E'))
        );

        // Bids
        $auction
            ->bid($auction->getBuyer('A'), 110)
            ->bid($auction->getBuyer('C'), 125)
            ->bid($auction->getBuyer('D'), 105)
            ->bid($auction->getBuyer('E'), 132)
            // Second
            ->bid($auction->getBuyer('A'), 130)
            ->bid($auction->getBuyer('D'), 115)
            ->bid($auction->getBuyer('E'), 135);

        // Third
        try {
            $auction->bid($auction->getBuyer('D'), 90);
        } catch (BidBelowReservePriceException $exception) {
            $this->assertEquals(
                $auction->getBuyer('D'),
                $exception->getBuyer(),
            );
        }

        $auction->bid($auction->getBuyer('E'), 140);

        $winner = $auction->winner();

        $this->assertEquals(
            $auction->getBuyer('E'),
            $winner,
        );

        $this->assertEquals(
            130,
            $auction->winningPrice($winner),
        );
    }

    public function testMoreThanOneWinnerException(): void
    {
        $this->expectException(MoreThanOneWinnerException::class);

        $auction = new Auction(
            (new Session(100))
                ->addBuyer(new Buyer('A'))
                ->addBuyer(new Buyer('B'))
        );

        $auction
            ->bid($auction->getBuyer('A'), 101)
            ->bid($auction->getBuyer('B'), 101);

        $auction->winner();
    }

    public function testNoWinnerException(): void
    {
        $this->expectException(NoWinnerException::class);

        $auction = new Auction(
            (new Session(100))
                ->addBuyer(new Buyer('A'))
        );

        $auction->winner();
    }

    public function testNoSecondBuyerException(): void
    {
        $this->expectException(NoSecondBuyerException::class);

        $auction = new Auction(
            (new Session(100))
                ->addBuyer(new Buyer('A'))
        );

        $auction
            ->bid($auction->getBuyer('A'), 101);

        $auction->winningPrice($auction->winner());
    }

    public function testBuyerGetBids(): void
    {
        $auction = new Auction(
            (new Session(1))
                ->addBuyer(new Buyer('A'))
        );

        $auction
            ->bid($auction->getBuyer('A'), 2)
            ->bid($auction->getBuyer('A'), 3);

        $this->assertEquals(
            [2, 3],
            $auction->getBuyer('A')->getBids(),
        );
    }
}
