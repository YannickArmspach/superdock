<?php
namespace Deployer;

require 'recipe/common.php';
require 'hosts.php';
require 'settings.php';

task('sync:media', function () {
    download( '{{deploy_path}}/shared/public/uploads/', 'public/uploads/' );
});

task('sync:db', function () {
    run('mysqldump --host={{deploy_db_host}} --user={{deploy_db_user}} --password={{deploy_db_pass}} {{deploy_db_name}} > {{deploy_path}}/{{deploy_env}}.sql');
    download( '{{deploy_path}}/{{deploy_env}}.sql', 'local/db/{{deploy_env}}.sql'  );
    run('rm {{deploy_path}}/{{deploy_env}}.sql');
});

task('sync:chown', function () {
	run('chown -R www-data:www-data {{deploy_path}}');
});

task('sync', [
    'sync:media',
    'sync:db',
	'sync:chown',
	'cleanup',
]);
