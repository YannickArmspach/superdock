<?php 

namespace SuperDock\Command;

use icanhazstring\SymfonyConsoleSpinner\SpinnerProgress;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class killCommand extends Command
{
    
    protected static $defaultName = 'kill';

    public function configure()
    {
        $this->setDescription('Kill all running docker instance');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $process = new Process( 
            [ 
                $_ENV['SUPERDOCK_USER_DIR'] . '/.superdock/sh/kill.sh', 
                $_ENV['PASS'], 
            ], 
            null, null, null, null, null
        );
        $process->setTty(Process::isTtySupported());
        $process->start();
        $spinner = new SpinnerProgress( $output, 100);
        $spinner->setMessage('killing');
        while ($process->isRunning()) {
            $spinner->advance();
            usleep(5000);
        }
        if ( $process->isSuccessful() ) {
            $spinner->finish();
        }
        $output->writeln( '<fg=black;bg=green> RHAAAAHH </> You killed them all. Now, you can start fresh and zen ;)' );
        return Command::SUCCESS;
    }
}
