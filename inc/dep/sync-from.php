<?php
namespace Deployer;

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
    download( '{{deploy_path}}/shared' . $_ENV['SUPERDOCK_' . strtoupper( get('deploy_env') ) . '_UPLOAD'] . '/', 'public' . $_ENV['SUPERDOCK_' . strtoupper( get('deploy_env') ) . '_UPLOAD']  );
});

task('sync:db', function () {
    run('mysqldump --host={{deploy_db_host}} --user={{deploy_db_user}} --password={{deploy_db_pass}} {{deploy_db_name}} > {{deploy_path}}/{{deploy_env}}.sql');
    download('{{deploy_path}}/{{deploy_env}}.sql', $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/database/{{deploy_env}}/dist.sql');
    run('rm {{deploy_path}}/{{deploy_env}}.sql');
});

task('sync:format', function () {
    $sql = file_get_contents( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/database/' . get('deploy_env') . '/dist.sql' );
    $sql = str_replace( $_ENV['SUPERDOCK_' . strtoupper( get('deploy_env') ) . '_DOMAIN'], $_ENV['SUPERDOCK_LOCAL_DOMAIN'], $sql );
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
            'mysql --host=superdock_database --user=root --password=root ' . $_ENV['SUPERDOCK_LOCAL_DB_NAME'] . ' < /var/www/html/superdock/database/' . get('deploy_env') . '/local.sql', 
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

desc('sync:migrate');
task('sync:migrate', function () {
    $process = new Process( 
        [ 
            'docker-compose', 
            '-f' . $_ENV['SUPERDOCK_USER_DIR'] . '/.superdock/docker/config.yml', 
            'exec', 
            'webserver', 
            'sh', 
            '-c', 
            './bin/console --no-interaction doctrine:migration:migrate'
        ], 
        null, null, null, null, null
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

desc('sync:elastic:reindex');
task('sync:elastic:reindex', function () {
    $process = new Process( 
        [ 
            'docker-compose', 
            '-f' . $_ENV['SUPERDOCK_USER_DIR'] . '/.superdock/docker/config.yml', 
            'exec', 
            'webserver', 
            'sh', 
            '-c', 
            './bin/console elastic:reindex'
        ], 
        null, null, null, null, null
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
    'sync:format',
    'sync:install',
    'sync:migrate',
    'sync:elastic:reindex',
	'cleanup',
]);
