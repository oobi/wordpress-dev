<?php

namespace Firefly\Shortcodes;

class NewsletterTitle
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
		add_shortcode('newsletter_title', function($atts) {
			$post = get_post();
			$post_id = 0;

			if (is_singular('article')) {
				$post_id = get_post_meta($post->ID, 'ff_parent_id', true);
			} else if (is_singular('newsletter')) {
				$post_id = get_the_ID();
			} else if( $post) {
				$post_id = $post->ID;
			}

			// return the post title
			return get_the_title($post_id);
		});
	}
}
