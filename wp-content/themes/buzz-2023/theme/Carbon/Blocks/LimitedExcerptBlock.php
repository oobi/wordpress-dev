<?php

namespace Firefly\Carbon\Blocks;

use Carbon_Fields\Field;
use Carbon_Fields\Block;
use Carbon_Fields\Container;
use Timber\Timber;

use Firefly\Buzz\Articles;
use Firefly\Buzz\Newsletter;

class LimitedExcerptBlock
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
		Block::make('limited-excerpt', __('Limited Excerpt', $this->text_domain))
			->set_description('Displays the excerpt only when manually defined in the post.')
			->set_icon('list-view')
			->set_mode('edit')
			->add_fields([
				Field::make('text', 'ff_excerpt_length', __('Excerpt Lengh', $this->text_domain))
			])
			->set_category('custom-blocks', __('Buzz Theme Blocks', $this->text_domain))
			->set_render_callback(function ($fields, $attributes, $inner_blocks, $post_id) {
				$vars['fields'] = $fields;
				$vars['content'] = $inner_blocks;
				$vars['attributes'] = $attributes;
				$vars['index'] = uniqid();
				$context = Timber::context();

				$context['excerpt'] = preg_replace('/((\w+\W*){'.($fields['ff_excerpt_length']-1).'}(\w+))(.*)/', '${1}', get_the_excerpt($post_id));

				Timber::render('/blocks/limited-excerpt.html.twig', $context);
			});
	}


}
