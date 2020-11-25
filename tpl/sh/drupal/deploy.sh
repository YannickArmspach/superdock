chown -R www-data:www-data web/sites/default/files
chmod -R 777 web/sites/default/files
chmod -R 660 web/sites/default/files
php vendor/bin/drush cache:rebuild