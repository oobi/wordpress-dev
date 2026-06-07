<?php

namespace Firefly\Carbon\Blocks;

use Carbon_Fields\Field;
use Carbon_Fields\Block;
use Carbon_Fields\Container;
use Timber\Timber;
use Firefly\Setup\Config;

class StatisticsGridBlock
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
		$colors = Config::get('theme')['theme_colors'];
		$palette = array_values($colors);

		Block::make(__('statistics-grid', $this->text_domain))
			->set_description('Statistics Grid')
			->set_icon('heading')
			->set_mode('both')
			->add_fields([
				Field::make('complex', 'statistic', 'Statistic')
					->add_fields([
						Field::make('text', 'icon', 'Icon')->set_width(35),
						Field::make('text', 'data', 'Data')->set_width(15),
						Field::make('text', 'label', 'Label')->set_width(50),
					])
					->set_min(1)
					->set_help_text('Icons font awesome icons, references can be found here: <a href="https://fontawesome.com/search">https://fontawesome.com/search</a>'),
				Field::make('color', 'text_color', 'Text Color')
					->set_width(33)
					->set_palette($palette)
					->set_default_value($palette[0]),
				Field::make('color', 'icon_color', 'Icon Color')
					->set_width(33)
					->set_palette($palette)
					->set_default_value($palette[0]),
				Field::make('color', 'icon_background', 'Icon Background')
					->set_width(33)
					->set_palette($palette)
					->set_default_value($palette[4])
			])

			->set_category('custom-blocks', __('Firefly Blocks', $this->text_domain))
			->set_render_callback(function ($fields, $attributes, $inner_blocks) {
				$vars['fields'] = $fields;
				$vars['attributes'] = $attributes;
				$vars['id'] = uniqid();
				Timber::render('/blocks/statistics-grid.html.twig', $vars);
			});
	}
}
