<?php 

namespace SuperDock\Command;

use SuperDock\Service\coreService;
use SuperDock\Service\redmineService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class initCommand extends Command
{
    
    protected static $defaultName = 'init';

    public function configure()
    {
        $this->setDescription('Create new project from current directory project');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln( coreService::start() );
        
        $project_dir = $_ENV['SUPERDOCK_PROJECT_DIR'];
        $SUPERDOCK_PROJECT_ID = basename( $project_dir );
 
        $helper = $this->getHelper('question');

        $SUPERDOCK_DEV_NAME = 'dev';
        $SUPERDOCK_DEV_EMAIL = 'dev@' . $SUPERDOCK_PROJECT_ID . '.local';
        $SUPERDOCK_REDMINE_ID = 0;
        $SUPERDOCK_PROJECT_TYPE = 'symfony';
        $SUPERDOCK_PHP = '7.3';
        $SUPERDOCK_XDEBUG = true;
        $SUPERDOCK_DATABASE = 'mysql:8';
        $SUPERDOCK_COMPOSER = true;
        $SUPERDOCK_NODE =true;
        $SUPERDOCK_V8JS = true;
        $SUPERDOCK_ELASTICSEARCH = true;
        $SUPERDOCK_REDIS = true;

        if ( isset( $_ENV['SUPERDOCK_PROJECT_ID'] ) ) {
            $output->writeln( '<fg=black;bg=red> WARNING </> You try to initialize an existing project.' );
            $question = new ConfirmationQuestion('Continue with this action ? (yes|no) ', false);
            if ( ! $helper->ask($input, $output, $question) ) return Command::FAILURE;
            if ( $_ENV['SUPERDOCK_DEV_NAME'] ) $SUPERDOCK_DEV_NAME = $_ENV['SUPERDOCK_DEV_NAME'];
            if ( $_ENV['SUPERDOCK_DEV_EMAIL'] ) $SUPERDOCK_DEV_EMAIL = $_ENV['SUPERDOCK_DEV_EMAIL'];
            if ( $_ENV['SUPERDOCK_REDMINE_ID'] ) $SUPERDOCK_REDMINE_ID = $_ENV['SUPERDOCK_REDMINE_ID'];
            if ( $_ENV['SUPERDOCK_PROJECT_TYPE'] ) $SUPERDOCK_PROJECT_TYPE = $_ENV['SUPERDOCK_PROJECT_TYPE'];
        }

        //id
        $questionId = new Question('<fg=green>Please enter your project ID (' . $SUPERDOCK_PROJECT_ID . '):</> ', $SUPERDOCK_PROJECT_ID );
        $SUPERDOCK_PROJECT_ID = $helper->ask($input, $output, $questionId);

        //name
        $questionName = new Question('<fg=green>Please enter your name (' . $SUPERDOCK_DEV_NAME . '):</> ', $SUPERDOCK_DEV_NAME );
        $SUPERDOCK_DEV_NAME = $helper->ask($input, $output, $questionName);

        //email
        $questionName = new Question('<fg=green>Please enter your dev email (' . $SUPERDOCK_DEV_EMAIL . '):</> ', $SUPERDOCK_DEV_EMAIL );
        $SUPERDOCK_DEV_EMAIL = $helper->ask($input, $output, $questionName);

        //type
        $question = new ChoiceQuestion( '<fg=green>Please select project type (' . $SUPERDOCK_PROJECT_TYPE . '):</>', ['symfony', 'drupal', 'wordpress', 'default'], $SUPERDOCK_PROJECT_TYPE );
        $question->setErrorMessage('Type %s is invalid.');
        $SUPERDOCK_PROJECT_TYPE = $helper->ask($input, $output, $question);
        $output->writeln( 'You have just selected: ' . $SUPERDOCK_PROJECT_TYPE);

        //redmine
        $question = new ChoiceQuestion('<fg=green>Please select redmine project (0):</>', redmineService::getProjectList(), 0 );
        $question->setErrorMessage('Type %s is invalid.');
        $SUPERDOCK_REDMINE_ID = $helper->ask($input, $output, $question);
        $SUPERDOCK_REDMINE_ID = explode('id: #', $SUPERDOCK_REDMINE_ID )[1]; 
        $output->writeln( 'You have just selected: ' . $SUPERDOCK_REDMINE_ID);

        //SUPERDOCK_PHP
        $question = new ChoiceQuestion( '<fg=green>Please select php version (7.3):</>', ['7.1', '7.2', '7.3', '7.4'], $SUPERDOCK_PHP );
        $question->setErrorMessage('Version %s is invalid.');
        $SUPERDOCK_PHP = $helper->ask($input, $output, $question);
        $output->writeln( 'You have just selected: ' . $SUPERDOCK_PHP);

        //SUPERDOCK_XDEBUG
        $question = new ChoiceQuestion( '<fg=green>Enable XDebug (true):</>', ['true', 'false'], $SUPERDOCK_XDEBUG );
        $question->setErrorMessage('Version %s is invalid.');
        $SUPERDOCK_XDEBUG = $helper->ask($input, $output, $question);
        $output->writeln( 'You have just selected: ' . $SUPERDOCK_XDEBUG);

        //SUPERDOCK_DATABASE
        $question = new ChoiceQuestion( '<fg=green>Select database engine (mysql:8):</>', ['mysql:8', 'mysql:7', 'MariaDb:8'], $SUPERDOCK_DATABASE );
        $question->setErrorMessage('Database engine %s is invalid.');
        $SUPERDOCK_DATABASE = $helper->ask($input, $output, $question);
        $output->writeln( 'You have just selected: ' . $SUPERDOCK_DATABASE);

        //SUPERDOCK_COMPOSER
        $question = new ConfirmationQuestion('Enable Composer (y|n)', $SUPERDOCK_COMPOSER);
        $SUPERDOCK_COMPOSER = $helper->ask($input, $output, $question);
        if( $SUPERDOCK_COMPOSER ) {
            $SUPERDOCK_COMPOSER = 'true';
        } else {
            $SUPERDOCK_COMPOSER = 'false';
        }

        //SUPERDOCK_NODE
        $question = new ConfirmationQuestion('Enable Node (y|n)', $SUPERDOCK_NODE);
        $SUPERDOCK_NODE = $helper->ask($input, $output, $question);
        if( $SUPERDOCK_NODE ) {
            $SUPERDOCK_NODE = 'true';
        } else {
            $SUPERDOCK_NODE = 'false';
        }

        //SUPERDOCK_V8JS
        $question = new ConfirmationQuestion('Enable V8js (y|n)', $SUPERDOCK_V8JS);
        $SUPERDOCK_V8JS = $helper->ask($input, $output, $question);
        if( $SUPERDOCK_V8JS ) {
            $SUPERDOCK_V8JS = 'true';
        } else {
            $SUPERDOCK_V8JS = 'false';
        }

        //SUPERDOCK_ELASTICSEARCH
        $question = new ConfirmationQuestion('Enable Elasticsearch (y|n)', $SUPERDOCK_ELASTICSEARCH);
        $SUPERDOCK_ELASTICSEARCH = $helper->ask($input, $output, $question);
        if( $SUPERDOCK_ELASTICSEARCH ) {
            $SUPERDOCK_ELASTICSEARCH = 'true';
        } else {
            $SUPERDOCK_ELASTICSEARCH = 'false';
        }

        //SUPERDOCK_REDIS
        $question = new ConfirmationQuestion('Enable redis (y|n)', $SUPERDOCK_REDIS);
        $SUPERDOCK_REDIS = $helper->ask($input, $output, $question);
        if( $SUPERDOCK_REDIS ) {
            $SUPERDOCK_REDIS = 'true';
        } else {
            $SUPERDOCK_REDIS = 'false';
        }

        //set values
        $ENV = [
            '.superdock' => [
                'SUPERDOCK_VERSION' => '1.0.0',
                'SUPERDOCK_DEV_NAME' => $SUPERDOCK_DEV_NAME,
                'SUPERDOCK_DEV_EMAIL' => $SUPERDOCK_DEV_EMAIL,
                'SUPERDOCK_PROJECT_ID' => $SUPERDOCK_PROJECT_ID,
                'SUPERDOCK_PROJECT_TYPE' => $SUPERDOCK_PROJECT_TYPE,
                'SUPERDOCK_REDMINE_ID' => $SUPERDOCK_REDMINE_ID,
                'SUPERDOCK_PHP' => $SUPERDOCK_PHP,
                'SUPERDOCK_XDEBUG' => $SUPERDOCK_XDEBUG,
                'SUPERDOCK_DATABASE' => $SUPERDOCK_DATABASE,
                'SUPERDOCK_COMPOSER' => $SUPERDOCK_COMPOSER,
                'SUPERDOCK_NODE' => $SUPERDOCK_NODE,
                'SUPERDOCK_V8JS' => $SUPERDOCK_V8JS,
                'SUPERDOCK_ELASTICSEARCH' => $SUPERDOCK_ELASTICSEARCH,
                'SUPERDOCK_REDIS' => $SUPERDOCK_REDIS,

            ],
            '.env.local' => [
                'SUPERDOCK_LOCAL_DOMAIN' => $SUPERDOCK_PROJECT_ID . '.local',
                'SUPERDOCK_LOCAL_DIR' => '/var/www/html/public',
                'SUPERDOCK_LOCAL_UPLOAD' => '',
                'SUPERDOCK_LOCAL_SSH_USER' => 'root',
                'SUPERDOCK_LOCAL_SSH_IP' => 'localhost',
                'SUPERDOCK_LOCAL_SSH_PORT' => '22',
                'SUPERDOCK_LOCAL_DB_NAME' => 'db',
                'SUPERDOCK_LOCAL_DB_USER' => 'admin',
                'SUPERDOCK_LOCAL_DB_PASS' => 'admin',
                'SUPERDOCK_LOCAL_DB_HOST' => 'localhost',
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
        $output->writeln( '<fg=black;bg=green> SUCCESS </> Project initialized. Start with <fg=green>sd up</>.' );
        return Command::SUCCESS;

    }

}
