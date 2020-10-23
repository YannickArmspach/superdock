<?php 

namespace SuperDock\Command;

use icanhazstring\SymfonyConsoleSpinner\SpinnerProgress;
use SuperDock\Service\coreService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class downCommand extends Command
{
    
    protected static $defaultName = 'down';

    public function configure()
    {
        $this->setDescription('Stop the local project');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        coreService::process([ 
            'docker-sync',
            'stop',
            '--config', 
            $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-sync.yml',
        ]);
        
        coreService::process([ 
            'docker-compose', 
            '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-compose.yml', 
            'down', 
            '--remove-orphans' 
        ]);

        coreService::process([ 
            $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/sh/down.sh', 
            $_ENV['PASS'], 
        ]);
        
        return Command::SUCCESS;
    }
}
