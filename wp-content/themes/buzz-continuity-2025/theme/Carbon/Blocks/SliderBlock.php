<?php

namespace Firefly\Carbon\Blocks;

use Carbon_Fields\Field;
use Carbon_Fields\Block;
use Carbon_Fields\Container;
use Timber\Timber;

class SliderBlock
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
		Block::make(__('slider', $this->text_domain))
			->set_description('Image Slider')
			->set_icon('images-alt2')
			->set_mode('edit')
			->add_fields([
				Field::make( 'complex', 'slides', 'Slides' )
                    ->set_layout( 'tabbed-vertical' )
                    ->add_fields( 'slide', 'Slide', [
                        Field::make( 'image', 'image', 'Image' )
                            ->set_required( TRUE ),

						Field::make( 'color', 'bg', 'Background' )
							->set_palette( ['#00B5EA', '#8E499C', '#EE2A7C', '#F47722',
											'#FCB32E', '#ffdfa5', '#72BF44', '#6b902d',
											'#FFFFFF', '#686b6c', '#000000'] )

                        // Field::make("text", "heading", "Heading"),

                    ])
				// 	->set_header_template('
                //     <% if (heading) { %>
                //         <%- heading %>
                //     <% } else { %>
                //         Slide
                //     <% } %>
                // ')
			])

			->set_category('custom-blocks', __('Firefly Blocks', $this->text_domain))
			->set_render_callback(function ($fields, $attributes, $inner_blocks) {
				$vars['fields'] = $fields;
				$vars['attributes'] = $attributes;
				$vars['id'] = uniqid();
				Timber::render('/blocks/slider.html.twig', $vars);
			});
	}
}
