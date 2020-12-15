<?php
namespace Deployer;

use SuperDock\Service\coreService;
use Symfony\Component\Console\Application;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

require 'recipe/common.php';
require 'hosts.php';
require 'settings.php';

task('sync:media', function () {
    download( '{{deploy_path}}/shared' . $_ENV['SUPERDOCK_' . strtoupper( get('deploy_env') ) . '_UPLOAD'] . '/', $_ENV['SUPERDOCK_PROJECT_DIR'] . $_ENV['SUPERDOCK_LOCAL_UPLOAD'] . '/'  );
});

task('sync:db', function () {
    //TODO: add secure file name to dist dump before remove for security
    if ( get('deploy_db_host') == 'localhost' ) {
    run('mysqldump --host={{deploy_db_host}} --user={{deploy_db_user}} --password={{deploy_db_pass}} {{deploy_db_name}} > {{deploy_path}}/{{deploy_env}}.sql');
    download('{{deploy_path}}/{{deploy_env}}.sql', $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/database/{{deploy_env}}/dist.sql');
    run('rm {{deploy_path}}/{{deploy_env}}.sql');
    } else {
        runLocally('mysqldump --host={{deploy_db_host}} --user={{deploy_db_user}} --password={{deploy_db_pass}} {{deploy_db_name}} > ' . $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/database/{{deploy_env}}/dist.sql');
    }
});

task('sync:format', function () {
    $sql = file_get_contents( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/database/' . get('deploy_env') . '/dist.sql' );
    $sql = str_replace( $_ENV['SUPERDOCK_' . strtoupper( get('deploy_env') ) . '_DOMAIN'], $_ENV['SUPERDOCK_LOCAL_DOMAIN'], $sql );
    file_put_contents( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/database/' . get('deploy_env') . '/local.sql', $sql );
});

task('sync:install', function () {
    echo "➤ Wait database sync to docker" . PHP_EOL;
    sleep(5);
    echo "➤ Starting database install" . PHP_EOL;
    coreService::process([ 
        'docker-compose', 
        '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-compose.yml', 
        'exec', 
        'webserver', 
        'sh', 
        '-c', 
        'mysql --host=superdock_database --user=root --password=root ' . $_ENV['SUPERDOCK_LOCAL_DB_NAME'] . ' < /var/www/html/superdock/database/' . get('deploy_env') . '/local.sql', 
    ]);
});

desc('sync:migrate');
task('sync:migrate', function () {
    coreService::process([ 
        'docker-compose', 
        '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-compose.yml', 
        'exec', 
        'webserver', 
        'sh', 
        '-c', 
        './bin/console --no-interaction doctrine:migration:migrate'
    ]);
});

desc('sync:elastic:reindex');
task('sync:elastic:reindex', function () {
    coreService::process([ 
        'docker-compose', 
        '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-compose.yml', 
        'exec', 
        'webserver', 
        'sh', 
        '-c', 
        './bin/console elastic:reindex'
    ]);
});

task('sync', [
    'sync:media',
    'sync:db',
    'sync:format',
    'sync:install',
    'sync:migrate',
    'sync:elastic:reindex',
	'cleanup',
]);
