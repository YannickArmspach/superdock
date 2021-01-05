<?php

namespace Deployer;

use SuperDock\Service\coreService;
use Symfony\Component\Process\Process;

require 'recipe/common.php';
require 'hosts.php';
require 'settings.php';
require 'symfony.php';

desc('Migrate database');
task('database:migrate', function () {
	$options = '--allow-no-migration';
	if (get('migrations_config') !== '') {
		$options = sprintf('%s --configuration={{release_path}}/{{migrations_config}}', $options);
	}

	run(sprintf('{{bin/php}} {{bin/console}} doctrine:migrations:migrate %s {{console_options}}', $options));
});

desc('Clear cache');
task('deploy:cache:clear', function () {
	run('{{bin/php}} {{bin/console}} cache:clear {{console_options}} --no-warmup');
});

desc('Warm up cache');
task('deploy:cache:warmup', function () {
	run('{{bin/php}} {{bin/console}} cache:warmup {{console_options}}');
});

desc('Warmup remote Rsync target');
task('rsync:warmup', function() {
    $config = get('rsync');

    $source = "{{deploy_path}}/current";
    $destination = "{{deploy_path}}/release";

    if (test("[ -d $(echo $source) ]")) {
        run("rsync -{$config['flags']} {{rsync_options}}{{rsync_excludes}}{{rsync_includes}}{{rsync_filter}} $source/ $destination/");
    } else {
        writeln("<comment>No way to warmup rsync.</comment>");
    }
});

desc('deploy:build');
task('deploy:build', function () {
    coreService::process([ 
        'docker-compose', 
        '-f' . $_ENV['SUPERDOCK_CORE_DIR'] . '/inc/docker/docker-compose.yml', 
        'exec', 
        'webserver', 
        'sh', 
        '-c', 
        './node_modules/.bin/encore production --env=' . get('deploy_env')
    ]);
});

task('deploy:code', function() {
    $config = get('rsync');

    $src = get('rsync_src');
    while (is_callable($src)) {
        $src = $src();
    }

    if (!trim($src)) {
        throw new \RuntimeException('You need to specify a source path.');
    }

    $dst = get('rsync_dest');
    while (is_callable($dst)) {
        $dst = $dst();
    }

    if (!trim($dst)) {
        throw new \RuntimeException('You need to specify a destination path.');
    }

    $server = \Deployer\Task\Context::get()->getHost();
    if ($server instanceof \Deployer\Host\Localhost) {
        runLocally("rsync -{$config['flags']} {{rsync_options}}{{rsync_includes}}{{rsync_excludes}}{{rsync_filter}} '$src/' '$dst/'", $config);
        return;
    }

    $host = $server->getRealHostname();
    $port = $server->getPort() ? ' -p' . $server->getPort() : '';
    $sshArguments = $server->getSshArguments();
    $user = !$server->getUser() ? '' : $server->getUser() . '@';

    runLocally("rsync -{$config['flags']} --stats --progress -e 'ssh$port $sshArguments' {{rsync_options}}{{rsync_includes}}{{rsync_excludes}}{{rsync_filter}} '$src/' '$user$host:$dst/'", $config);
});

/* update_code end */

desc('Overwrite env');
task('deploy:env', function () {
	upload( $_ENV['SUPERDOCK_PROJECT_DIR'] . '/.env.{{deploy_env}}', '{{release_path}}/.env' );
});

desc('elastic:reindex');
task('deploy:elastic:reindex', function () {
	run('{{bin/php}} {{bin/console}} elastic:reindex -vvv {{console_options}}');
});

task('deploy:build', function () {
	run('cd {{release_path}} && chmod -R 777 ./superdock/custom/build.sh');
	run('cd {{release_path}} && ./superdock/custom/build.sh');
});

//Deploy
task('deploy', [
    'deploy:info',
    'deploy:unlock',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:build',
    'deploy:code',
	'deploy:env',
    'deploy:shared',
    'deploy:writable',
	'deploy:cache:clear',
	// 'database:migrate',
	'deploy:cache:warmup',
    'deploy:elastic:reindex',
    'deploy:build',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
]);

after('deploy', 'success');

after('deploy:failed', 'deploy:unlock');
