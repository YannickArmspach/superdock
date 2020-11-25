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
        ->addArgument('env', InputArgument::REQUIRED, 'environement')
        ->addArgument('action', InputArgument::REQUIRED, 'up|down|remove');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln( coreService::start() );

        // coreService::getPassword( $input, $output );
        
        if ( isset( $_ENV['SUPERDOCK_PROJECT_ID'] ) && $_ENV['SUPERDOCK_PROJECT_ID'] ) {

            $env = strtoupper( $input->getArgument('env') );

            envService::docker('digitalocean', $env, $_ENV['SUPERDOCK_' . $env . '_DOMAIN'] );

            switch( $input->getArgument('action') ) {

                case 'up':
                
                    coreService::process([ 
                        'docker-compose', 
                        '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-compose.' . $input->getArgument('env') . '.yml',
                        'up',
                        '-d', 
                        '--build', 
                        '--remove-orphans', 
                        '--force-recreate',
                        '--renew-anon-volumes',
                    ]);

                    //add deploy to create host dir and allow certbot check

                    coreService::process([ 
                        'docker-compose', 
                        '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-compose.' . $input->getArgument('env') . '.yml', 
                        'exec', 
                        'webserver', 
                        'sh', 
                        '-c', 
                        'certbot --webroot -w ' . $_ENV['SUPERDOCK_' . $env . '_DIR'] . '/current' . $_ENV['SUPERDOCK_' . $env . '_DIR_PUBLIC'] . ' -d ' . $_ENV['SUPERDOCK_' . $env . '_DOMAIN'] . ' --email ' . $_ENV['SUPERDOCK_DEV_EMAIL'] . ' --non-interactive --keep-until-expiring --agree-tos certonly'
                    ]);

                    coreService::process([ 
                        'docker-compose', 
                        '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-compose.' . $input->getArgument('env') . '.yml', 
                        'exec', 
                        'webserver', 
                        'sh', 
                        '-c', 
                        'cp /etc/apache2/sites-available/000-default-ssl.conf /etc/apache2/sites-enabled/000-default.conf && service apache2 restart'
                    ]);

                break;
                case 'down':
                    //docker machine dist stop
                break;
                case 'remove':
                    //docker machine dist rm
                break;
                case 'clear':

                    coreService::process([ 
                        'docker-compose', 
                        '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-compose.' . $input->getArgument('env') . '.yml', 
                        'exec', 
                        'webserver', 
                        'sh', 
                        '-c', 
                        'cd ' . $_ENV['SUPERDOCK_' . $env . '_DIR'] . '/current && ' . 'composer install'
                    ]);
                    coreService::process([ 
                        'docker-compose', 
                        '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-compose.' . $input->getArgument('env') . '.yml', 
                        'exec', 
                        'webserver', 
                        'sh', 
                        '-c', 
                        'cd ' . $_ENV['SUPERDOCK_' . $env . '_DIR'] . '/current && chmod 600 public.key && chown www-data:www-data public.key && chmod 600 private.key && chown www-data:www-data private.key'
                    ]);
                    coreService::process([ 
                        'docker-compose', 
                        '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-compose.' . $input->getArgument('env') . '.yml', 
                        'exec', 
                        'webserver', 
                        'sh', 
                        '-c', 
                        'cd ' . $_ENV['SUPERDOCK_' . $env . '_DIR'] . '/current && chmod -R 777 web/sites/default/files && chown -R www-data:www-data web/sites/default/files'
                    ]);
                    coreService::process([ 
                        'docker-compose', 
                        '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-compose.' . $input->getArgument('env') . '.yml', 
                        'exec', 
                        'webserver', 
                        'sh', 
                        '-c', 
                        'cd ' . $_ENV['SUPERDOCK_' . $env . '_DIR'] . '/current && ' . 'php vendor/bin/drush cache:rebuild'
                    ]);
                break;
            }

            $output->writeln( coreService::infos() );
            
            return Command::SUCCESS;

        } else {
            
            $output->writeln( '<fg=black;bg=red> ERROR </> Run <fg=cyan>up</> command fom superdock project folder' );
            return Command::FAILURE;

        }

    }
}
