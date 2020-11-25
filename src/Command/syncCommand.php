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

class syncCommand extends Command
{
    
    protected static $defaultName = 'sync';

    public function configure()
    {
        $this->setDescription('Synchronize database and media from/to environements')
        ->addOption('from', null, InputOption::VALUE_NONE, 'sync from')
        ->addOption('to', null, InputOption::VALUE_NONE, 'sync to')
        ->addArgument('env', InputArgument::REQUIRED, 'environement')
        ->addOption('debug', null, InputOption::VALUE_NONE, 'verbose');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln( coreService::start() );

        envService::docker();
        
        if ( $input->getOption('to') == true ) {

            $cmd = [ 
                $_ENV['SUPERDOCK_CORE_DIR'] . '/vendor/deployer/deployer/bin/dep', 
                '--file=' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/dep/sync-to.php', 
                'sync',
                $input->getArgument('env')
            ];
            if ( $input->getOption('debug') ) array_push( $cmd, '-vvv' ); 
            coreService::process($cmd);
            $output->writeln( coreService::infos( 'The ' . $input->getArgument('env') . ' environement has been successfully synchronized with local' ) );
        
            new notifService('Sync to is done', 'message', true);

        } else {
            
            $cmd = [ 
                $_ENV['SUPERDOCK_CORE_DIR'] . '/vendor/deployer/deployer/bin/dep', 
                '--file=' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/dep/sync-from.php', 
                'sync',
                $input->getArgument('env')
            ];
            if ( $input->getOption('debug') ) array_push( $cmd, '-vvv' ); 
            coreService::process($cmd);
            $output->writeln( coreService::infos( 'The local environement has been successfully synchronized with ' . $input->getArgument('env') ) );

            new notifService('Sync from is done', 'message', true);

        }

        switch ( $_ENV['SUPERDOCK_PROJECT_TYPE'] ) {
            case 'symfony':
            break;
            case 'drupal':
            break;
            case 'wordpress':
            break;
        }

        return Command::SUCCESS;
    }
}
