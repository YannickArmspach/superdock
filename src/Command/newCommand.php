<?php 

namespace SuperDock\Command;

use SuperDock\Service\coreService;
use SuperDock\Service\redmineService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class newCommand extends Command
{
    
    protected static $defaultName = 'new';

    public function configure()
    {
        $this->setDescription('Create new project symfony|drupal|wordpress')
             ->addArgument('id', InputArgument::REQUIRED, 'The project ID');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln( coreService::start() );
        
        $project_dir = $_ENV['SUPERDOCK_PROJECT_DIR'] . '/' . $input->getArgument('id');

        if ( file_exists( $project_dir ) ) {
            $output->writeln( '<fg=black;bg=red> ERROR </> Project id <fg=red>' . $input->getArgument('id') . '</> already exist.' );
            return Command::FAILURE;
        }
        if ( isset( $_ENV['SUPERDOCK_PROJECT_ID'] ) ) {
            $output->writeln( '<fg=black;bg=red> ERROR </> You can\'t create a new project here. This directory is already a superdock project.' );
            return Command::FAILURE;
        }  
        
        mkdir( $project_dir, 0777, true);

        $helper = $this->getHelper('question');

        //name
        $questionName = new Question('<fg=green>Please enter your name (dev):</> ', 'dev' );
        $SUPERDOCK_DEV_NAME = $helper->ask($input, $output, $questionName);

        //email
        $questionName = new Question('<fg=green>Please enter your dev email (dev@' . $input->getArgument('id') . '.local):</> ', 'dev@' . $input->getArgument('id') . '.local' );
        $SUPERDOCK_DEV_EMAIL = $helper->ask($input, $output, $questionName);

        //type
        $question = new ChoiceQuestion( '<fg=green>Please select project type (symfony):</>', ['symfony', 'drupal', 'wordpress', 'default'], 0 );
        $question->setErrorMessage('Type %s is invalid.');
        $SUPERDOCK_PROJECT_TYPE = $helper->ask($input, $output, $question);
        $output->writeln( 'You have just selected: ' . $SUPERDOCK_PROJECT_TYPE);

        //redmine
        $question = new ChoiceQuestion('<fg=green>Please select redmine project:</>', redmineService::getProjectList(), 0 );
        $question->setErrorMessage('Type %s is invalid.');
        $SUPERDOCK_REDMINE_ID = $helper->ask($input, $output, $question);
        $SUPERDOCK_REDMINE_ID = explode('id: #', $SUPERDOCK_REDMINE_ID )[1]; 
        $output->writeln( 'You have just selected: ' . $SUPERDOCK_REDMINE_ID);

        //SUPERDOCK_PHP
        $question = new ChoiceQuestion( '<fg=green>Please select php version (7.3):</>', ['7.1', '7.2', '7.3', '7.4'], '7.3' );
        $question->setErrorMessage('Version %s is invalid.');
        $SUPERDOCK_PHP = $helper->ask($input, $output, $question);
        $output->writeln( 'You have just selected: ' . $SUPERDOCK_PHP);

        //SUPERDOCK_XDEBUG
        $question = new ChoiceQuestion( '<fg=green>Enable XDebug (true):</>', ['true', 'false'] );
        $question->setErrorMessage('Version %s is invalid.');
        $SUPERDOCK_XDEBUG = $helper->ask($input, $output, $question);
        $output->writeln( 'You have just selected: ' . $SUPERDOCK_XDEBUG);

        //SUPERDOCK_DATABASE
        $question = new ChoiceQuestion( '<fg=green>Select database engine (mysql:8):</>', ['mysql:7', 'mariadb:10.2'] );
        $question->setErrorMessage('Database engine %s is invalid.');
        $SUPERDOCK_DATABASE = $helper->ask($input, $output, $question);
        $output->writeln( 'You have just selected: ' . $SUPERDOCK_DATABASE);

        //SUPERDOCK_COMPOSER
        $question = new ConfirmationQuestion('Enable Composer', true);
        $SUPERDOCK_COMPOSER = $helper->ask($input, $output, $question);

        //set values
        $ENV = [
            '.superdock' => [
                'SUPERDOCK_VERSION' => '1.0.0',
                'SUPERDOCK_DEV_NAME' => $SUPERDOCK_DEV_NAME,
                'SUPERDOCK_DEV_EMAIL' => $SUPERDOCK_DEV_EMAIL,
                'SUPERDOCK_PROJECT_ID' => $input->getArgument('id'),
                'SUPERDOCK_PROJECT_TYPE' => $SUPERDOCK_PROJECT_TYPE,
                'SUPERDOCK_REDMINE_ID' => $SUPERDOCK_REDMINE_ID,
                'SUPERDOCK_PHP' => $SUPERDOCK_PHP,
                'SUPERDOCK_XDEBUG' => $SUPERDOCK_XDEBUG,
                'SUPERDOCK_DATABASE' => $SUPERDOCK_DATABASE,
                'SUPERDOCK_COMPOSER' => $SUPERDOCK_COMPOSER,
                'SUPERDOCK_NODE' => true,
                'SUPERDOCK_V8JS' => true,
                'SUPERDOCK_ELASTICSEARCH' => true,
                'SUPERDOCK_REDIS' => true,

            ],
            '.env.local' => [
                'SUPERDOCK_LOCAL_DOMAIN' => $input->getArgument('id') . '.local',
                'SUPERDOCK_LOCAL_DIR' => '/var/www/html/public',
                'SUPERDOCK_LOCAL_UPLOAD' => '',
                'SUPERDOCK_LOCAL_SSH_USER' => 'root',
                'SUPERDOCK_LOCAL_SSH_IP' => 'localhost',
                'SUPERDOCK_LOCAL_SSH_PORT' => '22',
                'SUPERDOCK_LOCAL_DB_NAME' => $input->getArgument('id'),
                'SUPERDOCK_LOCAL_DB_USER' => 'admin',
                'SUPERDOCK_LOCAL_DB_PASS' => 'admin',
                'SUPERDOCK_LOCAL_DB_HOST' => 'superdock_database',
                'SUPERDOCK_LOCAL_BRANCH' => 'origin/develop',
            ],
            '.env.staging' => [
                'SUPERDOCK_STAGING_DOMAIN' => '',
                'SUPERDOCK_STAGING_DIR' => '',
                'SUPERDOCK_STAGING_UPLOAD' => '',
                'SUPERDOCK_STAGING_SSH_USER' => '',
                'SUPERDOCK_STAGING_SSH_IP' => '',
                'SUPERDOCK_STAGING_SSH_PORT' => '',
                'SUPERDOCK_STAGING_DB_NAME' => '',
                'SUPERDOCK_STAGING_DB_USER' => '',
                'SUPERDOCK_STAGING_DB_PASS' => '',
                'SUPERDOCK_STAGING_DB_HOST' => '',
                'SUPERDOCK_STAGING_BRANCH' => '',
            ],
            '.env.preproduction' => [
                'SUPERDOCK_PREPRODUCTION_DOMAIN' => '',
                'SUPERDOCK_PREPRODUCTION_DIR' => '',
                'SUPERDOCK_PREPRODUCTION_UPLOAD' => '',
                'SUPERDOCK_PREPRODUCTION_SSH_USER' => '',
                'SUPERDOCK_PREPRODUCTION_SSH_IP' => '',
                'SUPERDOCK_PREPRODUCTION_SSH_PORT' => '',
                'SUPERDOCK_PREPRODUCTION_DB_NAME' => '',
                'SUPERDOCK_PREPRODUCTION_DB_USER' => '',
                'SUPERDOCK_PREPRODUCTION_DB_PASS' => '',
                'SUPERDOCK_PREPRODUCTION_DB_HOST' => '',
                'SUPERDOCK_PREPRODUCTION_BRANCH' => '',
            ],
            '.env.production' => [
                'SUPERDOCK_PRODUCTION_DOMAIN' => '',
                'SUPERDOCK_PRODUCTION_DIR' => '',
                'SUPERDOCK_PRODUCTION_UPLOAD' => '',
                'SUPERDOCK_PRODUCTION_SSH_USER' => '',
                'SUPERDOCK_PRODUCTION_SSH_IP' => '',
                'SUPERDOCK_PRODUCTION_SSH_PORT' => '',
                'SUPERDOCK_PRODUCTION_DB_NAME' => '',
                'SUPERDOCK_PRODUCTION_DB_USER' => '',
                'SUPERDOCK_PRODUCTION_DB_PASS' => '',
                'SUPERDOCK_PRODUCTION_DB_HOST' => '',
                'SUPERDOCK_PRODUCTION_BRANCH' => '',
            ],
        ];

        //build env
        foreach( $ENV as $filename => $data ) {
            $data_format = "";
            foreach( $data as $data_id => $data_value ) {
                $data_format .= $data_id . '=' . $data_value . PHP_EOL;
            }
            file_put_contents( $project_dir . '/' . $filename, $data_format );
        }

        //success
        $output->writeln( '<fg=black;bg=green> SUCCESS </> Project created. Go to folder with <fg=green> cd ' . $input->getArgument('id') . '</> and start with <fg=green>sd up</>.' );
        return Command::SUCCESS;

    }

}
