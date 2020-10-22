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
        ->addArgument('env', InputArgument::REQUIRED, 'environement')
        ->addOption('debug', null, InputOption::VALUE_NONE, 'verbose');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    { 
        $cmd = [
            $_ENV['SUPERDOCK_CORE_DIR'] . '/vendor/deployer/deployer/bin/dep', 
            '--file=' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/dep/deploy.' . $_ENV['SUPERDOCK_PROJECT_TYPE'] . '.php', 
            'deploy',
            $input->getArgument('env')
        ];
        if ( $input->getOption('debug') ) array_push( $cmd, '-vvv' ); 
        $process = new Process( $cmd, null, null, null, null, null );
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
