<?php 

namespace SuperDock\Command;

use SuperDock\Service\coreService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class syncCommand extends Command
{
    
    protected static $defaultName = 'sync';

    public function configure()
    {
        $this->setDescription('Syncronyse environements')
        ->addOption('from', null, InputOption::VALUE_NONE, 'sync from')
        ->addOption('to', null, InputOption::VALUE_NONE, 'sync to')
        ->addArgument('env', InputArgument::REQUIRED, 'environement');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {

        if ( $input->getOption('to') == true ) {

            $process = new Process( 
                [ 
                    $_ENV['SUPERDOCK_CORE_DIR'] . '/vendor/deployer/deployer/bin/dep', 
                    '--file=' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/dep/sync-to.php', 
                    'sync',
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
            $output->writeln( coreService::infos( 'The ' . $input->getArgument('env') . ' environement has been successfully synchronized with local' ) );
        
        } else {
            
            $process = new Process( 
                [ 
                    $_ENV['SUPERDOCK_CORE_DIR'] . '/vendor/deployer/deployer/bin/dep', 
                    '--file=' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/dep/sync-from.php', 
                    'sync',
                    $input->getArgument('env'),
                    '-vvv'
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
            $output->writeln( coreService::infos( 'The local environement has been successfully synchronized with ' . $input->getArgument('env') ) );

        }
        return Command::SUCCESS;
    }
}
