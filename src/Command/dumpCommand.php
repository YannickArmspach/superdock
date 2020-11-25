<?php 

namespace SuperDock\Command;

use SuperDock\Service\coreService;
use SuperDock\Service\envService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class dumpCommand extends Command
{
    
    protected static $defaultName = 'dump';

    public function configure()
    {
        $this->setDescription('Dump database from environements')
             ->addArgument('env', InputArgument::REQUIRED, 'environement')
             ->addOption('debug', null, InputOption::VALUE_NONE, 'verbose');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln( coreService::start() );
        
        envService::docker();

        $cmd = [ 
            $_ENV['SUPERDOCK_CORE_DIR'] . '/vendor/deployer/deployer/bin/dep', 
            '--file=' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/dep/dump.php', 
            'dump',
            $input->getArgument('env')
        ];
        if ( $input->getOption('debug') ) array_push( $cmd, '-vvv' ); 
        coreService::process($cmd);
        $output->writeln( coreService::infos( 'The ' . $input->getArgument('env') . ' environement has been successfully saved in superdock/database/' . $input->getArgument('env') . '/dump.sql' ) );

        return Command::SUCCESS;
    }
}
