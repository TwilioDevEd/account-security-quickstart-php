FROM twiliodeved/account-security-quickstart-php:base

COPY ./ /var/www/html

WORKDIR /var/www/html

RUN ./vendor/bin/phpunit
