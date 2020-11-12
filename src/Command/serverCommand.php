<?php 

namespace SuperDock\Command;

use icanhazstring\SymfonyConsoleSpinner\SpinnerProgress;
use SuperDock\Service\coreService;
use SuperDock\Service\envService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Process\Process;

class serverCommand extends Command
{
    
    protected static $defaultName = 'server';

    public function configure()
    {
        $this->setDescription('Distant server manager')
        ->addArgument('action', InputArgument::REQUIRED, 'init|start|stop')
        ->addArgument('env', InputArgument::REQUIRED, 'environement');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln( coreService::start() );

        coreService::getPassword( $input, $output );
        
        envService::docker('digitalocean');

        if ( isset( $_ENV['SUPERDOCK_PROJECT_ID'] ) && $_ENV['SUPERDOCK_PROJECT_ID'] ) {

            coreService::process([ 
                'docker-compose', 
                '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-compose.server.yml',
                'up',
                '-d', 
                '--build', 
                '--remove-orphans', 
                '--force-recreate',
                '--renew-anon-volumes',
            ]);

            coreService::process([ 
                'docker-compose', 
                '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-compose.server.yml', 
                'exec', 
                'webserver', 
                'sh', 
                '-c', 
                'certbot --webroot -w ' . $_ENV['SUPERDOCK_LOCAL_DIR'] . '' . $_ENV['SUPERDOCK_LOCAL_DIR_PUBLIC'] . ' -d ' . $_ENV['SUPERDOCK_LOCAL_DOMAIN'] . ' --email ' . $_ENV['SUPERDOCK_DEV_EMAIL'] . ' --non-interactive --agree-tos certonly'
            ]);

            $output->writeln( coreService::infos() );
            
            return Command::SUCCESS;

        } else {
            
            $output->writeln( '<fg=black;bg=red> ERROR </> Run <fg=cyan>up</> command fom superdock project folder' );
            return Command::FAILURE;

        }

    }
}
