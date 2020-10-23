<?php 

namespace SuperDock\Command;

use icanhazstring\SymfonyConsoleSpinner\SpinnerProgress;
use SuperDock\Service\coreService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class watchCommand extends Command
{
    
    protected static $defaultName = 'watch';

    public function configure()
    {
        $this->setDescription('Watch and rebuild prepross files');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {

        if ( isset( $_ENV['SUPERDOCK_PROJECT_ID'] ) && $_ENV['SUPERDOCK_PROJECT_ID'] ) {

            coreService::process([ 
                'docker-compose', 
                '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-compose.yml', 
                'exec', 
                'webserver', 
                'sh', 
                '-c', 
                './node_modules/.bin/encore dev --watch', 
            ]);
            
            $output->writeln( coreService::infos('watching files') );
            
            return Command::SUCCESS;

        } else {
            
            $output->writeln( '<fg=black;bg=red> ERROR </> Run <fg=cyan>up</> command fom superdock project folder' );
            return Command::FAILURE;

        }

    }
}
