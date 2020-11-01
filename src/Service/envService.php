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

	private $dotenv;

	public function __construct()
	{
		$this->dotenv = new Dotenv();
	}

	public function init()
	{
		
		if ( strlen(\Phar::running()) > 0 ) {
			$dir = $_SERVER['HOME'] . '/.superdock';
		} else {
			$dir = dirname(dirname(__DIR__));
		}
		
		$this->dotenv->populate([
			'SUPERDOCK_CORE_DIR' => $dir,
			'SUPERDOCK_USER_DIR' => $_SERVER['HOME'],
			'SUPERDOCK_PROJECT_DIR' => $_SERVER['PWD'],
			'SUPERDOCK_PROJECT_BASENAME' => basename( $_SERVER['PWD'] ),
		]);

		if ( is_file( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.superdock' ) ) $this->dotenv->load( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.superdock');
		if ( is_file( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.local' ) ) $this->dotenv->load( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.local');
		if ( is_file( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.staging' ) ) $this->dotenv->load( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.staging');
		if ( is_file( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.preproduction' ) ) $this->dotenv->load( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.preproduction');
		if ( is_file( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.production' ) ) $this->dotenv->load( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.production');
	
	}

	public function password($input, $output)
	{
		// $helper = new QuestionHelper();
        // $questionPass = new Question('Password: ' );
        // $questionPass->setHidden(true);
        // $questionPass->setHiddenFallback(false);
        // $SUPERDOCK_PASS = $helper->ask($input, $output, $questionPass);
		// $this->dotenv->populate([ 'PASS' => $SUPERDOCK_PASS ]);
	}

	public function docker() {
			
		coreService::process([ 
			'docker-machine', 
			'create',
			'--driver', 
			'virtualbox', 
			'superdock',
		]);
		coreService::process([ 
			'docker-machine', 
			'start',
			'superdock',
		]);

		$env = [];
		$env['DOCKER_TLS_VERIFY'] = 0;
		$env['DOCKER_HOST'] = 0;
		$env['DOCKER_CERT_PATH'] = 0;
		$env['DOCKER_MACHINE_NAME'] = "superdock";

		$process = new Process( 
			[ 
				'docker-machine',
				'config',
				'superdock',
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

		$this->dotenv->populate( $env );

	}

}