<?php 

namespace SuperDock\Command;

use SuperDock\Service\coreService;
use SuperDock\Service\envService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class dbInstallCommand extends Command
{
    
    protected static $defaultName = 'db-install';

    public function configure()
    {
        $this->setDescription('Database install to local')
            ->addArgument('path', InputArgument::REQUIRED, 'file path');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln( coreService::start() );
        
        envService::docker();

        if ( file_exists( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/database/' . $input->getArgument('path') ) ) {

        coreService::process([ 
            'docker',
            'compose', 
            '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-compose.yml', 
            'exec', 
            'webserver', 
            'sh', 
            '-c', 
            'mysql -f --host=superdock_database --user=root --password=root ' . $_ENV['SUPERDOCK_LOCAL_DB_NAME'] . ' < /var/www/html/superdock/database/' . $input->getArgument('path')
        ]);

        $output->writeln( coreService::infos( 'The database file ' . $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/database/' . $input->getArgument('path') . ' installed with success in your local environement' ) );
        
        } else {

            $output->writeln( coreService::infos( 'The database file ' . $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/database/' . $input->getArgument('path') . ' not found' ) );
        
        }

        
        return Command::SUCCESS;
    }
}
