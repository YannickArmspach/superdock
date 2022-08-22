<?php 

namespace SuperDock\Command;

use SuperDock\Service\coreService;
use SuperDock\Service\envService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class composerCommand extends Command
{
    
    protected static $defaultName = 'composer';

    public function configure()
    {
        $this->setDescription('Execute composer command in local container')
        ->addArgument('execute_command', InputArgument::REQUIRED, 'command')
        ->addArgument('execute_argument', InputArgument::REQUIRED, 'argument');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    { 
        $output->writeln( coreService::start() );
        
        envService::docker();

        coreService::process([ 
            'docker',
            'compose', 
            '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-compose.yml', 
            'exec', 
            'webserver', 
            'sh', 
            '-c', 
            'composer ' . $input->getArgument('execute_command') . ' ' . $input->getArgument('execute_argument') . ' --no-cache -vvv'
        ]);
        
        $output->writeln( coreService::infos( 'Your command "composer ' . $input->getArgument('execute_command') . ' ' . $input->getArgument('execute_argument') . '" was executed in local container' ) );
    
        return Command::SUCCESS;
    }
}
