<?php

namespace Firefly\Carbon\Fields;

use Carbon_Fields\Field;
use Carbon_Fields\Container;

class ThemeOptions
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
		// basic optiopns
		$basic_options_container = Container::make('theme_options', __('Theme Options', $this->text_domain))
			->add_fields([
				Field::make( 'text', 'ff_test', __( 'Test' ) ),
				Field::make( 'complex', 'ff_bz_social_links', __( 'Social Links' ) )
					->add_fields( [
						Field::make( 'text', 'icon', __( 'Icon' ) )->set_width(33.33),
						Field::make( 'text', 'url', __( 'URL' ) )->set_width(33.33),
						Field::make( 'text', 'label', __( 'Label' ) )->set_width(33.33),
					])
			]);
	}
}
