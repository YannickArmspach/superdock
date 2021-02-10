<?php

namespace Deployer;

use Symfony\Component\Process\Process;
use SuperDock\Service\envService;

$envService = new envService();
$envService->init();
$envService->docker();

require 'recipe/common.php';
require 'hosts.php';
require 'settings.php';

set('shared_dirs', function () {
	$SUPERDOCK = get('SUPERDOCK');
	return [ ltrim( $SUPERDOCK['DIST_UPLOAD'], '/') ];
});
set('writable_dirs', function () {
	$SUPERDOCK = get('SUPERDOCK');
	return [ ltrim( $SUPERDOCK['DIST_UPLOAD'], '/') ];
});

set('rsync_src', './');
set('rsync_dest','{{release_path}}');

set('rsync', function () {
	$SUPERDOCK = get('SUPERDOCK');
	return [
		'exclude' => [
			$SUPERDOCK['SOURCE_UPLOAD'],
			'.git',
			'.temp',
			'node_modules',
			'.env',
			'.env.local',
			'.env.staging',
			'.env.preproduction',
			'.env.production',
		],
		'exclude-file' => false,
		'include'      => [],
		'include-file' => false,
		'filter'       => [],
		'filter-file'  => false,
		'filter-perdir'=> false,
		'flags'        => 'rz',
		'options'      => ['delete'],
		'timeout'      => 3600,
	];
});

set('rsync_excludes', function () {
	$config = get('rsync');
	$excludes = $config['exclude'];
	$excludeFile = $config['exclude-file'];
	$excludesRsync = '';
	foreach ($excludes as $exclude) {
		$excludesRsync.=' --exclude=' . escapeshellarg($exclude);
	}
	if (!empty($excludeFile) && file_exists($excludeFile) && is_file($excludeFile) && is_readable($excludeFile)) {
		$excludesRsync .= ' --exclude-from=' . escapeshellarg($excludeFile);
	}

	return $excludesRsync;
});

set('rsync_includes', function () {
	$config = get('rsync');
	$includes = $config['include'];
	$includeFile = $config['include-file'];
	$includesRsync = '';
	foreach ($includes as $include) {
		$includesRsync.=' --include=' . escapeshellarg($include);
	}
	if (!empty($includeFile) && file_exists($includeFile) && is_file($includeFile) && is_readable($includeFile)) {
		$includesRsync .= ' --include-from=' . escapeshellarg($includeFile);
	}

	return $includesRsync;
});

set('rsync_filter', function () {
	$config = get('rsync');
	$filters = $config['filter'];
	$filterFile = $config['filter-file'];
	$filterPerDir = $config['filter-perdir'];
	$filtersRsync = '';
	foreach ($filters as $filter) {
		$filtersRsync.=" --filter='$filter'";
	}
	if (!empty($filterFile)) {
		$filtersRsync .= " --filter='merge $filterFile'";
	}
	if (!empty($filterPerDir)) {
		$filtersRsync .= " --filter='dir-merge $filterPerDir'";
	}
	return $filtersRsync;
});

set('rsync_options', function () {
	$config = get('rsync');
	$options = $config['options'];
	$optionsRsync = [];
	foreach ($options as $option) {
		$optionsRsync[] = "--$option";
	}
	return implode(' ', $optionsRsync);
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

desc('Overwrite env');
task('deploy:env', function () {
	$SUPERDOCK = get('SUPERDOCK');
	upload( $SUPERDOCK['SOURCE_DIR'] . '/.env.{{deploy_env}}', '{{release_path}}/.env' );
});

task('deploy:build', function () {
	if ( file_exists( './superdock/custom/build.sh' ) || file_exists( './superdock/custom/build.' . get('deploy_env') . '.sh' ) ) {
		$buildFilename = file_exists( './superdock/custom/build.' . get('deploy_env') . '.sh' ) ? 'build.' . get('deploy_env') . '.sh' : 'build.sh';
		run('cd {{release_path}} && chmod -R 777 ./superdock/custom/' . $buildFilename);
		run('cd {{release_path}} && ./superdock/custom/' . $buildFilename);
	}
});

//Deploy
task('deploy', [
	'deploy:info',
	'deploy:unlock',
	'deploy:prepare',
	'deploy:lock',
	'deploy:release',
	'deploy:code',
	'deploy:env',
	'deploy:shared',
	'deploy:writable',
	'deploy:build',
	'deploy:symlink',
	'deploy:unlock',
	'cleanup',
]);

after('deploy', 'success');

after('deploy:failed', 'deploy:unlock');
