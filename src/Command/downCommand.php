<?php 

namespace SuperDock\Command;

use SuperDock\Service\coreService;
use SuperDock\Service\envService;
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
        $output->writeln( coreService::start() );
        
        coreService::getPassword( $input, $output );
        
        envService::docker();

        coreService::process([ 
            'docker',
            'compose', 
            '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-compose.yml', 
            'down', 
            '--remove-orphans' 
        ]);

        coreService::process([ 
            'mutagen', 
            'sync',
            'terminate',
            'superdock',
        ]);
        
        coreService::process([ 
            $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/sh/down.sh', 
            $_ENV['PASS'], 
        ]);
        
        return Command::SUCCESS;
    }
}
