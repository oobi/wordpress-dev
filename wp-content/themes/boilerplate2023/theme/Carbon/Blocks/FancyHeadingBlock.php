<?php

namespace Firefly\Carbon\Blocks;

use Carbon_Fields\Field;
use Carbon_Fields\Block;
use Carbon_Fields\Container;
use Timber\Timber;

class FancyHeadingBlock
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
		Block::make(__('fancy-heading', $this->text_domain))
			->set_description('Fancy Heading')
			->set_icon('heading')
			->set_mode('both')
			->add_fields([
				Field::make('text', 'heading', 'Heading Text')->set_required(true)
			])

			->set_category('custom-blocks', __('Firefly Blocks', $this->text_domain))
			->set_render_callback(function ($fields, $attributes, $inner_blocks) {
				$vars['fields'] = $fields;
				$vars['attributes'] = $attributes;
				$vars['id'] = uniqid();
				Timber::render('/blocks/fancy-heading.html.twig', $vars);
			});
	}
}
