<?php

namespace Firefly\Carbon\Blocks;

use Carbon_Fields\Field;
use Carbon_Fields\Block;
use Carbon_Fields\Container;
use Timber\Timber;

class ImageSliderBlock
{
	protected $text_domain;

	public function __construct()
	{
		$this->text_domain = wp_get_theme()->get('TextDomain');
		add_action('carbon_fields_register_fields', [$this, 'register']);
	}

	function get_image_sizes() {
		$sizes = get_intermediate_image_sizes();
		return array_combine($sizes, $sizes);
	}

	/**
	 * Register Carbon Fields blocks
	 */
	public function register()
	{
		Block::make(__('Image Slider', $this->text_domain))
			->set_description('Image Slider')
			->set_icon('images-alt2')
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
				Field::make('select', 'size', 'Image Size')
					->set_options( array(
						'default' => 'Default',
					) )
					->add_options( [$this, 'get_image_sizes'] )
					->set_width(33),
				Field::make('complex', 'slides', 'Slides')
					->set_layout('tabbed-vertical')
					->add_fields('slide', 'Slide', [
						Field::make('image', 'image', 'Image')
							->set_required(TRUE),
						Field::make('text', 'title', 'Title'),
						Field::make('textarea', 'text', 'Text'),
						Field::make('text', 'citation', 'Citation'),
						Field::make('text', 'link_url', 'Link URL')
							->set_width(50),
						Field::make('text', 'link_text', 'Link Text')
							->set_width(50),
						Field::make('checkbox', 'link_new_tab', 'Open link in new tab?')
							->set_option_value('yes'),
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
				// shuffle start?
				if( $fields['random_start'] ) {
					shuffle($fields['slides']);
				}

				$vars['fields'] = $fields;
				$vars['attributes'] = $attributes;
				$vars['id'] = uniqid();

				Timber::render('/blocks/image-slider.html.twig', $vars);
			});
	}
}
