<?php

namespace Firefly\Shortcodes;

class FontAwesome
{
	protected $text_domain;

	public function __construct()
	{
		$this->text_domain = wp_get_theme()->get('TextDomain');
		add_action('init', [$this, 'register']);
	}

	/**
	 * shortcode to insert FontAwesome icon
	 */
	public function register()
	{
		add_shortcode('fa', function ($atts) {
			$atts = shortcode_atts([
				'prefix' => 'fa',
				'icon' => '',
				'class' => ''
			], $atts);

			return sprintf('<i class="ff-icon %s fa-%s %s"></i>', $atts['prefix'], $atts['icon'], $atts['class']);
		});
	}
}
