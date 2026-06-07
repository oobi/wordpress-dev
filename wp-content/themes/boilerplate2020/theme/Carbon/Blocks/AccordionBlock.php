<?php

namespace Firefly\Carbon\Blocks;

use Carbon_Fields\Field;
use Carbon_Fields\Block;
use Carbon_Fields\Container;
use Timber\Timber;

class AccordionBlock
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
		Block::make(__('accordion', $this->text_domain))
			->set_description('Accordion is a collapsible content section')
			->set_icon('list-view')
			->set_mode('both')
			->add_fields([
				Field::make('text', 'heading', __('Accordion Heading', $this->text_domain))
					->set_required(true)
					->set_width(80),
				Field::make('checkbox', 'open', __('Open', $this->text_domain))
					->set_option_value('yes')
					->set_width(20)
			])

			->set_inner_blocks(true)
			->set_inner_blocks_position('below')
			->set_inner_blocks_template(array(
				array('core/paragraph', array(
					'placeholder' => 'Accordion content goes here',
				)),
			))
			->set_category('custom-blocks', __('Firefly Blocks', $this->text_domain))
			->set_render_callback(function ($fields, $attributes, $inner_blocks) {
				$vars['fields'] = $fields;
				$vars['content'] = $inner_blocks;
				$vars['attributes'] = $attributes;
				$vars['index'] = uniqid();
				Timber::render('/blocks/accordion.html.twig', $vars);
			});
	}
}
