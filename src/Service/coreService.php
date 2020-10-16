<?php

namespace SuperDock\Service;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Dotenv\Dotenv;

class coreService
{

	static function install()
	{
		
		$filesystem = new Filesystem();

		try {
		
			$filesystem->mirror('inc', $_ENV['SUPERDOCK_USER_DIR'] . '/.superdock' );
		
		} catch (IOExceptionInterface $exception) {

			echo "An error occurred while creating your directory at " . $exception->getPath();
			return false;

		}

		return true;
	}

	static function update()
	{
		return false;	
	}

	static function uninstall()
	{
		return false;	
	}

	static function env()
	{
		
		$dotenv = new Dotenv();

		$dotenv->populate([
			'PASS' => 'handy',
			'SUPERDOCK_CORE_DIR' => dirname(dirname(__DIR__)),
			'SUPERDOCK_USER_DIR' => $_SERVER['HOME'],
			'SUPERDOCK_PROJECT_DIR' => $_SERVER['PWD']
		]);

		if ( file_exists( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.superdock' ) ) $dotenv->load( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.superdock');
		if ( file_exists( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.local' ) ) $dotenv->load( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.local');
		if ( file_exists( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.staging' ) ) $dotenv->load( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.staging');
		if ( file_exists( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.preproduction' ) ) $dotenv->load( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.preproduction');
		if ( file_exists( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.production' ) ) $dotenv->load( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.production');
	
	}

	static function envPopulate($envs = [])
	{
		
		$dotenv = new Dotenv();

		$dotenv->populate($envs);

	}

	static function dir()
	{
		if ( isset( $_ENV['SUPERDOCK_LOCAL_DOMAIN'] ) && ! is_dir( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/certificate/' . $_ENV['SUPERDOCK_LOCAL_DOMAIN'] ) ) {
			mkdir( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/certificate/' . $_ENV['SUPERDOCK_LOCAL_DOMAIN'], 0777, true );
		}
		if ( ! is_dir( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/database' ) ) {
			mkdir( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/database', 0777, true );
		}
		if ( ! is_dir( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/database/local' ) ) {
			mkdir( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/database/local', 0777, true );
		}
		if ( ! is_dir( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/database/staging' ) ) {
			mkdir( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/database/staging', 0777, true );
		}
		if ( ! is_dir( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/database/preproduction' ) ) {
			mkdir( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/database/preproduction', 0777, true );
		}
		if ( ! is_dir( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/database/production' ) ) {
			mkdir( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/database/production', 0777, true );
		}
		if ( ! is_dir( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/overwrite' ) ) {
			mkdir( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/overwrite', 0777, true );
		}	
	}

}