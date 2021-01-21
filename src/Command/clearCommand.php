<?php 

namespace SuperDock\Command;

use SuperDock\Service\coreService;
use SuperDock\Service\envService;
use SuperDock\Service\notifService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class clearCommand extends Command
{
    
    protected static $defaultName = 'clear';

    public function configure()
    {
        $this->setDescription('Clear project (cache and specific project type commands)');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln( coreService::start() );
        
        envService::docker();

        if ( isset( $_ENV['SUPERDOCK_PROJECT_ID'] ) && $_ENV['SUPERDOCK_PROJECT_ID'] ) {

            switch ( $_ENV['SUPERDOCK_PROJECT_TYPE'] ) {
                case 'symfony':
                    coreService::process([ 
                        'docker-compose', 
                        '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-compose.yml', 
                        'exec', 
                        'webserver', 
                        'sh', 
                        '-c', 
                        'chmod -R 777 var'
                    ]);
                    coreService::process([ 
                        'docker-compose', 
                        '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-compose.yml', 
                        'exec', 
                        'webserver', 
                        'sh', 
                        '-c', 
                        'php bin/console cache:clear'
                    ]);
                break;
                case 'drupal':
                    coreService::process([
                        'docker-compose',
                        '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-compose.yml',
                        'exec',
                        'webserver',
                        'sh',
                        '-c',
                        'php vendor/drush/drush/drush.php cache:rebuild'
                    ]);
                    coreService::process([ 
                        'docker-compose', 
                        '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-compose.yml', 
                        'exec', 
                        'webserver', 
                        'sh', 
                        '-c', 
                        'echo "SHOW TABLES LIKE cache%" | $(php vendor/drush/drush/drush.php sql-connect) | tail -n +2 | xargs -L1 -I% echo "TRUNCATE TABLE %;" | $(php vendor/drush/drush/drush.php sql-connect) -v'
                    ]);
                    

                break;
                case 'wordpress':
                break;
            }
            
            $output->writeln( coreService::infos('Cache clear done !') );
            
            new notifService('Cache clear done !', 'message', true);
            
            return Command::SUCCESS;

        } else {
            
            $output->writeln( '<fg=black;bg=red> ERROR </> Run <fg=cyan>up</> command fom superdock project folder' );
            return Command::FAILURE;

        }

    }
}
