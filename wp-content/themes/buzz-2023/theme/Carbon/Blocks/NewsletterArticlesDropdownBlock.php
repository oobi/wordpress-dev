<?php

namespace Firefly\Carbon\Blocks;

use Carbon_Fields\Field;
use Carbon_Fields\Block;
use Carbon_Fields\Container;
use Timber\Timber;

use Firefly\Buzz\Articles;
use Firefly\Buzz\Newsletter;

class NewsletterArticlesDropdownBlock
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
		$fields = class_exists('Buzz_Addon_Taxonomies') ? [
			Field::make('html', 'ff_nadb_block', __('Content', $this->text_domain))
				->set_html('<p>Article Menu</p>'),
			Field::make('checkbox', 'group_by_category', __('Group by category', $this->text_domain))
					->set_option_value('yes')
					->set_default_value('yes')
		] : [];


		Block::make('newsletter-articles-dropdown-block', __('Newsletter Articles Dropdown', $this->text_domain))
			->set_description('A dropdown including all the current articles in this newsletter.')
			->set_icon('list-view')
			->set_mode('edit')
			->add_fields($fields)
			->set_category('custom-blocks', __('Buzz Theme Blocks', $this->text_domain))
			->set_render_callback(function ($fields, $attributes, $inner_blocks) {
				$vars['fields'] = $fields;
				$vars['content'] = $inner_blocks;
				$vars['attributes'] = $attributes;
				$vars['index'] = uniqid();

				$groupByCategory = $fields['group_by_category'] === 'yes';

				$articles = get_articles_from_newsletter($groupByCategory);
				$vars['articles'] = $articles;
				$vars['group_by_category'] = $groupByCategory;

				Timber::render('/blocks/newsletter-articles-dropdown.html.twig', $vars);
			});
	}


}
