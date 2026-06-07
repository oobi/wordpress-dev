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
		add_action('carbon_fields_register_fields', [$this, 'registerFields']);
    }
    /**
     * Register the theme options containers
     */
    public function registerFields()
    {
        // Default options page
        $basic_options_container = Container::make('theme_options', __('Theme Options', $this->text_domain))
            ->add_fields([
				Field::make_text('ff_gtm_id', __('Google Tag Manager ID')),
				Field::make_complex('ff_social_links', __('Social-Links'))->add_fields([
					Field::make_text('icon', __('Icon')),
					Field::make_text('link', __('Link')),
					Field::make_text('label', __('Label')),
				])
			]);
    }
}
