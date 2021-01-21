<?php 

namespace SuperDock\Command;

use SuperDock\Service\coreService;
use SuperDock\Service\envService;
use SuperDock\Service\notifService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class deployCommand extends Command
{
    
    protected static $defaultName = 'deploy';

    public function configure()
    {
        $this->setDescription('Deploy code on environements')
            ->addArgument('env', InputArgument::REQUIRED, 'environement');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    { 
        
        $output->writeln( coreService::start() );
        
        envService::docker();

        $cmd = [
            $_ENV['SUPERDOCK_CORE_DIR'] . '/vendor/deployer/deployer/bin/dep', 
            '--file=' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/dep/deploy.' . $_ENV['SUPERDOCK_PROJECT_TYPE'] . '.php', 
            'deploy',
            $input->getArgument('env')
        ];
        if ( $input->getOption('verbose') ) array_push( $cmd, '-vvv' ); 
        coreService::process($cmd);
        $output->writeln( coreService::infos( 'Code was successfully deploy to the ' . $input->getArgument('env') . ' environement' ) );
    
        new notifService('Deploy is done', 'message', true);

        return Command::SUCCESS;
    }
}
