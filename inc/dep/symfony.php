<?php

namespace Deployer;

set('shared_dirs', ['public/uploads', 'var/log', 'var/sessions']);
set('writable_dirs', ['public/uploads', 'var']);
set('migrations_config', '');

set('bin/console', function () {
	return parse('{{release_path}}/bin/console');
});

set('console_options', function () {
	return '--no-interaction';
});

set('rsync_src', './');
set('rsync_dest','{{release_path}}');

set('rsync',[
	'exclude'      => [
		'local',
		'android',
		'ios',
		'browser',
		'electron',
		'docs',
		'public/uploads',
		'node_modules',
		'var/cache',
		'.git',
		'.temp',
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
]);

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