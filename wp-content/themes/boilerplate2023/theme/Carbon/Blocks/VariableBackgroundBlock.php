<?php

namespace Firefly\Carbon\Blocks;

use Carbon_Fields\Field;
use Carbon_Fields\Block;
use Carbon_Fields\Container;
use Timber\Timber;

class VariableBackgroundBlock
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
		Block::make(__('Variable Background', $this->text_domain))
			->set_description('A container with a variable background')
			->set_icon('slides')
			->set_mode('both')
			->add_fields([
				Field::make('checkbox', 'random_start', 'Random Start')
					->set_help_text("Optionally start on a random background.")
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
				Field::make( 'complex', 'images', 'Backgrounds' )
					->set_context( 'side' )
                    ->set_layout( 'tabbed-vertical' )
                    ->add_fields( 'image', ' Image', [
                        Field::make( 'image', 'image', 'Image' )
                            ->set_required( TRUE )
                    ])
				])

			->set_inner_blocks(true)
			->set_inner_blocks_position('below')
			->set_inner_blocks_template(array(
				array('core/paragraph', array(
					'placeholder' => 'Block content goes here',
				)),
			))
			->set_category('custom-blocks', __('Firefly Blocks', $this->text_domain))
			->set_render_callback(function ($fields, $attributes, $inner_blocks) {
				$vars['fields'] = $fields;
				$vars['content'] = $inner_blocks;
				$vars['attributes'] = $attributes;
				$vars['index'] = uniqid();

				// variable image background
				$backgrounds = $fields['images'];
				$vars['backgrounds'] = [];

				foreach( $backgrounds as $background ) {
					$image = wp_get_attachment_image_src($background['image'], 'cropped_hero');
					if( $image ) {
						$vars['backgrounds'][] = wp_get_attachment_image_src($background['image'], 'cropped_hero')[0];
					}
				}

				// randomise the order
				shuffle($vars['backgrounds']);

				Timber::render('/blocks/variable-bg.html.twig', $vars);
			});
	}
}
