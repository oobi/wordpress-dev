<?php

namespace Firefly\Shortcodes;

class BuzzDates
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
		add_shortcode('buzz_dates', function($atts) {
			$context = \Timber\Timber::get_context();

			$nid = get_post_type( get_the_ID() ) == 'newsletter' ? get_post()->ID : false;
			$newsletter = \Firefly\Buzz\Newsletter::get( $nid );

			$blocks = parse_blocks($newsletter->post_content);
			$dateBlocks = collect_blocks_by_name($blocks, 'buzz/date');
			$dates = [];

			foreach( $dateBlocks as $date ) {
				$heading = trim(strip_tags($date['innerBlocks'][0]['innerHTML'] ?? ''));

				$dates[] = [
					'date' => $heading,
					'link_url' => $date['attrs']['link_url'] ?? '',
					'link_text' => $date['attrs']['link_label'] ?? 'View',
					'description' => $date['attrs']['date_description'] ?? '',
					'external' => isset($date['attrs']['link_external']),
				];
			}

			$context['heading'] = isset($atts['heading']) ? $atts['heading'] : false;
			$context['show_dates'] = true;
			$context['data'] = $dates;

			return \Timber\Timber::compile('email/partials/dates-list-2col.html.twig', $context);
		});
	}
}
