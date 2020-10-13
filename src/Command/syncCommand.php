<?php 

namespace SuperDock\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class syncCommand extends Command
{
    
    protected static $defaultName = 'sync';

    public function configure()
    {
        $this->setDescription('Syncronyse environements')
             ->addArgument('env', InputArgument::REQUIRED, 'environement');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln( 'sync env: ' . $input->getArgument('env') );
        return Command::SUCCESS;
    }
}
