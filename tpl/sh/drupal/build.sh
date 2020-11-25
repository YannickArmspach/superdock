composer install --prefer-dist --no-progress --no-ansi --no-interaction -vvv
chown -R www-data:www-data web/sites/default/files
chmod -R 777 web/sites/default/files
chmod -R 660 private.key
chmod -R 750 public.key
php vendor/bin/drush cache:rebuild