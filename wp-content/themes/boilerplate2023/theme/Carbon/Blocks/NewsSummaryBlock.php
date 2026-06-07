<?php

namespace Firefly\Carbon\Blocks;

use Carbon_Fields\Field;
use Carbon_Fields\Block;
use Carbon_Fields\Container;
use Timber\Timber;

class NewsSummaryBlock
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
		Block::make(__('news-summary', $this->text_domain))
			->set_description('This block displays the latest news article.')
			->set_icon('list-view')
			->add_fields([
				Field::make('text', 'news_link', 'News Archive Link')
			])
			->set_category('custom-blocks', __('Firefly Blocks', $this->text_domain))
			->set_render_callback(function ($fields, $attributes, $inner_blocks) {
				//get the latest news post
				$args = [
					'post_type' => 'post',
					'posts_per_page' => 3,
					'orderby' => 'date',
					'order' => 'DESC',
				];
				$latest_news = Timber::get_posts($args);

				$vars['posts'] = $latest_news;
				$vars['fields'] = $fields;
				
				Timber::render('/blocks/news-summary.html.twig', $vars);
			});
	}
}
