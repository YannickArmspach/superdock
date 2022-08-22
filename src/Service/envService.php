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

	static function docker( $docker_machine_driver = 'superdock', $env = 'LOCAL', $domain = 'superdock.dev' ) {
    /*
		$dotenv = new Dotenv();

		if ( $docker_machine_driver == 'digitalocean' && $env !== 'LOCAL' && isset( $_ENV['SUPERDOCK_' . $env . '_DIGITALOCEAN_NAME'] ) ) {
		
			$docker_machine_id = $_ENV['SUPERDOCK_' . $env . '_DIGITALOCEAN_NAME'];

			coreService::process([ 
				'docker-machine', 
				'create',
				'--driver', 
				'digitalocean', 
				'--digitalocean-region',
				'fra1',
				'--digitalocean-image', 
				$_ENV['SUPERDOCK_' . $env . '_DIGITALOCEAN_DOCKER_IMAGE'],
				'--digitalocean-size',
				$_ENV['SUPERDOCK_' . $env . '_DIGITALOCEAN_DOCKER_SIZE'],
				'--digitalocean-ssh-key-fingerprint',
				$_ENV['SUPERDOCK_' . $env . '_DIGITALOCEAN_SSH_FINGERPRINT'],
				'--digitalocean-access-token',
				$_ENV['SUPERDOCK_' . $env . '_DIGITALOCEAN_TOKEN'],
				$docker_machine_id
			]);

			// --digitalocean-backups										enable backups for droplet [$DIGITALOCEAN_BACKUPS]
			// --digitalocean-image "ubuntu-16-04-x64"								Digital Ocean Image [$DIGITALOCEAN_IMAGE]
			// --digitalocean-ipv6											enable ipv6 for droplet [$DIGITALOCEAN_IPV6]
			// --digitalocean-monitoring										enable monitoring for droplet [$DIGITALOCEAN_MONITORING]
			// --digitalocean-private-networking									enable private networking for droplet [$DIGITALOCEAN_PRIVATE_NETWORKING]
			// --digitalocean-region "nyc3"										Digital Ocean region [$DIGITALOCEAN_REGION]
			// --digitalocean-size "s-1vcpu-1gb"									Digital Ocean size [$DIGITALOCEAN_SIZE]
			// --digitalocean-tags 											comma-separated list of tags to apply to the Droplet [$DIGITALOCEAN_TAGS]
			// --digitalocean-userdata
			
			coreService::process([ 
				'docker-machine', 
				'start',
				$docker_machine_id,
			]);

		} else {

			$docker_machine_id = 'superdock';
			
			exec("docker-machine status superdock | grep Running 2>&1", $output, $return_var); 

			if ( isset( $output[0] ) && $output[0] == 'Running' ) {
			
			} else {

				coreService::process([ 
					'docker-machine', 
					'create',
					'--driver', 
					'virtualbox', 
					'--virtualbox-disk-size',
					'60000',
					$docker_machine_id
				]);

				coreService::process([ 
					'docker-machine', 
					'start',
					$docker_machine_id,
				]);

			}
		
		}

		$env = self::getDockerEnv( $docker_machine_id );
		$dotenv->populate( $env );
    */
	}

	static function getDockerEnv( $docker_machine_id = 'superdock' ) {

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

		$env['DOCKER_MACHINE_IP'] = trim( coreService::process([ 
			'docker-machine', 
			'ip',
			$docker_machine_id,
		], true ) );

		return $env;
	}

}