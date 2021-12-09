<?php

namespace Deployer;

use SuperDock\Service\envService;

$envService = new envService();
$envService->init();
$envService->docker();

// dump( $_ENV );

ini_set( 'memory_limit', '-1' );

set('ssh_type', 'native');
set('use_relative_symlinks', false);
set('ssh_multiplexing', true);
set('allow_anonymous_stats', false);
set('writable_use_sudo', false);
set('writable_mode', 'chmod');
// set('http_user', 'www-data');
set('keep_releases', 5);