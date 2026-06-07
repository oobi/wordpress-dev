<?php

namespace Firefly\Carbon\Blocks;

use Carbon_Fields\Field;
use Carbon_Fields\Block;
use Carbon_Fields\Container;
use Timber\Timber;

use Firefly\Buzz\Articles;
use Firefly\Buzz\Newsletter;

class SearchDropdownBlock
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
		Block::make('search-dropdown', __('Search Dropdown', $this->text_domain))
			->set_description('A button that opens a search dropdown.')
			->set_icon('list-view')
			->set_mode('edit')
			->add_fields([
				Field::make('html', 'ff_search_label', __('Content', $this->text_domain))
					->set_html('<p>Search Dropdown</p>'),
			])
			->set_category('custom-blocks', __('Buzz Theme Blocks', $this->text_domain))
			->set_render_callback(function ($fields, $attributes, $inner_blocks) {
				$vars['fields'] = $fields;
				$vars['content'] = $inner_blocks;
				$vars['attributes'] = $attributes;
				$vars['index'] = uniqid();
				$context = Timber::context();

				Timber::render('/blocks/search-dropdown.html.twig', $context);
			});
	}


}
