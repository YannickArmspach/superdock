<?php 

namespace SuperDock\Command;

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

            if ( file_exists( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/custom/build.sh' ) || file_exists( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/custom/build.local.sh' ) ) {

                $buildFilename = file_exists($_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/custom/build.local.sh') ? 'build.local.sh' : 'build.sh';

                coreService::process([ 
                    'docker',
                    'compose', 
                    '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-compose.yml', 
                    'exec', 
                    'webserver', 
                    'sh', 
                    '-c', 
                    'chmod -R 777 superdock/custom/' . $buildFilename
                ]);

                coreService::process([ 
                    'docker',
                    'compose', 
                    '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-compose.yml', 
                    'exec', 
                    'webserver', 
                    'sh', 
                    '-c', 
                    'superdock/custom/' . $buildFilename
                ]);

                $output->writeln( coreService::infos('Project was build') );

            } else {

                $output->writeln( coreService::infos('Build script not found at superdock/custom/build.sh') );

            }

            return Command::SUCCESS;

        } else {
            
            $output->writeln( '<fg=black;bg=red> ERROR </> Run <fg=cyan>up</> command fom superdock project folder' );
            return Command::FAILURE;

        }

    }
}
