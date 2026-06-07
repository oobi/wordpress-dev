<?php

namespace Firefly\Carbon\Blocks;

use Carbon_Fields\Field;
use Carbon_Fields\Block;
use Carbon_Fields\Container;
use Timber\Timber;

class BlockSliderBlock
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
		Block::make(__('Block Slider', $this->text_domain))
			->set_description('Block Slider')
			->set_icon('images-alt2')
			->set_mode('both')
			->add_fields([
				Field::make('checkbox', 'random_start', 'Random Start')
					->set_help_text("Optionally start on a random slide.")
					->set_width(33),
				Field::make('text', 'delay', 'Delay (ms)')
					->set_help_text("Delay in milliseconds between transitions.")
					->set_attribute('type', 'number')
					->set_default_value(8000)
					->set_width(33),
				Field::make('select', 'alignment', 'Alignment')
					->set_options( array(
						'' => 'None',
						'alignwide' => 'Wide',
						'alignfull' => 'Full Width'
					) )
					->set_width(33),
			])
			->set_inner_blocks( true )
			->set_inner_blocks_position('below')
			->set_category('custom-blocks', __('Firefly Blocks', $this->text_domain))
			->set_render_callback(function ($fields, $attributes, $inner_blocks) {
				$vars['fields'] = $fields;
				$vars['attributes'] = $attributes;
				$vars['id'] = uniqid();
				$vars['inner_blocks'] = $inner_blocks;
				Timber::render('/blocks/block-slider.html.twig', $vars);
			});
	}
}
