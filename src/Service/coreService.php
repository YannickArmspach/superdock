<?php

namespace SuperDock\Service;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Process\Process;

class coreService
{

	static function install()
	{
		
		$filesystem = new Filesystem();

		try {
		
			//TODO: check why mirror don't copy all files (think about RecursiveIteratorIterator in Filesystem )
			//$filesystem->mirror('inc', $_ENV['SUPERDOCK_USER_DIR'] . '/.superdock/inc', null, ['override' => true, 'copy_on_windows' => true, 'delete' => true ] );
			
			if ( ! file_exists( $_ENV['SUPERDOCK_USER_DIR'] . '/.superdock' ) ) mkdir( $_ENV['SUPERDOCK_USER_DIR'] . '/.superdock', 0755 );

			$source = "./inc";
			$dest= $_ENV['SUPERDOCK_USER_DIR'] . '/.superdock/inc';

			if ( ! file_exists( $dest ) ) mkdir($dest, 0755);
			foreach (
			$iterator = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
			\RecursiveIteratorIterator::SELF_FIRST) as $item
			) {
			if ($item->isDir()) {
				if ( ! file_exists( $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName() ) ) mkdir($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
			} else {
				copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
			}
			}

			$source = "./src";
			$dest= $_ENV['SUPERDOCK_USER_DIR'] . '/.superdock/src';

			if ( ! file_exists( $dest ) ) mkdir($dest, 0755);
			foreach (
			$iterator = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
			\RecursiveIteratorIterator::SELF_FIRST) as $item
			) {
			if ($item->isDir()) {
				if ( ! file_exists( $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName() ) ) mkdir($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
			} else {
				copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
			}
			}

			$source = "./vendor";
			$dest= $_ENV['SUPERDOCK_USER_DIR'] . '/.superdock/vendor';

			if ( ! file_exists( $dest ) ) mkdir($dest, 0755);
			foreach (
			$iterator = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
			\RecursiveIteratorIterator::SELF_FIRST) as $item
			) {
			if ($item->isDir()) {
				if ( ! file_exists( $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName() ) ) mkdir($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
			} else {
				copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
			}
			}

			$filesystem->chmod( $_ENV['SUPERDOCK_USER_DIR'] . '/.superdock/vendor/deployer/deployer/bin/dep', 0755, 0000, false );
			$filesystem->chmod( $_ENV['SUPERDOCK_USER_DIR'] . '/.superdock/inc/sh', 0755, 0000, true );

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

	static function infos( $title = "" )
	{
		$output = PHP_EOL;
		$output .= '<fg=black;bg=green> superdock </> ' . $title . PHP_EOL;
		$output .= PHP_EOL;
		if( isset( $_ENV['SUPERDOCK_LOCAL_DOMAIN'] ) ) $output .= '<fg=green> local</> https://' . $_ENV['SUPERDOCK_LOCAL_DOMAIN'] . PHP_EOL;
		if( isset( $_ENV['SUPERDOCK_STAGING_DOMAIN'] ) ) $output .= '<fg=green> staging</> https://' . $_ENV['SUPERDOCK_STAGING_DOMAIN'] . PHP_EOL; 
		if( isset( $_ENV['SUPERDOCK_PREPRODUCTION_DOMAIN'] ) ) $output .= '<fg=green> preproduction</> https://' . $_ENV['SUPERDOCK_PREPRODUCTION_DOMAIN'] . PHP_EOL; 
		if( isset( $_ENV['SUPERDOCK_PRODUCTION_DOMAIN'] ) ) $output .= '<fg=green> production</> https://' . $_ENV['SUPERDOCK_PRODUCTION_DOMAIN'] . PHP_EOL;
		return $output;
		
	}

	static function env()
	{
		
		$dotenv = new Dotenv();

		if ( strlen(\Phar::running()) > 0 ) {
			$dir = $_SERVER['HOME'] . '/.superdock';
		} else {
			$dir = dirname(dirname(__DIR__));
		}
		
		$dotenv->populate([
			'PASS' => 'handy',
			'SUPERDOCK_CORE_DIR' => $dir,
			'SUPERDOCK_USER_DIR' => $_SERVER['HOME'],
			'SUPERDOCK_PROJECT_DIR' => $_SERVER['PWD'],
			'SUPERDOCK_PROJECT_BASENAME' => basename( $_SERVER['PWD'] )
		]);

		if ( file_exists( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.superdock' ) ) $dotenv->load( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.superdock');
		if ( file_exists( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.local' ) ) $dotenv->load( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.local');
		if ( file_exists( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.staging' ) ) $dotenv->load( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.staging');
		if ( file_exists( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.preproduction' ) ) $dotenv->load( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.preproduction');
		if ( file_exists( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.production' ) ) $dotenv->load( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.production');
	
	}

	static function dir()
	{
		if ( file_exists( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.superdock' ) ) {
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
			if ( ! is_dir( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/custom' ) ) {
				mkdir( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/custom', 0777, true );
			}
		}	
	}

	static function process( $command = [] )
	{
		$process = new Process( $command, null, null, null, null, null );
		$process->setTty(Process::isTtySupported());
		$process->run(function ($type, $buffer) {
			if (Process::ERR === $type) {
				echo $buffer;
			} else {
				echo $buffer;
			}
		});
	}

}