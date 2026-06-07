<?php

namespace Firefly\Carbon\Blocks;

use Carbon_Fields\Field;
use Carbon_Fields\Block;
use Carbon_Fields\Container;
use Timber\Timber;

class FeaturedPages
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
	public function register()
	{
		Block::make(__('featured-pages', $this->text_domain))
			->set_description('Featured Pages')
			->set_icon('heading')
			->set_mode('both')
			->add_fields([
				Field::make('association', 'ff_related_pages', 'Pages')
					->set_types([
						[
							'type'      => 'post',
							'post_type' => 'page',
						]
					])->set_min(1)->set_max(3),
			])

			->set_category('custom-blocks', __('Firefly Blocks', $this->text_domain))
			->set_render_callback(function ($fields, $attributes, $inner_blocks) {
				$vars['fields'] = $fields;
				$vars['attributes'] = $attributes;
				$vars['id'] = uniqid();
				$vars['pages'] = array_map(function ($page) {
					return Timber::get_post($page['id']);
				}, $fields['ff_related_pages']);
				Timber::render('/blocks/featured-pages.html.twig', $vars);
			});
	}
}
