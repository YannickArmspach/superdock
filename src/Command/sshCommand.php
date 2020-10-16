<?php 

namespace SuperDock\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class sshCommand extends Command
{
    
    protected static $defaultName = 'ssh';

    public function configure()
    {
        $this->setDescription('Start the local project')
             ->addArgument('env', InputArgument::OPTIONAL, 'environement', 'local' );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        switch ( $input->getArgument('env') ) {
            case 'local':
                $process = new Process( 
                    [ 
                        'docker-compose', 
                        '-f' . $_ENV['SUPERDOCK_USER_DIR'] . '/.superdock/docker/config.yml', 
                        'exec',
                        'webserver',
                        'bash'
                    ]
                );
            break;
            case 'staging':
                if ( 
                    isset( $_ENV['SUPERDOCK_STAGING_SSH_USER'] ) && $_ENV['SUPERDOCK_STAGING_SSH_USER'] && 
                    isset( $_ENV['SUPERDOCK_STAGING_SSH_IP'] ) && $_ENV['SUPERDOCK_STAGING_SSH_IP'] && 
                    isset( $_ENV['SUPERDOCK_STAGING_SSH_PORT'] ) && $_ENV['SUPERDOCK_STAGING_SSH_PORT']
                ) {
                    $process = new Process( 
                        [ 
                            'ssh',
                            $_ENV['SUPERDOCK_STAGING_SSH_USER'] . '@' . $_ENV['SUPERDOCK_STAGING_SSH_IP'] . ':' . $_ENV['SUPERDOCK_STAGING_SSH_PORT']
                        ]
                    );
                } else {
                    $output->writeln( '<fg=black;bg=red> ERROR </> Please configurate the <fg=cyan>.env.staging</> file' );
                    return Command::FAILURE;
                }
            break;
            case 'preproduction':
                if ( 
                    isset( $_ENV['SUPERDOCK_PREPRODUCTION_SSH_USER'] ) && $_ENV['SUPERDOCK_PREPRODUCTION_SSH_USER'] && 
                    isset( $_ENV['SUPERDOCK_PREPRODUCTION_SSH_IP'] ) && $_ENV['SUPERDOCK_PREPRODUCTION_SSH_IP'] && 
                    isset( $_ENV['SUPERDOCK_PREPRODUCTION_SSH_PORT'] ) && $_ENV['SUPERDOCK_PREPRODUCTION_SSH_PORT']
                ) {
                    $process = new Process( 
                        [ 
                            'ssh',
                            $_ENV['SUPERDOCK_PREPRODUCTION_SSH_USER'] . '@' . $_ENV['SUPERDOCK_PREPRODUCTION_SSH_IP'] . ':' . $_ENV['SUPERDOCK_PREPRODUCTION_SSH_PORT']
                        ]
                    );
                } else {
                    $output->writeln( '<fg=black;bg=red> ERROR </> Please configurate the <fg=cyan>.env.preproduction</> file' );
                    return Command::FAILURE;
                }
            break;
            case 'production':
                if ( 
                    isset( $_ENV['SUPERDOCK_PRODUCTION_SSH_USER'] ) && $_ENV['SUPERDOCK_PRODUCTION_SSH_USER'] && 
                    isset( $_ENV['SUPERDOCK_PRODUCTION_SSH_IP'] ) && $_ENV['SUPERDOCK_PRODUCTION_SSH_IP'] && 
                    isset( $_ENV['SUPERDOCK_PRODUCTION_SSH_PORT'] ) && $_ENV['SUPERDOCK_PRODUCTION_SSH_PORT']
                ) {
                    $process = new Process( 
                        [ 
                            'ssh',
                            $_ENV['SUPERDOCK_PRODUCTION_SSH_USER'] . '@' . $_ENV['SUPERDOCK_PRODUCTION_SSH_IP']. ':' . $_ENV['SUPERDOCK_PRODUCTION_SSH_PORT']
                        ]
                    );
                } else {
                    $output->writeln( '<fg=black;bg=red> ERROR </> Please configurate the <fg=cyan>.env.production</> file' );
                    return Command::FAILURE;
                }
            break;
        }
        $process->setTty(Process::isTtySupported());
        $process->setTimeout(600);
        $process->run(function ($type, $buffer) {
            if (Process::ERR === $type) {
                echo $buffer;
            } else {
                echo $buffer;
            }
        });
        return Command::SUCCESS;
    }
}
