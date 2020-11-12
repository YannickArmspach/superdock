<?php
namespace Deployer;

use SuperDock\Service\coreService;

require 'recipe/common.php';
require 'hosts.php';
require 'settings.php';

task('dump', function () {
    $SUPERDOCK = get('SUPERDOCK');
    if ( get('deploy_env') == 'local' ) {
        coreService::process([ 
            'docker-compose', 
            '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-compose.yml', 
            'exec', 
            'webserver', 
            'sh', 
            '-c', 
            'mysqldump --host=superdock_database --user=root --password=root ' . $_ENV['SUPERDOCK_LOCAL_DB_NAME'] . ' > /var/www/html/superdock/database/local/local.sql'
        ]);
    } else {
        //TODO: add secure file name to dist dump before remove for security
        run('mysqldump --host={{deploy_db_host}} --user={{deploy_db_user}} --password={{deploy_db_pass}} {{deploy_db_name}} > {{deploy_path}}/{{deploy_env}}.sql');
        download('{{deploy_path}}/{{deploy_env}}.sql', $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/database/{{deploy_env}}/dump.sql');
        run('rm {{deploy_path}}/{{deploy_env}}.sql');
    }
});