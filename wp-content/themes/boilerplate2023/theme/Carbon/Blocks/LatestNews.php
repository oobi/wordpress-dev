<?php

namespace Firefly\Carbon\Blocks;

use Carbon_Fields\Field;
use Carbon_Fields\Block;
use Carbon_Fields\Container;
use Timber\Timber;

class LatestNews
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
		Block::make(__('latest-news', $this->text_domain))
			->set_description('This block displays the latest news article.')
			->set_icon('list-view')
			->add_fields([
				
			])
			->set_category('custom-blocks', __('Firefly Blocks', $this->text_domain))
			->set_render_callback(function ($fields, $attributes, $inner_blocks) {
				//get the latest news post
				$args = [
					'post_type' => 'post',
					'posts_per_page' => 5,
					'orderby' => 'date',
					'order' => 'DESC',
				];
				$latest_news = Timber::get_posts($args);

				$vars['posts'] = $latest_news;
				// this displays the link to the news page on the block.
				$vars['show_all_news_link'] = true;
				
				Timber::render('/blocks/latest-news.html.twig', $vars);
			});
	}
}
