<?php 

namespace SuperDock\Command;

use icanhazstring\SymfonyConsoleSpinner\SpinnerProgress;
use SuperDock\Service\coreService;
use SuperDock\Service\envService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class coreCommand extends Command
{
    
    protected static $defaultName = 'core';

    public function configure()
    {
        $this->setDescription('self core install/update/uninstall')
        ->addArgument('action', InputArgument::REQUIRED, 'install|update|uninstall');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        switch ( $input->getArgument('action') ){
            case 'install':
                // coreService::install();
            break;
            case 'update':
                // coreService::update();
            break;
            case 'uninstall':
                // coreService::uninstall();
            break;
        }
        return Command::SUCCESS;
    }
}
