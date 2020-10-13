<?php 

namespace SuperDock\Command;

use icanhazstring\SymfonyConsoleSpinner\SpinnerProgress;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class upCommand extends Command
{
    
    protected static $defaultName = 'up';

    public function configure()
    {
        $this->setDescription('Start the local project');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {

        if ( isset( $_ENV['SUPERDOCK_PROJECT_ID'] ) && $_ENV['SUPERDOCK_PROJECT_ID'] ) {

            if ( ! is_dir( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/certificate/' . $_ENV['SUPERDOCK_LOCAL_DOMAIN'] ) ) {
                mkdir( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/certificate/' . $_ENV['SUPERDOCK_LOCAL_DOMAIN'], 0777, true );
            }
            if ( ! is_dir( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/database' ) ) {
                mkdir( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/database', 0777, true );
            }
            if ( ! is_dir( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/overwrite' ) ) {
                mkdir( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/overwrite', 0777, true );
            }

            $process = new Process( 
                [ 
                    'docker-compose', 
                    '-f' . $_ENV['SUPERDOCK_USER_DIR'] . '/.superdock/docker/config.yml', 
                    'up', 
                    '-d', 
                    '--build', 
                    '--remove-orphans', 
                    '--force-recreate' 
                ]
            );
            $process->start();
            $spinner = new SpinnerProgress( $output );
            $spinner->setMessage('starting ' . $_ENV['SUPERDOCK_PROJECT_ID']);
            while ($process->isRunning()) {
                $spinner->advance();
                usleep(5000);
            }
            if ( $process->isSuccessful() ) {
                $spinner->finish();
            }

            if ( ! file_exists( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/certificate/' . $_ENV['SUPERDOCK_LOCAL_DOMAIN'] . '/' . $_ENV['SUPERDOCK_LOCAL_DOMAIN'] . '.pem' ) ) {

                $process = new Process( 
                    [ 
                        $_ENV['SUPERDOCK_USER_DIR'] . '/.superdock/sh/cert.sh', 
                        $_ENV['PASS'], 
                        $_ENV['SUPERDOCK_LOCAL_DOMAIN'], 
                        $_ENV['SUPERDOCK_CORE_DIR'], 
                        $_ENV['SUPERDOCK_PROJECT_ID'], 
                        $_ENV['SUPERDOCK_PROJECT_DIR'], 
                    ]
                );
                $process->run(function ($type, $buffer) {
                    if (Process::ERR === $type) {
                        // echo $buffer;
                    } else {
                        //echo $buffer;
                    }
                });

            }

            $process = new Process( 
                [ 
                    $_ENV['SUPERDOCK_USER_DIR'] . '/.superdock/sh/up.sh', 
                    $_ENV['PASS'], 
                    $_ENV['SUPERDOCK_LOCAL_DOMAIN'], 
                    $_ENV['SUPERDOCK_CORE_DIR'], 
                    $_ENV['SUPERDOCK_PROJECT_ID'], 
                ]
            );
            $process->run(function ($type, $buffer) {
                if (Process::ERR === $type) {
                    // echo $buffer;
                } else {
                    //echo $buffer;
                }
            });

            $output->writeln( '<fg=green> local</> https://' . $_ENV['SUPERDOCK_LOCAL_DOMAIN'] );
            $output->writeln( '<fg=red> staging</> https://' . $_ENV['SUPERDOCK_STAGING_DOMAIN'] );
            $output->writeln( '<fg=red> preproduction</> https://' . $_ENV['SUPERDOCK_PREPRODUCTION_DOMAIN'] );
            $output->writeln( '<fg=red> production</> https://' . $_ENV['SUPERDOCK_PRODUCTION_DOMAIN'] );
            
            return Command::SUCCESS;

        } else {
            
            $output->writeln( '<fg=black;bg=red> ERROR </> Run <fg=cyan>up</> command fom superdock project folder' );
            return Command::FAILURE;

        }

    }
}
