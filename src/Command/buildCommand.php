<?php 

namespace SuperDock\Command;

use icanhazstring\SymfonyConsoleSpinner\SpinnerProgress;
use SuperDock\Service\coreService;
use SuperDock\Service\envService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class buildCommand extends Command
{
    
    protected static $defaultName = 'build';

    public function configure()
    {
        $this->setDescription('Build project. Execute bash script from your project (/superdock/custom/build.sh)');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln( coreService::start() );
        
        envService::docker();

        if ( isset( $_ENV['SUPERDOCK_PROJECT_ID'] ) && $_ENV['SUPERDOCK_PROJECT_ID'] ) {

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

            $output->writeln( coreService::infos('Project was build') );
            
            return Command::SUCCESS;

        } else {
            
            $output->writeln( '<fg=black;bg=red> ERROR </> Run <fg=cyan>up</> command fom superdock project folder' );
            return Command::FAILURE;

        }

    }
}
