<?php 

namespace SuperDock\Command;

use icanhazstring\SymfonyConsoleSpinner\SpinnerProgress;
use SuperDock\Service\coreService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class upCommand extends Command
{
    
    protected static $defaultName = 'up';

    public function configure()
    {
        $this->setDescription('Start the local project');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {

        if ( isset( $_ENV['SUPERDOCK_PROJECT_ID'] ) && $_ENV['SUPERDOCK_PROJECT_ID'] ) {

            coreService::process([ 
				'chmod',
				'-R',
				'755', 
				$_ENV['SUPERDOCK_PROJECT_DIR'],
            ]);
            
            coreService::process([ 
                'docker-compose', 
                '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-compose.yml',
                'up',
                '-d', 
                '--build', 
                '--remove-orphans', 
                '--force-recreate',
                '--renew-anon-volumes',
            ]);

            coreService::process([ 
                'mutagen', 
                'sync',
                'terminate',
                'superdock',
            ]);

            coreService::process([ 
                'mutagen', 
                '-c' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/mutagen.yml',
                'sync',
                'create',
                '--name',
                'superdock',
                '~/Projects/futuroscope-scolaire',
                'docker://root@superdock_webserver/var/www/html',
            ]);
            
            if ( ! file_exists( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/certificate/' . $_ENV['SUPERDOCK_LOCAL_DOMAIN'] . '/' . $_ENV['SUPERDOCK_LOCAL_DOMAIN'] . '.pem' ) ) {

                coreService::process([ 
                    $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/sh/cert.sh', 
                    $_ENV['PASS'], 
                    $_ENV['SUPERDOCK_LOCAL_DOMAIN'], 
                    $_ENV['SUPERDOCK_CORE_DIR'], 
                    $_ENV['SUPERDOCK_PROJECT_ID'], 
                    $_ENV['SUPERDOCK_PROJECT_DIR'], 
                ]);

            }

            coreService::process([ 
                $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/sh/up.sh', 
                $_ENV['PASS'], 
                $_ENV['SUPERDOCK_LOCAL_DOMAIN'], 
                $_ENV['SUPERDOCK_CORE_DIR'], 
                $_ENV['SUPERDOCK_PROJECT_ID'], 
            ]);

            $output->writeln( coreService::infos() );
            
            return Command::SUCCESS;

        } else {
            
            $output->writeln( '<fg=black;bg=red> ERROR </> Run <fg=cyan>up</> command fom superdock project folder' );
            return Command::FAILURE;

        }

    }
}
