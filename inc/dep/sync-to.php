<?php
namespace Deployer;

use Symfony\Component\Process\Process;

require 'recipe/common.php';
require 'hosts.php';
require 'settings.php';

task('sync:media', function () {
    $SUPERDOCK = get('SUPERDOCK');
	upload( $SUPERDOCK['SOURCE_DIR'] . $SUPERDOCK['SOURCE_UPLOAD'] . '/', '{{deploy_path}}/shared' . $SUPERDOCK['DIST_UPLOAD'] . '/' );
});

task('sync:dump', function () {
    $SUPERDOCK = get('SUPERDOCK');
    $process = new Process( 
        [ 
            'docker-compose', 
            '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/config.yml', 
            'exec', 
            'webserver', 
            'sh', 
			'-c', 
			'mysqldump --host=superdock_database --user=root --password=root ' . $_ENV['SUPERDOCK_LOCAL_DB_NAME'] . ' > /var/www/html/superdock/database/local/local.sql'
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
    $SUPERDOCK = get('SUPERDOCK');
    $sql = file_get_contents( $SUPERDOCK['SOURCE_DIR'] . '/superdock/database/local/local.sql' );
    $sql = str_replace( $_ENV['SUPERDOCK_LOCAL_DOMAIN'], $_ENV['SUPERDOCK_' . strtoupper( get('deploy_env') ) . '_DOMAIN'], $sql );
    $sql = str_replace( 'utf8mb4_0900_ai_ci', 'utf8mb4_unicode_ci', $sql );
    file_put_contents( $SUPERDOCK['SOURCE_DIR'] . '/superdock/database/local/dist.sql', $sql );
});

task('sync:db', function () {
    $SUPERDOCK = get('SUPERDOCK');
    upload( $SUPERDOCK['SOURCE_DIR'] . '/superdock/database/local/dist.sql', '{{deploy_path}}/{{deploy_env}}.sql' );
    run('mysql -f --host={{deploy_db_host}} --user={{deploy_db_user}} --password={{deploy_db_pass}} {{deploy_db_name}} < {{deploy_path}}/{{deploy_env}}.sql');
    //run('mysql -u {{deploy_db_user}} -p -f -D --host={{deploy_db_host}} {{deploy_db_name}} < {{deploy_path}}/{{deploy_env}}.sql');
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
