<?php

namespace Firefly\Carbon;

use Firefly\Setup\Config;
use Carbon_Fields\Carbon_Fields;

class Init
{
	function __construct()
	{
		add_action('after_setup_theme', [$this, 'after_setup_theme']);
		add_filter('carbon_fields_map_field_api_key', 'get_gmaps_api_key');
        add_action('widgets_init', [$this, 'load_widgets']);
	}

	public function after_setup_theme()
	{
		Carbon_Fields::boot();

		new Fields\ThemeOptions;
		// new Fields\HomePageFields;
	}

	public function get_gmaps_api_key($current_key)
	{
		return Config::get('theme')['gmaps_api_key'];
	}

	/*
	 * If you theme use Carbon Fields to construct custom Widgets,
	 * Register them in this function
	 * https://docs.carbonfields.net/#/containers/widgets
	 */
	public function load_widgets() {}
}
