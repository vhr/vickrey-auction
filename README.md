# Vickrey auction

A Vickrey auction or sealed-bid second-price auction (SBSPA) is a type of sealed-bid auction. Bidders submit written bids without knowing the bid of the other people in the auction. The highest bidder wins but the price paid is the second-highest bid.

# Run application

## Docker

build the image locally
```
git clone https://github.com/vhr/vickrey-auction.git
cd vickrey-auction
docker run -it --rm --name vickrey-auction vickrey-auction
```

Run
```
docker run -it --rm --name vickrey-auction vickrey-auction
```

## Run locally

```bash
git clone https://github.com/vhr/vickrey-auction.git
cd vickrey-auction
composer install
php ./application.php
```

# Tests

```bash
composer test
```

# References

https://en.wikipedia.org/wiki/Vickrey_auction
https://symfony.com/doc/current/components/console.html
https://phpunit.de/