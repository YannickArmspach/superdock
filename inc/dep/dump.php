<?php
namespace Deployer;

use SuperDock\Service\coreService;

require 'recipe/common.php';
require 'hosts.php';
require 'settings.php';

task('dump', function () {
    if ( get('deploy_db_host') == 'localhost' ) {
        run('mysqldump --host={{deploy_db_host}} --user={{deploy_db_user}} --password={{deploy_db_pass}} {{deploy_db_name}} > {{deploy_path}}/{{deploy_env}}.sql');
        download('{{deploy_path}}/{{deploy_env}}.sql', $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/database/{{deploy_env}}/dump.sql');
        run('rm {{deploy_path}}/{{deploy_env}}.sql');
    } else {
        runLocally('mysqldump --host={{deploy_db_host}} --user={{deploy_db_user}} --password={{deploy_db_pass}} {{deploy_db_name}} > ' . $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/database/{{deploy_env}}/dump.sql');
    }
});