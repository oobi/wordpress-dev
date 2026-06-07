<?php

namespace FireFly\Timber;

use Timber\Timber;
use Firefly\Timber\Context;

class Init
{

	function __construct()
	{
		$this->init();
	}

	private function init()
	{
		// Add Timber support
		$timber = new Timber();

		// Timber views directory name
		Timber::$dirname = ['views'];

		// Add Timber Context
		new Context();

		if (function_exists('\Djboris88\Timber\initialize_filters')) {
			\Djboris88\Timber\initialize_filters();
		  }
	}
}
