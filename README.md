# Vickrey auction

A Vickrey auction or sealed-bid second-price auction (SBSPA) is a type of sealed-bid auction. Bidders submit written bids without knowing the bid of the other people in the auction. The highest bidder wins but the price paid is the second-highest bid.

## Run application

### Docker

build the image locally
```
git clone https://github.com/vhr/vickrey-auction.git
cd vickrey-auction
docker build -t vickrey-auction .
docker run -it --rm --name vickrey-auction vickrey-auction
```

or run the pre-build image
```
docker pull vhr7/vickrey-auction:latest
docker run -it --rm --name vickrey-auction vhr7/vickrey-auction:latest
```

### Run locally

```bash
git clone https://github.com/vhr/vickrey-auction.git
cd vickrey-auction
composer install
php ./application.php
```

## Tests [![codecov](https://codecov.io/gh/vhr/vickrey-auction/branch/main/graph/badge.svg?token=6PB12ANA1W)](https://codecov.io/gh/vhr/vickrey-auction)

```bash
composer test
```

## License [![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2Fvhr%2Fvickrey-auction.svg?type=shield)](https://app.fossa.com/projects/git%2Bgithub.com%2Fvhr%2Fvickrey-auction?ref=badge_shield)

The code in this project is released under the [MIT License](LICENSE).

## References

- https://en.wikipedia.org/wiki/Vickrey_auction
- https://symfony.com/doc/current/components/console.html
- https://phpunit.de/
