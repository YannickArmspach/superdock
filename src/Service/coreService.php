<?php

namespace SuperDock\Service;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\Question;

class coreService
{

	static function install()
	{
		
		$filesystem = new Filesystem();

		try {
		
			//TODO: check why mirror don't copy all files (think about RecursiveIteratorIterator in Filesystem )
			//$filesystem->mirror('inc', $_ENV['SUPERDOCK_USER_DIR'] . '/.superdock/inc', null, ['override' => true, 'copy_on_windows' => true, 'delete' => true ] );
			
			if ( ! file_exists( $_ENV['SUPERDOCK_USER_DIR'] . '/.superdock' ) ) mkdir( $_ENV['SUPERDOCK_USER_DIR'] . '/.superdock', 0777 );

			$source = dirname(dirname(__DIR__)) . "/inc";
			$dest= $_ENV['SUPERDOCK_USER_DIR'] . '/.superdock/inc';
			if ( ! file_exists( $dest ) ) mkdir($dest, 0777);
			$iterator = new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);
			foreach ( $iterator as $item ) {
				// if ( ! is_link( $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName() ) ) {
					if ($item->isDir()) {
						if ( ! file_exists( $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName() ) ) mkdir($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
					} else {
						copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
					}
				// }
			}

			$source = dirname(dirname(__DIR__)) . "/src";
			$dest= $_ENV['SUPERDOCK_USER_DIR'] . '/.superdock/src';
			if ( ! file_exists( $dest ) ) mkdir($dest, 0777);
			$iterator = new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);
			foreach ( $iterator as $item ) {
				if ( ! is_link( $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName() ) ) {
					if ($item->isDir()) {
						if ( ! file_exists( $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName() ) ) mkdir($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
					} else {
						copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
					}
				}
			}

			$source = dirname(dirname(__DIR__)) . "/vendor";
			$dest= $_ENV['SUPERDOCK_USER_DIR'] . '/.superdock/vendor';
			if ( ! file_exists( $dest ) ) mkdir($dest, 0777);
			$iterator = new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);
			foreach ( $iterator as $item ) {
				//if ( ! is_link( $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName() ) ) {
					if ($item->isDir()) {
						if ( ! file_exists( $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName() ) ) mkdir($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
					} else {
						copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
					}
				//}
			}

			$filesystem->chmod( $_ENV['SUPERDOCK_USER_DIR'] . '/.superdock/vendor/deployer/deployer/bin/dep', 0777, 0000, false );
			$filesystem->chmod( $_ENV['SUPERDOCK_USER_DIR'] . '/.superdock/inc/sh', 0777, 0000, true );

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

	static function start()
	{
		$output = PHP_EOL;
		$output .= '<fg=black;bg=green> superdock </> v1.0.0' . PHP_EOL;
		// $output .= PHP_EOL;
		return $output;
	}

	static function infos( $title = "" )
	{
		$output = PHP_EOL;
		$output .= '<fg=black;bg=green> superdock </> ' . $title . PHP_EOL;
		$output .= PHP_EOL;
		$output .= ' Environements: ' . PHP_EOL;
		if( isset( $_ENV['SUPERDOCK_LOCAL_DOMAIN'] ) ) $output .= ' - <fg=green>local</> https://' . $_ENV['SUPERDOCK_LOCAL_DOMAIN'] . PHP_EOL;
		if( isset( $_ENV['SUPERDOCK_STAGING_DOMAIN'] ) ) $output .= ' - <fg=green>staging</> https://' . $_ENV['SUPERDOCK_STAGING_DOMAIN'] . PHP_EOL; 
		if( isset( $_ENV['SUPERDOCK_PREPRODUCTION_DOMAIN'] ) ) $output .= ' - <fg=green>preproduction</> https://' . $_ENV['SUPERDOCK_PREPRODUCTION_DOMAIN'] . PHP_EOL; 
		if( isset( $_ENV['SUPERDOCK_PRODUCTION_DOMAIN'] ) ) $output .= ' - <fg=green>production</> https://' . $_ENV['SUPERDOCK_PRODUCTION_DOMAIN'] . PHP_EOL;
		$output .= PHP_EOL;
		$output .= ' Tools: ' . PHP_EOL;
		$output .= ' - <fg=green>adminer</> http://0.0.0.0:8080' . PHP_EOL;
		$output .= ' - <fg=green>mailhog</> http://0.0.0.0:8025' . PHP_EOL;

		return $output;
		
	}

	static function getPassword($input, $output)
	{
		$dotenv = new Dotenv();
		$helper = new QuestionHelper();
        $questionPass = new Question('Password: ' );
        $questionPass->setHidden(true);
        $questionPass->setHiddenFallback(false);
        $SUPERDOCK_PASS = $helper->ask($input, $output, $questionPass);
		$dotenv->populate([ 'PASS' => $SUPERDOCK_PASS ]);
	}

	static function dir()
	{
		if ( is_file( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.superdock' ) ) {
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

	static function process( $command = [], $output = false  )
	{
		$process = new Process( 
			array_filter( $command ), 
			null, 
			$_ENV, 
			null, 
			null, 
			null 
		);

		if ( $output ) {

			$process->run();
			return $process->getOutput();
		
		} else {
		
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

	static function command( $command = [], $output = false  )
	{
		$process = new Process( 
			array_filter( $command ), 
			null, 
			$_ENV, 
			null, 
			null, 
			null 
		);
		$process->setTty(Process::isTtySupported());
		$process->start();
		$process->wait();
		if ( $output ) return $process->getOutput();
	}

}