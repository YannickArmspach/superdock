<?php 

namespace SuperDock\Command;

use icanhazstring\SymfonyConsoleSpinner\SpinnerProgress;
use SuperDock\Service\coreService;
use SuperDock\Service\envService;
use SuperDock\Service\notifService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Dotenv\Dotenv;
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
        $output->writeln( coreService::start() );

        coreService::getPassword( $input, $output );

        envService::docker();
        
        if ( isset( $_ENV['SUPERDOCK_PROJECT_ID'] ) && $_ENV['SUPERDOCK_PROJECT_ID'] ) {

            //create certs
            if ( ! file_exists( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/certificate/' . $_ENV['SUPERDOCK_LOCAL_DOMAIN'] . '/' . $_ENV['SUPERDOCK_LOCAL_DOMAIN'] . '.pem' ) ) 
            {
                coreService::process([ 
                    $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/sh/cert.sh', 
                    $_ENV['PASS'], 
                    $_ENV['SUPERDOCK_LOCAL_DOMAIN'], 
                    $_ENV['SUPERDOCK_CORE_DIR'], 
                    $_ENV['SUPERDOCK_PROJECT_ID'], 
                    $_ENV['SUPERDOCK_PROJECT_DIR'], 
                ]);
            }

            //load env and create hosts
            coreService::process([ 
                $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/sh/env.sh', 
                $_ENV['PASS'], 
                $_ENV['SUPERDOCK_LOCAL_DOMAIN'], 
                $_ENV['SUPERDOCK_CORE_DIR'], 
                $_ENV['SUPERDOCK_PROJECT_ID'], 
            ]);

            //start docker composer
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

            //enable mutagen
            if ( isset( $_ENV['SUPERDOCK_MUTAGEN'] ) && $_ENV['SUPERDOCK_MUTAGEN'] ) 
            {
                coreService::process([ 
                    'mutagen', 
                    '-c' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/mutagen.yml',
                    'sync',
                    'create',
                    '--name',
                    'superdock',
                    $_ENV['SUPERDOCK_PROJECT_DIR'],
                    'docker://root@superdock_webserver/var/www/html',
                ]);
                function mutagen_connect_retry( ) 
                {
                    $process = new Process( [ 'mutagen', 'sync', 'list' ], null, null, null, null, null );
                    $process->start();
                    $process->wait();
                    if( strpos( $process->getOutput(), 'Watching for changes' ) !== false ) {
                        echo "✔ Mutagen connected" . PHP_EOL;
                        return false;
                    } else{
                       echo "➤ Waiting mutagen start..." . PHP_EOL;
                       return true;
                    }
                }
                $active = true;
                while($active) {
                    $active = mutagen_connect_retry();
                    sleep(5);
                }
            }

            switch ( $_ENV['SUPERDOCK_PROJECT_TYPE'] ) {
                case 'symfony':
                break;
                case 'drupal':
                break;
                case 'wordpress':
                break;
            }

            if ( file_exists( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/custom/build.sh' ) ) {

                coreService::process([ 
                    'docker-compose', 
                    '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-compose.yml', 
                    'exec', 
                    'webserver', 
                    'sh', 
                    '-c', 
                    'chmod -R 777 superdock/custom/build.sh'
                ]);

                coreService::process([ 
                    'docker-compose', 
                    '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-compose.yml', 
                    'exec', 
                    'webserver', 
                    'sh', 
                    '-c', 
                    'superdock/custom/build.sh'
                ]);

            }
            
            $output->writeln( coreService::infos() );
            
            new notifService('Superdock is up', 'message', true);

            return Command::SUCCESS;

        } else {
            
            $output->writeln( '<fg=black;bg=red> ERROR </> Run <fg=cyan>up</> command fom superdock project folder' );
            return Command::FAILURE;

        }

    }
}
