<?php

namespace SuperDock\Service;


class notifService
{

	public function __construct( $string, $type = 'message' )
	{
		//TODO: check if mac
		coreService::process([ 
			'osascript', 
			'-e',
			'display notification "' . $string . '" with title "superdock" subtitle "' . $type . '"'
		]);

		coreService::process([ 
			'osascript', 
			$_ENV['SUPERDOCK_CORE_DIR'] . '/inc/scpt/chrome_refresh.scpt'
		]);

	}

}