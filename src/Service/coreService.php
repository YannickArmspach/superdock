<?php

namespace SuperDock\Service;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class coreService
{

	static function install()
	{
		
		$filesystem = new Filesystem();

		try {
		
			$filesystem->mirror('inc', $_ENV['SUPERDOCK_USER_DIR'] . '/.superdock' );
		
		} catch (IOExceptionInterface $exception) {

			echo "An error occurred while creating your directory at " . $exception->getPath();
			return false;

		}

		return true;
	}

	static function update()
	{
		return false;	
	}

	static function uninstall()
	{
		return false;	
	}

}