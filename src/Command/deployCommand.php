<?php 

namespace SuperDock\Command;

use SuperDock\Service\coreService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class deployCommand extends Command
{
    
    protected static $defaultName = 'deploy';

    public function configure()
    {
        $this->setDescription('Deploy code')
        ->addArgument('env', InputArgument::REQUIRED, 'environement');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    { 
        
        $process = new Process( 
            [ 
                $_ENV['SUPERDOCK_CORE_DIR'] . '/vendor/deployer/deployer/bin/dep', 
                '--file=' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/dep/deploy.php', 
                'deploy',
                // '-vvv',
                $input->getArgument('env')
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
        $output->writeln( coreService::infos( 'Code was successfully deploy to the ' . $input->getArgument('env') . ' environement' ) );
    
        return Command::SUCCESS;
    }
}
