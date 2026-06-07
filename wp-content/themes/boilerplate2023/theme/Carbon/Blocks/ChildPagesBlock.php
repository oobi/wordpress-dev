<?php

namespace Firefly\Carbon\Blocks;

use Carbon_Fields\Field;
use Carbon_Fields\Block;
use Carbon_Fields\Container;
use Timber\Timber;

class ChildPagesBlock
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
		Block::make('Child Pages', __('Child Pages', $this->text_domain))
			->add_fields([
				Field::make('html', 'description')
					->set_html('<p>' . __('This block will display a grid of tiles that link to this pages child pages.', $this->text_domain) . '</p>'),
			])
			->set_description('Displays a grid of tiles that link to this pages child pages.')
			->set_icon('columns')
			->set_mode('both')
			->set_category('custom-blocks', __('Firefly Blocks', $this->text_domain))
			->set_render_callback(function ($fields, $attributes, $inner_blocks, $post_id) {
				$vars['fields'] = $fields;
				$vars['attributes'] = $attributes;
				$vars['id'] = uniqid();
				$vars['post'] = \Timber\Timber::get_post($post_id);

				Timber::render('/blocks/child-pages.html.twig', $vars);
			});
	}
}
