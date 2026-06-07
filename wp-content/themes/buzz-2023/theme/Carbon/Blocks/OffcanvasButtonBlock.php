<?php

namespace Firefly\Carbon\Blocks;

use Carbon_Fields\Field;
use Carbon_Fields\Block;
use Carbon_Fields\Container;
use Timber\Timber;

use Firefly\Buzz\Articles;
use Firefly\Buzz\Newsletter;

class OffcanvasButtonBlock
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
		Block::make('offcanvas-button', __('Offcanvas Button', $this->text_domain))
			->set_description('A button that opens the Offcanvas Menu.')
			->set_icon('list-view')
			->set_mode('edit')
			->add_fields([
				Field::make('html', 'ff_obb_label', __('Content', $this->text_domain))
					->set_html('<p>Offcanvas Button</p>'),
			])
			->set_category('custom-blocks', __('Buzz Theme Blocks', $this->text_domain))
			->set_render_callback(function ($fields, $attributes, $inner_blocks) {
				$vars['fields'] = $fields;
				$vars['content'] = $inner_blocks;
				$vars['attributes'] = $attributes;
				$vars['index'] = uniqid();

				$articles = get_articles_from_newsletter(true);
				$vars['articles'] = $articles;

				$context = Timber::context();

				Timber::render('/blocks/offcanvas-button.html.twig', $context);
			});
	}


}
