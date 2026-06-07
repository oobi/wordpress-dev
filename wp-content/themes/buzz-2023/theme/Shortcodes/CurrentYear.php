<?php

namespace Firefly\Shortcodes;

class CurrentYear
{
	protected $text_domain;

	public function __construct()
	{
		$this->text_domain = \wp_get_theme()->get('TextDomain');
		$this->register();
	}

	/**
	 * Register the theme options containers
	 */
	public function register()
	{
		add_shortcode('current_year', function($atts) {
			return date('Y');
		});
	}
}
