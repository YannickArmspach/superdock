<?php

namespace Deployer;

use SuperDock\Service\coreService;

coreService::env();

host('staging')
	->hostname( $_ENV['SUPERDOCK_STAGING_SSH_IP'] )
	->port( 22 )
	->user( $_ENV['SUPERDOCK_STAGING_SSH_USER'] )
	->set('deploy_env', 'staging')
	->set('deploy_db_user', $_ENV['SUPERDOCK_STAGING_DB_USER'] )
	->set('deploy_db_pass', $_ENV['SUPERDOCK_STAGING_DB_PASS'] )
	->set('deploy_db_name', $_ENV['SUPERDOCK_STAGING_DB_NAME'] )
	->set('deploy_db_host', $_ENV['SUPERDOCK_STAGING_DB_HOST'] )
	->set('deploy_path', $_ENV['SUPERDOCK_STAGING_DIR'] );

host('preproduction')
	->hostname( $_ENV['SUPERDOCK_PREPRODUCTION_SSH_IP'] )
	->port( 22 )
	->user( $_ENV['SUPERDOCK_PREPRODUCTION_SSH_USER'] )
	->set('deploy_env', 'preproduction')
	->set('deploy_db_user', $_ENV['SUPERDOCK_PREPRODUCTION_DB_USER'] )
	->set('deploy_db_pass', $_ENV['SUPERDOCK_PREPRODUCTION_DB_PASS'] )
	->set('deploy_db_name', $_ENV['SUPERDOCK_PREPRODUCTION_DB_NAME'] )
	->set('deploy_db_host', $_ENV['SUPERDOCK_PREPRODUCTION_DB_HOST'] )
	->set('deploy_path', $_ENV['SUPERDOCK_PREPRODUCTION_DIR'] );

host('production')
	->hostname( $_ENV['SUPERDOCK_PRODUCTION_SSH_IP'] )
	->port( 22 )
	->user( $_ENV['SUPERDOCK_PRODUCTION_SSH_USER'] )
	->set('deploy_env', 'production')
	->set('deploy_db_user', $_ENV['SUPERDOCK_PRODUCTION_DB_USER'] )
	->set('deploy_db_pass', $_ENV['SUPERDOCK_PRODUCTION_DB_PASS'] )
	->set('deploy_db_name', $_ENV['SUPERDOCK_PRODUCTION_DB_NAME'] )
	->set('deploy_db_host', $_ENV['SUPERDOCK_PRODUCTION_DB_HOST'] )
	->set('deploy_path', $_ENV['SUPERDOCK_PRODUCTION_DIR'] );