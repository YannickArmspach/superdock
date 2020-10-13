<?php 

require_once dirname( __DIR__ ) . '/vendor/autoload.php';

use SuperDock\Command\coreCommand;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Console\Application; 
use SuperDock\Command\deployCommand;
use SuperDock\Command\downCommand;
use SuperDock\Command\newCommand;
use SuperDock\Command\redmineCommand;
use SuperDock\Command\syncCommand;
use SuperDock\Command\upCommand;
use SuperDock\Command\sshCommand;
use SuperDock\Command\openCommand;
use SuperDock\Command\killCommand;
use SuperDock\Command\initCommand;


$app = new Application('SUPERDOCK', 'v1.0.0');

$dotenv = new Dotenv();

$dotenv->populate([
    'PASS' => 'handy',
    'SUPERDOCK_CORE_DIR' => dirname(__DIR__),
    'SUPERDOCK_USER_DIR' => $_SERVER['HOME'],
    'SUPERDOCK_PROJECT_DIR' => $_SERVER['PWD']
]);

if ( file_exists( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.superdock' ) ) $dotenv->load( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.superdock');
if ( file_exists( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.local' ) ) $dotenv->load( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.local');
if ( file_exists( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.staging' ) ) $dotenv->load( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.staging');
if ( file_exists( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.preproduction' ) ) $dotenv->load( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.preproduction');
if ( file_exists( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.production' ) ) $dotenv->load( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.production');

$app->add(new coreCommand());

if ( file_exists( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.superdock' ) ) 
{
    $app->add(new sshCommand());
    $app->add(new upCommand());
    $app->add(new downCommand());
    $app->add(new deployCommand());
    $app->add(new syncCommand());
    $app->add(new redmineCommand());
}

$app->add(new initCommand());
$app->add(new openCommand());
$app->add(new newCommand());
$app->add(new killCommand());

echo PHP_EOL;
$app->run();