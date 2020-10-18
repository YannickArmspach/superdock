<?php
namespace Deployer;

use Symfony\Component\Process\Process;

require 'recipe/common.php';
require 'hosts.php';
require 'settings.php';

task('sync:media', function () {
	upload('public/uploads/', '{{deploy_path}}/shared/public/uploads/' );
});

task('sync:dump', function () {
    
    $process = new Process( 
        [ 
            'docker-compose', 
            '-f' . $_ENV['SUPERDOCK_USER_DIR'] . '/.superdock/docker/config.yml', 
            'exec', 
            'webserver', 
            'sh', 
			'-c', 
			'mysqldump --host=superdock_database --user=root --password=root ' . $_ENV['SUPERDOCK_LOCAL_DB_NAME'] . ' > /var/www/html/superdock/database/local/dump.sql'
        ]
    );
    $process->setTty(Process::isTtySupported());
    $process->run(function ($type, $buffer) {
        if (Process::ERR === $type) {
            echo $buffer;
        } else {
            echo $buffer;
        }
    });

});

task('sync:format', function () {
    $sql = file_get_contents( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/database/local/dump.sql' );
    $sql = str_replace( $_ENV['SUPERDOCK_LOCAL_DOMAIN'], $_ENV['SUPERDOCK_' . strtoupper( get('deploy_env') ) . '_DOMAIN'], $sql );
    file_put_contents( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/database/' . get('deploy_env') . '/dist.sql', $sql );
});

task('sync:db', function () {
    upload( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/database/{{deploy_env}}/dist.sql', '{{deploy_path}}/{{deploy_env}}.sql' );
    run('mysql --host={{deploy_db_host}} --user={{deploy_db_user}} --password={{deploy_db_pass}} {{deploy_db_name}} < {{deploy_path}}/{{deploy_env}}.sql');
    run('rm {{deploy_path}}/{{deploy_env}}.sql');
});

task('sync', [
	'deploy:info',
	'deploy:unlock',
	'deploy:lock',
	'sync:media',
	'sync:dump',
	'sync:format',
	'sync:db',
	'deploy:unlock',
	'cleanup',
]);
