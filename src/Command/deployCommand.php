<?php 

namespace SuperDock\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class deployCommand extends Command
{
    
    protected static $defaultName = 'deploy';

    public function configure()
    {
        $this->setDescription('Deploy you project in different environement')
             ->addArgument('env', InputArgument::REQUIRED, 'env');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln( 'deploy env: ' . $input->getArgument('env') );
        return Command::SUCCESS;
    }
}
