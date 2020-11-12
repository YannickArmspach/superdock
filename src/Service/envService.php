<?php

namespace SuperDock\Service;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\Question;

class envService
{

	static function init()
	{
		
		if ( strlen(\Phar::running()) > 0 ) {
			$dir = $_SERVER['HOME'] . '/.superdock';
		} else {
			$dir = dirname(dirname(__DIR__));
		}
		
		$dotenv = new Dotenv();

		$dotenv->populate([
			'SUPERDOCK_CORE_DIR' => $dir,
			'SUPERDOCK_USER_DIR' => $_SERVER['HOME'],
			'SUPERDOCK_PROJECT_DIR' => $_SERVER['PWD'],
			'SUPERDOCK_PROJECT_BASENAME' => basename( $_SERVER['PWD'] ),
		]);

		if ( is_file( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.superdock' ) ) $dotenv->load( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.superdock');
		if ( is_file( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.local' ) ) $dotenv->load( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.local');
		if ( is_file( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.staging' ) ) $dotenv->load( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.staging');
		if ( is_file( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.preproduction' ) ) $dotenv->load( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.preproduction');
		if ( is_file( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.production' ) ) $dotenv->load( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.production');
	
	}

	static function password($input, $output)
	{
		//TODO: prompt and save pass one time on CLI start (by mac keychain ?)
		// $helper = new QuestionHelper();
        // $questionPass = new Question('Password: ' );
        // $questionPass->setHidden(true);
        // $questionPass->setHiddenFallback(false);
        // $SUPERDOCK_PASS = $helper->ask($input, $output, $questionPass);
		// $dotenv->populate([ 'PASS' => $SUPERDOCK_PASS ]);
	}

	static function docker( $docker_machine_driver = 'superdock' ) {

		$dotenv = new Dotenv();

		if ( $docker_machine_driver == 'digitalocean' ) {
		
			$docker_machine_id = 'docker.' . $_ENV['SUPERDOCK_PROJECT_ID'];

			coreService::process([ 
				'docker-machine', 
				'create',
				'--driver', 
				'digitalocean', 
				'--digitalocean-image', 
				$_ENV['SUPERDOCK_STAGING_DIGITALOCEAN_DOKER_IMAGE'],
				'--digitalocean-size',
				$_ENV['SUPERDOCK_STAGING_DIGITALOCEAN_DOKER_SIZE'],
				'--digitalocean-ssh-key-fingerprint',
				$_ENV['SUPERDOCK_STAGING_DIGITALOCEAN_SSH_FINGERPRINT'],
				'--digitalocean-access-token',
				$_ENV['SUPERDOCK_STAGING_DIGITALOCEAN_TOKEN'],
				$docker_machine_id
			]);
		
		} else {

			$docker_machine_id = 'superdock';
			
			coreService::process([ 
				'docker-machine', 
				'create',
				'--driver', 
				'virtualbox', 
				'--virtualbox-disk-size',
				'60000',
				$docker_machine_id
			]);
		
		}
		coreService::process([ 
			'docker-machine', 
			'start',
			$docker_machine_id,
		]);

		$env = [];
		$env['DOCKER_TLS_VERIFY'] = 0;
		$env['DOCKER_HOST'] = 0;
		$env['DOCKER_CERT_PATH'] = 0;
		$env['DOCKER_MACHINE_NAME'] = $docker_machine_id;

		$process = new Process( 
			[ 
				'docker-machine',
				'config',
				$docker_machine_id,
			], 
			null, 
			null, 
			null, 
			null, 
			null 
		);
		$process->run();
		$iterator = $process->getIterator($process::ITER_SKIP_ERR | $process::ITER_KEEP_OUTPUT);
		foreach ($iterator as $data ) {
			$configs = explode( PHP_EOL, $data );
			foreach ( $configs as $config ) {
				$config_data = explode( '=', $config );
				if ( isset( $config_data[0] ) ) {
					switch ( $config_data[0] ) {
						case '--tlsverify';
							$env['DOCKER_TLS_VERIFY'] = 1;
						break;
						case '--tlskey';
							$env['DOCKER_CERT_PATH'] = dirname( str_replace( '"', '', $config_data[1] ) );
						break;
						case '-H';
						$env['DOCKER_HOST'] = $config_data[1];
						break;
					}
				}
			}
		}

		$dotenv->populate( $env );

	}

}