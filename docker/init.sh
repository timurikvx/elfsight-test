#!/bin/bash

cd /var/www/elfsight
composer install

php /var/www/elfsight/bin/console doctrine:migrations:migrate --no-interaction -q
php /var/www/elfsight/bin/console episode:import

exec php-fpm -F
