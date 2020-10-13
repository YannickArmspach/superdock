<?php
namespace Deployer;

require 'recipe/common.php';
require 'hosts.php';
require 'settings.php';

task('mercure:start', function () {
	run('chown -R www-data:www-data {{deploy_path}}/current/mercure/mercure');
    run('{{deploy_path}}/current/mercure/mercure --jwt-key="mykeytest" --addr="localhost:3001" --debug --allow-anonymous=1 --cors-allowed-origins="*" --publish-allowed-origins="*"');
});

