<?php 

namespace SuperDock\Command;

use SuperDock\Service\coreService;
use SuperDock\Service\envService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class dbDumpCommand extends Command
{
    
    protected static $defaultName = 'db-dump';

    public function configure()
    {
        $this->setDescription('Database dump from environements')
            ->addArgument('env', InputArgument::REQUIRED, 'environement');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln( coreService::start() );
        
        envService::docker();

        if ( $input->getArgument('env') == 'local' ) {
                
            coreService::process([ 
                'docker-compose', 
                '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-compose.yml', 
                'exec', 
                'webserver', 
                'sh', 
                '-c', 
                'mysqldump --no-tablespaces --host=superdock_database --user=root --password=root ' . $_ENV['SUPERDOCK_LOCAL_DB_NAME'] . ' > /var/www/html/superdock/database/local/dump.' . date("Y.m.d-H:i:s") . '.sql'
            ]);
        
        } else {

            $cmd = [ 
                $_ENV['SUPERDOCK_CORE_DIR'] . '/vendor/deployer/deployer/bin/dep', 
                '--file=' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/dep/dump.php', 
                'dump',
                $input->getArgument('env')
            ];
            if ( $input->getOption('verbose') ) array_push( $cmd, '-vvv' ); 
            coreService::process($cmd);

        }

        $output->writeln( coreService::infos( 'The database dumped from ' . $input->getArgument('env') . ' in superdock/database/' . $input->getArgument('env') . '/dump.{timestamp}.sql' ) );

        return Command::SUCCESS;
    }
}
