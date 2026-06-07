<?php

class Buzz_Addon_Dates_Customizer
{

	// MUST match the same parameters in the theme Customizer.php
	private $config_id = 'buzz_customizer';
	private $panel_id = 'buzz_theme_options';

	public function __construct()
	{
		$exists = false;

		// Kirki V3
		if (defined('KIRKI_VERSION') && version_compare(KIRKI_VERSION, '4.0.0', '<')) {
			$configs = \Kirki_Config::get_instance()->get_config_ids();
			$exists = in_array($this->config_id, $configs);
		}
		// Kirki v4+
		else {
			$configs = \Kirki::$config;
			$exists = array_key_exists($this->config_id, $configs);
		}

		// if the config does not exist, add it and display it
		// this is used only in the event that the theme doesn't explicitly add support
		// for Kirki fields

		if (!$exists) {
			// setup Kirki
			\Kirki::add_config($this->config_id, array(
				'capability'    => 'edit_theme_options',
				'option_type'   => 'theme_mod',
				// 'disable_output'=> true, // do not automatically output CSS, we will enqueue it manually for greater control
				'styles_priority' => 999,
			));

			\Kirki::add_panel($this->panel_id, array(
				'priority'    => 999, // last in list
				'title'       => ff__('Theme Options'),
				'description' => ff__('Customize the look of your Buzz newsletter'),
			));

			// show the options here
			$this->set_options();
		}


	}

