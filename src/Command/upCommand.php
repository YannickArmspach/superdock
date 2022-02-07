<?php

namespace SuperDock\Command;

use icanhazstring\SymfonyConsoleSpinner\SpinnerProgress;
use SuperDock\Service\coreService;
use SuperDock\Service\envService;
use SuperDock\Service\notifService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Process\Process;

class upCommand extends Command
{

  protected static $defaultName = 'up';

  public function configure()
  {
    $this->setDescription('Start the local project');
  }

  public function execute(InputInterface $input, OutputInterface $output)
  {
    $output->writeln(coreService::start());

    coreService::getPassword($input, $output);

    envService::docker();

    if (isset($_ENV['SUPERDOCK_PROJECT_ID']) && $_ENV['SUPERDOCK_PROJECT_ID']) {

      coreService::process([
        'git',
        'config',
        'core.fileMode',
        'false'
      ]);

      coreService::process([ 
        'docker-compose', 
        '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-compose.yml', 
        'down', 
        '--remove-orphans' 
      ]);

      if (isset($_ENV['SUPERDOCK_MUTAGEN']) && $_ENV['SUPERDOCK_MUTAGEN']) {
        coreService::process([ 
          'mutagen', 
          'sync',
          'terminate',
          'superdock',
        ]);
      }

      $output->writeln( PHP_EOL . '<fg=black;bg=green> CERTS </> generate local certificate' . PHP_EOL);

      //create certs
      if (!file_exists($_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/certificate/' . $_ENV['SUPERDOCK_LOCAL_DOMAIN'] . '/' . $_ENV['SUPERDOCK_LOCAL_DOMAIN'] . '.pem')) {
        coreService::process([
          $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/sh/cert.sh',
          $_ENV['PASS'],
          $_ENV['SUPERDOCK_LOCAL_DOMAIN'],
          $_ENV['SUPERDOCK_CORE_DIR'],
          $_ENV['SUPERDOCK_PROJECT_ID'],
          $_ENV['SUPERDOCK_PROJECT_DIR'],
        ]);
      }

      $output->writeln( PHP_EOL . '<fg=black;bg=green> ENV </> load project environements' . PHP_EOL);

      //load env and create hosts
      coreService::process([
        $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/sh/env.sh',
        $_ENV['PASS'],
        $_ENV['SUPERDOCK_LOCAL_DOMAIN'],
        $_ENV['SUPERDOCK_CORE_DIR'],
        $_ENV['SUPERDOCK_PROJECT_ID'],
      ]);

      $output->writeln( PHP_EOL . '<fg=black;bg=green> DOCKER </> start docker machine' . PHP_EOL);

      //start docker composer
      coreService::process([
        'docker-compose',
        '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-compose.yml',
        'up',
        '-d',
        '--build',
        '--remove-orphans',
        '--force-recreate',
        '--renew-anon-volumes',
      ]);

      //enable mutagen
      if (isset($_ENV['SUPERDOCK_MUTAGEN']) && $_ENV['SUPERDOCK_MUTAGEN']) {

        $output->writeln( PHP_EOL . '<fg=black;bg=green> MUTAGEN </> start mutagen sync' . PHP_EOL);

        coreService::process([
          'mutagen',
          '-c' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/mutagen.yml',
          'sync',
          'create',
          '--name',
          'superdock',
          $_ENV['SUPERDOCK_PROJECT_DIR'],
          'docker://root@superdock_webserver/var/www/html',
          '--ignore',
          $_ENV['SUPERDOCK_LOCAL_UPLOAD']
        ]);
        global $mutagen_error;
        $mutagen_error = false;
        function mutagen_connect_retry()
        {
          global $mutagen_error;
        
          $process = new Process(['mutagen', 'sync', 'list'], null, null, null, null, null);
          $process->start();
          $process->wait();

          $status_error = preg_grep("/\Last error\b/i", explode( PHP_EOL, $process->getOutput() ) );
          $error = $status_error && is_array($status_error) ? implode( ' ', $status_error ) : '';

          if ( ! empty( $error ) ) {

            echo "➤ Mutagen stopped... " . $error . PHP_EOL;
            $mutagen_error = true;
            return false;
          
          }

          if (strpos($process->getOutput(), 'Watching for changes') !== false) {
            echo "✔ Mutagen synchronized" . PHP_EOL;
            return false;
          } else {
            echo "➤ Mutagen synchronization in progress..." . PHP_EOL;
            return true;
          }
        }

        $active = true;
        while ($active) {
          $active = mutagen_connect_retry();
          sleep(5);
        }
        
        if ( $mutagen_error ){
          new notifService('Superdock error', 'message', false);
          $output->writeln( PHP_EOL . '<fg=black;bg=red> ERROR </> mutagen error' . PHP_EOL);
          return Command::FAILURE;
        }

      }

      if ( file_exists($_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/custom/build.sh') || file_exists($_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/custom/build.local.sh') ) {

        $output->writeln( PHP_EOL . '<fg=black;bg=green> BUILD </> run build.sh' . PHP_EOL);

        $buildFilename = file_exists($_ENV['SUPERDOCK_PROJECT_DIR'] . '/superdock/custom/build.local.sh') ? 'build.local.sh' : 'build.sh';
        
        coreService::process([
          'docker-compose',
          '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-compose.yml',
          'exec',
          'webserver',
          'sh',
          '-c',
          'chmod -R 777 superdock/custom/' . $buildFilename
        ]);

        coreService::process([
          'docker-compose',
          '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-compose.yml',
          'exec',
          'webserver',
          'sh',
          '-c',
          'superdock/custom/' . $buildFilename
        ]);
      }

      $output->writeln(coreService::infos());

      new notifService('Superdock is up', 'message', false);

      return Command::SUCCESS;
    } else {

      $output->writeln('<fg=black;bg=red> ERROR </> Run <fg=cyan>up</> command fom superdock project folder');
      return Command::FAILURE;
    }
  }
}
