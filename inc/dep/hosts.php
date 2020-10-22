<?php

namespace Deployer;

use SuperDock\Service\coreService;

coreService::env();

if ( isset( $_ENV['SUPERDOCK_STAGING_SSH_IP'] ) ) {
	host('staging')
		->hostname( $_ENV['SUPERDOCK_STAGING_SSH_IP'] )
		->port( (int) $_ENV['SUPERDOCK_STAGING_SSH_PORT'] )
		->user( $_ENV['SUPERDOCK_STAGING_SSH_USER'] )
		->set('deploy_env', 'staging')
		->set('deploy_db_user', $_ENV['SUPERDOCK_STAGING_DB_USER'] )
		->set('deploy_db_pass', $_ENV['SUPERDOCK_STAGING_DB_PASS'] )
		->set('deploy_db_name', $_ENV['SUPERDOCK_STAGING_DB_NAME'] )
		->set('deploy_db_host', $_ENV['SUPERDOCK_STAGING_DB_HOST'] )
		->set('deploy_path', $_ENV['SUPERDOCK_STAGING_DIR'] );
}

if ( isset( $_ENV['SUPERDOCK_PREPRODUCTION_SSH_IP'] ) ) {
	host('preproduction')
		->hostname( $_ENV['SUPERDOCK_PREPRODUCTION_SSH_IP'] )
		->port( (int) $_ENV['SUPERDOCK_PREPRODUCTION_SSH_PORT'] )
		->user( $_ENV['SUPERDOCK_PREPRODUCTION_SSH_USER'] )
		->set('deploy_env', 'preproduction')
		->set('deploy_db_user', $_ENV['SUPERDOCK_PREPRODUCTION_DB_USER'] )
		->set('deploy_db_pass', $_ENV['SUPERDOCK_PREPRODUCTION_DB_PASS'] )
		->set('deploy_db_name', $_ENV['SUPERDOCK_PREPRODUCTION_DB_NAME'] )
		->set('deploy_db_host', $_ENV['SUPERDOCK_PREPRODUCTION_DB_HOST'] )
		->set('deploy_path', $_ENV['SUPERDOCK_PREPRODUCTION_DIR'] );
}

if ( isset( $_ENV['SUPERDOCK_PRODUCTION_SSH_IP'] ) ) {
	host('production')
		->hostname( $_ENV['SUPERDOCK_PRODUCTION_SSH_IP'] )
		->port( (int) $_ENV['SUPERDOCK_PRODUCTION_SSH_PORT'] )
		->user( $_ENV['SUPERDOCK_PRODUCTION_SSH_USER'] )
		->set('deploy_env', 'production')
		->set('deploy_db_user', $_ENV['SUPERDOCK_PRODUCTION_DB_USER'] )
		->set('deploy_db_pass', $_ENV['SUPERDOCK_PRODUCTION_DB_PASS'] )
		->set('deploy_db_name', $_ENV['SUPERDOCK_PRODUCTION_DB_NAME'] )
		->set('deploy_db_host', $_ENV['SUPERDOCK_PRODUCTION_DB_HOST'] )
		->set('deploy_path', $_ENV['SUPERDOCK_PRODUCTION_DIR'] );
}

set('SUPERDOCK', function() {
	$SUPERDOCK = [];
	$SUPERDOCK['SOURCE_DIR'] = $_ENV['SUPERDOCK_PROJECT_DIR'];
	$SUPERDOCK['SOURCE_UPLOAD'] = $_ENV['SUPERDOCK_LOCAL_UPLOAD'];
	switch( get('deploy_env') ) {
		case "local":
			$SUPERDOCK['DIST_DIR'] = $_ENV['SUPERDOCK_LOCAL_DIR'];
			$SUPERDOCK['DIST_UPLOAD'] = $_ENV['SUPERDOCK_LOCAL_UPLOAD'];
		break;
		case "staging":
			$SUPERDOCK['DIST_DIR'] = $_ENV['SUPERDOCK_STAGING_DIR'];
			$SUPERDOCK['DIST_UPLOAD'] = $_ENV['SUPERDOCK_STAGING_UPLOAD'];
		break;
	}
	return $SUPERDOCK;
} );