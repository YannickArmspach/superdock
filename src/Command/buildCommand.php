<?php 

namespace SuperDock\Command;

use icanhazstring\SymfonyConsoleSpinner\SpinnerProgress;
use SuperDock\Service\coreService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class buildCommand extends Command
{
    
    protected static $defaultName = 'build';

    public function configure()
    {
        $this->setDescription('Build project');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {

        if ( isset( $_ENV['SUPERDOCK_PROJECT_ID'] ) && $_ENV['SUPERDOCK_PROJECT_ID'] ) {

            coreService::process([ 
                'docker-compose', 
                '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-compose.yaml', 
                'exec', 
                'webserver', 
                'sh', 
                '-c', 
                'npm rebuild node-sass && npm install', 
            ]);

            $output->writeln( coreService::infos('Project was build') );
            
            return Command::SUCCESS;

        } else {
            
            $output->writeln( '<fg=black;bg=red> ERROR </> Run <fg=cyan>up</> command fom superdock project folder' );
            return Command::FAILURE;

        }

    }
}
