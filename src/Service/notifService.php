<?php

namespace SuperDock\Service;

class notifService
{
	public function __construct( $string, $type = 'message', $refresh = false )
	{
		coreService::process([ 
			'osascript', 
			'-e',
			'display notification "' . $string . '" with title "superdock" subtitle "' . $type . '"'
		]);
		if ( $refresh ) {
			coreService::process([ 
				'osascript', 
				$_ENV['SUPERDOCK_CORE_DIR'] . '/inc/scpt/chrome_refresh.scpt'
			]);
		}
	}
}