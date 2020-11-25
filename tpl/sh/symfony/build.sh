composer install --prefer-dist --no-progress --no-ansi --no-interaction -vvv
chmod -R 777 var/cache
php bin/console cache:clear