	/**
	 * Add options to Customizer with Kirki
	 */
	public function set_options()
	{

		/****************************************************************
		 * SECTION
		 ****************************************************************/

		$section_id = 'buzz_dates';
		\Kirki::add_section($section_id, array(
			'title' 		=> ff__('Dates'),
			'description' 	=> ff__('Customize the newsletter
			section.'),
			'priority'		=> 80,
			'panel'			=> $this->panel_id
		));

		/****************************************************************
		 * DATE SETS
		 ****************************************************************/

		// Info Label
		\Kirki::add_field($this->config_id, array(
			'type'        => 'custom',
			'settings'    => "{$section_id}_info_3",
			'label'       => ff__('Date Sets'),
			'section'     => $section_id,
			'priority'    => 10,
		));

		// Date Sets
		\Kirki::add_field($this->config_id, array(
			'type'       	=> 'repeater',
			'settings'    	=> "{$section_id}_sets",
			'label'       	=> ff__('Date Sets'),
			'section'     	=> $section_id,
			'priority'    	=> 10,
			'row_label' 	=> array(
				'type' => 'field',
				'value' => ff__('Set'),
				'field' => 'label',
			),
			'button_label' => ff__('Add new set'),
			'default'		=> array(
				array(
					'label' => ff__('Dates for your Diary'),
					'class' => ff__('dates-for-your-diary'),
				)
			),
			'fields' => array(
				'label' => array(
					'type'        => 'text',
					'label'       => ff__('Label'),
					'description' => '',
					'default'     => '',
				),
				'class' => array(
					'type'        => 'text',
					'label'       => ff__('Class'),
					'description' => '',
					'default'     => '',
				),
			)
		));

		/****************************************************************
		 * DATE SETS
		 ****************************************************************/

		// Info Label
		\Kirki::add_field($this->config_id, array(
			'type'        => 'custom',
			'settings'    => "{$section_id}_info_4",
			'label'       => ff__('Date Icons'),
			'section'     => $section_id,
			'priority'    => 10,
		));

		// Date Icons
		\Kirki::add_field($this->config_id, array(
			'type'       	=> 'repeater',
			'settings'    	=> "{$section_id}_icons",
			'label'       	=> ff__('Date Icons'),
			'description'  	=> ff__('SVG recommended'),
			'section'     	=> $section_id,
			'priority'    	=> 10,
			'row_label' 	=> array(
				'type' => 'field',
				'value' => ff__('Icon'),
				'field' => 'label',
			),
			'button_label' => ff__('Add new icon'),
			'fields' => array(
				'label' => array(
					'type'        => 'text',
					'label'       => ff__('Label'),
					'description' => '',
					'default'     => '',
				),
				'class' => array(
					'type'        => 'text',
					'label'       => ff__('Class'),
					'description' => '',
					'default'     => '',
				),
				'image' => array(
					'type'        => 'image',
					'label'       => ff__('Image'),
					'description' => '',
					'default'     => '',
				),
			)
		));
	}

	/**
	 * Add color options to Customizer with Kirki
	 */
	public function set_colors()
	{
		$section_id = 'buzz_colors';

		// Group Label
		\Kirki::add_field($this->config_id, array(
			'type'        => 'custom',
			'settings'    => "{$section_id}_info_dates",
			'label'       => ff__('Dates'),
			'section'     => $section_id,
			'priority'    => 10,
		));

		// Dates
		\Kirki::add_field($this->config_id, array(
			'type'        	=> 'multicolor',
			'settings'   	=> "{$section_id}_dates",
			'label'       	=> ff__('Dates'),
			'section'     	=> $section_id,
			'transport'		=> 'auto',
			'alpha'			=> false,
			'default'		=> [
				'widget-title'	=> '#333333',
				'date-title'	=> '#333333',
				'date-link'		=> '#3476a6',
				'date-desc'		=> '#333333',
				'month-bg'		=> '#333333',
				'month-text'	=> '#FFFFFF',
				'trim'			=> '#000000'
			],
			'choices'		=> [
				'widget-title' 	=> ff__('Widget Title'),
				'date-title'	=> ff__('Title'),
				'date-link'		=> ff__('Link'),
				'date-desc'		=> ff__('Description'),
				'month-bg'		=> ff__('Month Background/Date Text'),
				'month-text'	=> ff__('Month Text'),
				'trim'			=> ff__('Trim'),
			],
			'output'		=> [
				['choice' => 'widget-title', 	'element' => '.widget-area .buzz-dates .widget-title', 					'property' => 'color'],
				['choice' => 'date-title',	 	'element' => '.widget-area .buzz-dates .date-wrapper .title', 			'property' => 'color'],
				['choice' => 'date-link',	 	'element' => '.widget-area .buzz-dates .date-wrapper .link', 			'property' => 'color'],
				['choice' => 'date-desc', 		'element' => '.widget-area .buzz-dates .date-wrapper .description', 	'property' => 'color'],
				['choice' => 'month-bg', 		'element' => '.widget-area .buzz-dates .date-wrapper .date__month', 	'property' => 'background-color'],
				['choice' => 'month-bg', 		'element' => '.widget-area .buzz-dates .date-wrapper .date', 			'property' => 'color'],
				['choice' => 'month-text', 	'element' => '.widget-area .buzz-dates .date-wrapper .date__month', 	'property' => 'color'],
				['choice' => 'trim', 			'element' => '.widget-area .buzz-dates .date-wrapper .date-inner',		'property' => 'border-color'],

				// email view
				['choice' => 'widget-title', 	'element' => '#email-view .buzz-dates .widget-title', 					'property' => 'color'],
				['choice' => 'date-title',	 	'element' => '#email-view .buzz-dates .date-wrapper .title', 			'property' => 'color'],
				['choice' => 'date-link',	 	'element' => '#email-view .buzz-dates .date-wrapper .link', 			'property' => 'color'],
				['choice' => 'date-desc', 		'element' => '#email-view .buzz-dates .date-wrapper .description', 		'property' => 'color'],
				['choice' => 'month-bg', 		'element' => '#email-view .buzz-dates .date-wrapper .date__month', 		'property' => 'background-color'],
				['choice' => 'month-bg', 		'element' => '#email-view .buzz-dates .date-wrapper .date', 			'property' => 'color'],
				['choice' => 'month-text', 	'element' => '#email-view .buzz-dates .date-wrapper .date__month', 		'property' => 'color'],
				['choice' => 'trim', 			'element' => '#email-view .buzz-dates .date-wrapper .date', 			'property' => 'border-color'],
			]
		));
	}
}
