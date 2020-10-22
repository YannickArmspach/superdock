<?php 

namespace SuperDock\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class openCommand extends Command
{
    
    //cd command is not possible with symfony command
    protected static $defaultName = 'open';

    public function configure()
    {
        $this->setDescription('Open existing project');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {

        if ( is_dir( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/sensiotest' ) ) {

            $process = new Process( 
                [ 
                    $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/sh/cd.sh', 
                    $_ENV['SUPERDOCK_PROJECT_DIR'] . '/sensiotest', 
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

        } else {
            
            $output->writeln( '<fg=black;bg=red> ERROR </> Run <fg=cyan>up</> command fom superdock project folder' );
            return Command::FAILURE;

        }

    }
}
