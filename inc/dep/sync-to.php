<?php
namespace Deployer;

require 'recipe/common.php';
require 'hosts.php';
require 'settings.php';

task('sync:media', function () {
	upload('public/uploads/', '{{deploy_path}}/shared/public/uploads/' );
});

task('sync:db', function () {
    upload( 'local/db/{{deploy_env}}.dist.sql', '{{deploy_path}}/{{deploy_env}}.dist.sql' );
    run('mysql --host={{deploy_db_host}} --user={{deploy_db_user}} --password={{deploy_db_pass}} {{deploy_db_name}} < {{deploy_path}}/{{deploy_env}}.dist.sql');
    run('rm {{deploy_path}}/{{deploy_env}}.dist.sql');
});

task('sync:chown', function () {
	run('chown -R www-data:www-data {{deploy_path}}');
});

task('sync', [
	'deploy:info',
	'deploy:unlock',
	'deploy:lock',
	'sync:media',
	'sync:db',
	'sync:chown',
	'deploy:unlock',
	'cleanup',
]);
