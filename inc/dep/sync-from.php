<?php
namespace Deployer;

use Symfony\Component\Process\Process;

require 'recipe/common.php';
require 'hosts.php';
require 'settings.php';

task('sync:media', function () {
    download( '{{deploy_path}}/shared/public/uploads/', 'public/uploads/' );
});

task('sync:db', function () {
    run('mysqldump --host={{deploy_db_host}} --user={{deploy_db_user}} --password={{deploy_db_pass}} {{deploy_db_name}} > {{deploy_path}}/{{deploy_env}}.sql');
    download('{{deploy_path}}/{{deploy_env}}.sql', $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/database/{{deploy_env}}/dist.sql');
    run('rm {{deploy_path}}/{{deploy_env}}.sql');
});

task('sync:chown', function () {
	run('chown -R www-data:www-data {{deploy_path}}');
});


task('sync:format', function () {
    $sql = file_get_contents( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/database/' . get('deploy_env') . '/dist.sql' );
    $sql =str_replace( $_ENV['SUPERDOCK_STAGING_DOMAIN'], $_ENV['SUPERDOCK_LOCAL_DOMAIN'], $sql );
    file_put_contents( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/database/' . get('deploy_env') . '/local.sql', $sql );
});

task('sync:install', function () {
    
    $process = new Process( 
        [ 
            'docker-compose', 
            '-f' . $_ENV['SUPERDOCK_USER_DIR'] . '/.superdock/docker/config.yml', 
            'exec', 
            'webserver', 
            'sh', 
            '-c', 
            'mysql --host=superdock_database --user=root --password=root db < /var/www/html/superdock/database/' . get('deploy_env') . '/local.sql', 
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


task('sync', [
    'sync:media',
    'sync:db',
    'sync:chown',
    'sync:format',
    'sync:install',
	'cleanup',
]);
