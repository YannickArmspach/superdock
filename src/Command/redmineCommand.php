<?php 

namespace SuperDock\Command;

use SuperDock\Service\coreService;
use SuperDock\Service\redmineService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class redmineCommand extends Command
{
    
    protected static $defaultName = 'redmine';
    
    public function configure()
    {
        $this->setDescription('Get redmine informations')
             ->addArgument('projects', InputArgument::REQUIRED, 'list all redmine projects');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln( coreService::start() );
        
        $output->writeln( 'redmine:' . $input->getArgument('projects') );

        dump( redmineService::getProjectList() );

        return Command::SUCCESS;
    }
}