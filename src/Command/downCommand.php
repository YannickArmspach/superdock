<?php 

namespace SuperDock\Command;

use icanhazstring\SymfonyConsoleSpinner\SpinnerProgress;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class downCommand extends Command
{
    
    protected static $defaultName = 'down';

    public function configure()
    {
        $this->setDescription('Stop the local project');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $process = new Process( 
            [ 
                'docker-compose', 
                '-f' . $_ENV['SUPERDOCK_USER_DIR'] . '/.superdock/docker/config.yml', 
                'down', 
                '--remove-orphans' 
            ], 
            null, null, null, null, null
        );
        $process->setTty(Process::isTtySupported());
        $process->start();
        $spinner = new SpinnerProgress( $output );
        $spinner->setMessage('stopping ' . $_ENV['SUPERDOCK_PROJECT_ID']);
        while ($process->isRunning()) {
            $spinner->advance();
            usleep(5000);
        }
        if ( $process->isSuccessful() ) {
            $spinner->finish();
        }
        $process = new Process( 
            [ 
                $_ENV['SUPERDOCK_USER_DIR'] . '/.superdock/sh/down.sh', 
                $_ENV['PASS'], 
            ], 
            null, null, null, null, null
        );
        $process->setTty(Process::isTtySupported());
        $process->run(function ($type, $buffer) {
            if (Process::ERR === $type) {
                // echo $buffer;
            } else {
                echo $buffer;
            }
        });
        return Command::SUCCESS;
    }
}
