<?php 

namespace SuperDock\Command;

use icanhazstring\SymfonyConsoleSpinner\SpinnerProgress;
use SuperDock\Service\coreService;
use SuperDock\Service\envService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class killCommand extends Command
{
    
    protected static $defaultName = 'kill';

    public function configure()
    {
        $this->setDescription('Kill all running docker instance');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln( coreService::start() );
        
        coreService::getPassword( $input, $output );
        
        envService::docker();

        coreService::process([ 
            $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/sh/kill.sh', 
            $_ENV['PASS'], 
        ]);
        
        $output->writeln( '<fg=black;bg=green> RHAAAAHH </> You killed them all. Now, you can start fresh and zen ;)' );
        return Command::SUCCESS;
    }
}
