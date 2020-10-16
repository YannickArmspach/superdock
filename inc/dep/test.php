<?php 
namespace Deployer;

use SuperDock\Service\coreService;

coreService::env();

desc('task');
task('task', function () {
    writeln('task start' );
    dump( $_ENV );
    writeln('wait...');
    sleep(3);
    writeln('task end');
});
task('task2', function () {
    writeln('task2 start');
    writeln('wait...');
    sleep(3);
    writeln('task2 end');
});
task('deploy', [
    'task',
    'task2'
]);