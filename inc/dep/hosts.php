<?php

namespace Deployer;

use Symfony\Component\Dotenv\Dotenv;

(new Dotenv())->overload( dirname( dirname( dirname(__FILE__ ) ) ) . '/.env.staging' );

host('staging')
	->hostname( $_ENV['PROJECTMANAGER_STAGING_SSH_IP'] )
	->port( 22 )
	->user( $_ENV['PROJECTMANAGER_STAGING_SSH_USER'] )
	->set('deploy_env', 'staging')
	->set('deploy_db_user', $_ENV['PROJECTMANAGER_STAGING_DB_USER'] )
	->set('deploy_db_pass', $_ENV['PROJECTMANAGER_STAGING_DB_PASS'] )
	->set('deploy_db_name', $_ENV['PROJECTMANAGER_STAGING_DB_NAME'] )
	->set('deploy_db_host', $_ENV['PROJECTMANAGER_STAGING_DB_HOST'] )
	->set('deploy_path', $_ENV['PROJECTMANAGER_STAGING_DIR'] );

(new Dotenv())->overload( dirname( dirname( dirname(__FILE__ ) ) ) . '/.env.preproduction' );

host('preproduction')
	->hostname( $_ENV['PROJECTMANAGER_PREPRODUCTION_SSH_IP'] )
	->port( 22 )
	->user( $_ENV['PROJECTMANAGER_PREPRODUCTION_SSH_USER'] )
	->set('deploy_env', 'preproduction')
	->set('deploy_db_user', $_ENV['PROJECTMANAGER_PREPRODUCTION_DB_USER'] )
	->set('deploy_db_pass', $_ENV['PROJECTMANAGER_PREPRODUCTION_DB_PASS'] )
	->set('deploy_db_name', $_ENV['PROJECTMANAGER_PREPRODUCTION_DB_NAME'] )
	->set('deploy_db_host', $_ENV['PROJECTMANAGER_PREPRODUCTION_DB_HOST'] )
	->set('deploy_path', $_ENV['PROJECTMANAGER_PREPRODUCTION_DIR'] );

(new Dotenv())->overload( dirname( dirname( dirname(__FILE__ ) ) ) . '/.env.production' );

host('production')
	->hostname( $_ENV['PROJECTMANAGER_PRODUCTION_SSH_IP'] )
	->port( 22 )
	->user( $_ENV['PROJECTMANAGER_PRODUCTION_SSH_USER'] )
	->set('deploy_env', 'production')
	->set('deploy_db_user', $_ENV['PROJECTMANAGER_PRODUCTION_DB_USER'] )
	->set('deploy_db_pass', $_ENV['PROJECTMANAGER_PRODUCTION_DB_PASS'] )
	->set('deploy_db_name', $_ENV['PROJECTMANAGER_PRODUCTION_DB_NAME'] )
	->set('deploy_db_host', $_ENV['PROJECTMANAGER_PRODUCTION_DB_HOST'] )
	->set('deploy_path', $_ENV['PROJECTMANAGER_PRODUCTION_DIR'] );