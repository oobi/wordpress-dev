<?php

namespace Firefly\Shortcodes;

class BuzzCredit
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
		add_shortcode('buzz_credit', function($atts) {
			return <<<EOT
				<a href="https://www.thebuzz.net.au" class="buzz-credit">Powered by The Buzz</a>
			EOT;
		});
	}
}
