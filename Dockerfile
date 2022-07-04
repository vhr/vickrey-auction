FROM php:8.1-cli-alpine

RUN apk --update add wget \ 
	curl \
	git && rm /var/cache/apk/*

RUN apk add --no-cache $PHPIZE_DEPS \
	&& pecl install pcov \
	&& docker-php-ext-enable pcov

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer 

COPY ./ /usr/src/app
WORKDIR /usr/src/app

RUN composer install
RUN composer test

CMD [ "php", "./application.php" ]