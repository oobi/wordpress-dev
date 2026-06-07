<?php

namespace Firefly\Shortcodes;

use Timber\Timber;

class IFrame
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
		// add shortcode for social links
		add_shortcode('iframe', function ($atts) {
			$context = Timber::get_context();
			$context['attributes'] = $atts;
			return Timber::compile('shortcodes/iframe.html.twig', $context);
		});
	}
}
