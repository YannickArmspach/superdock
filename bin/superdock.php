<?php 

require_once dirname( __DIR__ ) . '/vendor/autoload.php';

use Symfony\Component\Console\Application; 
use SuperDock\Command\buildCommand;
use SuperDock\Command\clearCommand;
use SuperDock\Command\composerCommand;
use SuperDock\Command\copyFromCommand;
use SuperDock\Command\coreCommand;
use SuperDock\Command\deployCommand;
use SuperDock\Command\downCommand;
use SuperDock\Command\execCommand;
use SuperDock\Command\infoCommand;
use SuperDock\Command\newCommand;
use SuperDock\Command\redmineCommand;
use SuperDock\Command\dbCommand;
use SuperDock\Command\upCommand;
use SuperDock\Command\sshCommand;
use SuperDock\Command\killCommand;
use SuperDock\Command\initCommand;
use SuperDock\Command\logsCommand;
use SuperDock\Command\serverCommand;
use SuperDock\Command\syncFromCommand;
use SuperDock\Command\syncToCommand;
use SuperDock\Command\watchCommand;
use SuperDock\Service\coreService;
use SuperDock\Service\envService;

envService::init();
coreService::dir();

$app = new Application('SUPERDOCK', 'v1.0.0');

$app->add(new coreCommand());

if ( is_file( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.superdock' )  ) 
{
    $app->add(new execCommand());
    $app->add(new composerCommand());
    $app->add(new buildCommand());
    $app->add(new clearCommand());
    $app->add(new logsCommand());
    $app->add(new sshCommand());
    $app->add(new upCommand());
    $app->add(new watchCommand());
    $app->add(new downCommand());
    $app->add(new serverCommand());
    $app->add(new deployCommand());
    $app->add(new copyFromCommand());
    //TODO: $app->add(new copyToCommand()); 
    $app->add(new syncFromCommand());
    $app->add(new syncToCommand());
    $app->add(new dbCommand());
    $app->add(new redmineCommand());
}

$app->add(new infoCommand());
$app->add(new initCommand());
$app->add(new newCommand());
$app->add(new killCommand());

echo PHP_EOL;
$app->run();