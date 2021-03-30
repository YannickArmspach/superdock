<?php 

namespace SuperDock\Command;

use SuperDock\Service\coreService;
use SuperDock\Service\envService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class copyFromCommand extends Command
{
    
    protected static $defaultName = 'copy-from';

    public function configure()
    {
        $this->setDescription('scp for environements')
            ->addArgument('env', InputArgument::OPTIONAL, 'environement', 'local' )
            ->addArgument('path', InputArgument::OPTIONAL, 'path', '/' );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln( coreService::start() );

        switch ( $input->getArgument('env') ) {
            case 'staging':
                if ( 
                    isset( $_ENV['SUPERDOCK_STAGING_SSH_USER'] ) && $_ENV['SUPERDOCK_STAGING_SSH_USER'] && 
                    isset( $_ENV['SUPERDOCK_STAGING_SSH_IP'] ) && $_ENV['SUPERDOCK_STAGING_SSH_IP'] && 
                    isset( $_ENV['SUPERDOCK_STAGING_SSH_PORT'] ) && $_ENV['SUPERDOCK_STAGING_SSH_PORT']
                ) {
                    coreService::process([ 
                        'scp',
                        '-r',
                        $_ENV['SUPERDOCK_STAGING_SSH_USER'] . '@' . $_ENV['SUPERDOCK_STAGING_SSH_IP'] . ':' . $input->getArgument('path'),
                        $_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/copy/from/staging/',
                    ]);
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
                    coreService::process([ 
                        'ssh',
                        $_ENV['SUPERDOCK_PREPRODUCTION_SSH_USER'] . '@' . $_ENV['SUPERDOCK_PREPRODUCTION_SSH_IP'],
                        '-p' . $_ENV['SUPERDOCK_PREPRODUCTION_SSH_PORT'],
                        '-t',
                        'cd ' . $_ENV['SUPERDOCK_PREPRODUCTION_DIR'] . '; bash --login'
                    ]);
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
                    coreService::process([ 
                        'ssh',
                        $_ENV['SUPERDOCK_PRODUCTION_SSH_USER'] . '@' . $_ENV['SUPERDOCK_PRODUCTION_SSH_IP'],
                        '-p' . $_ENV['SUPERDOCK_PRODUCTION_SSH_PORT'],
                        '-t',
                        'cd ' . $_ENV['SUPERDOCK_PRODUCTION_DIR'] . '; bash --login'
                    ]);
                } else {
                    $output->writeln( '<fg=black;bg=red> ERROR </> Please configurate the <fg=cyan>.env.production</> file' );
                    return Command::FAILURE;
                }
            break;
        }
        return Command::SUCCESS;
    }
}
