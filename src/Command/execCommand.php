<?php 

namespace SuperDock\Command;

use SuperDock\Service\coreService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class execCommand extends Command
{
    
    protected static $defaultName = 'exec';

    public function configure()
    {
        $this->setDescription('Execute command in local container')
        ->addArgument('execute_command', InputArgument::REQUIRED, 'command');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    { 
        $process = new Process( 
            [ 
                'docker-compose', 
                '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/config.yml', 
                'exec', 
                'webserver', 
                'sh', 
                '-c', 
                $input->getArgument('execute_command')
            ], 
            null, null, null, null, null
        );
        $process->setTty(Process::isTtySupported());
        $process->run(function ($type, $buffer) {
            if (Process::ERR === $type) {
                echo $buffer;
            } else {
                echo $buffer;
            }
        });
        $output->writeln( coreService::infos( 'Your command ' . $command . ' was executed in local container' ) );
    
        return Command::SUCCESS;
    }
}
