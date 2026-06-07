<?php

namespace Firefly\Carbon\Blocks;

use Carbon_Fields\Field;
use Carbon_Fields\Block;
use Carbon_Fields\Container;
use Timber\Timber;

class EventsSummaryBlock
{
	protected $text_domain;

	public function __construct()
	{
		$this->text_domain = wp_get_theme()->get('TextDomain');
		add_action('carbon_fields_register_fields', [$this, 'register']);
	}

	/**
	 * Register Carbon Fields blocks
	 */
	public function register() {
		Block::make(__('events-summary', $this->text_domain))
			->set_description('This block displays the latest events.')
			->set_icon('list-view')
			->add_fields([
				Field::make('text', 'events_link', 'Events Archive Link')
			])
			->set_category('custom-blocks', __('Firefly Blocks', $this->text_domain))
			->set_render_callback(function ($fields, $attributes, $inner_blocks) {
				//get the latest news post
				$args = [
					'post_type' => 'event',
					'posts_per_page' => 4,
					'post_status' => 'publish',
					'meta_query' => [
						'relation' => 'OR',
						[
							'key' => '_ff_event_date',
							'value' => date('Ymd'),
							'compare' => '>=',
							'type' => 'DATE'
						],
						[
							'key' => '_ff_event_end',
							'value' => date('Ymd'),
							'compare' => '>=',
							'type' => 'DATE',
						],
					],
					'orderby' => 'meta_value',
					'order' => 'ASC'
				];
				$latest_events = Timber::get_posts($args);

				$vars['posts'] = $latest_events;
				$vars['fields'] = $fields;
				
				Timber::render('/blocks/events-summary.html.twig', $vars);
			});
	}
}
