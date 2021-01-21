<?php 

namespace SuperDock\Command;

use SuperDock\Service\coreService;
use SuperDock\Service\envService;
use SuperDock\Service\notifService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Process\Process;

class syncFromCommand extends Command
{
    
    protected static $defaultName = 'sync-from';

    public function configure()
    {
        $this->setDescription('Replace local database and media with distant database and media')
        ->addArgument('env', InputArgument::REQUIRED, 'environement');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln( coreService::start() );

        envService::docker();
        
        $helper = $this->getHelper('question');

        $verbose = $input->getOption('verbose') ? '-vvv' : ''; 

        $question = new ConfirmationQuestion('Replace local database with ' . $input->getArgument('env') . ' database ? (yes|no) ', false);
        if ( ! $helper->ask($input, $output, $question) ) return Command::FAILURE;
        coreService::process([ 
            $_ENV['SUPERDOCK_CORE_DIR'] . '/vendor/deployer/deployer/bin/dep', 
            '--file=' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/dep/sync-from.php', 
            'sync-db',
            $input->getArgument('env'),
            $verbose,
        ]);

        $question = new ConfirmationQuestion('Replace local medias with ' . $input->getArgument('env') . ' medias ? (yes|no) ', false);
        if ( ! $helper->ask($input, $output, $question) ) return Command::FAILURE;
        coreService::process([ 
            $_ENV['SUPERDOCK_CORE_DIR'] . '/vendor/deployer/deployer/bin/dep', 
            '--file=' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/dep/sync-from.php', 
            'sync-media',
            $input->getArgument('env'),
            $verbose,
        ]);


        $output->writeln( coreService::infos( 'The local environement has been successfully synchronized with ' . $input->getArgument('env') ) );

        new notifService('Sync from is done', 'message', true);

        return Command::SUCCESS;
    }
}
