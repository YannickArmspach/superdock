<?php
$buildRoot = dirname(__DIR__);
$phar = new Phar( $buildRoot . '/dist/superdock.phar', 0, 'superdock.phar' );
$include = '/^(?=(.*Command|.*Service|.*bin|.*inc|.*vendor))(.*)$/i';
$phar->buildFromDirectory($buildRoot, $include);
$phar->setStub("#!/usr/bin/env php\n" . $phar->createDefaultStub("bin/superdock.php"));
echo 'build success' . PHP_EOL;