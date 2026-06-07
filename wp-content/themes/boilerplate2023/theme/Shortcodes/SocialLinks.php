<?php

namespace Firefly\Shortcodes;

use Timber\Timber;

class SocialLinks
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
		add_shortcode('ff-social-links', function ($atts) {
			$context = Timber::get_context();
			$context['social_links'] = carbon_get_theme_option('ff_social_links');
			return Timber::compile('layout/social-links.html.twig', $context);
		});
	}
}
