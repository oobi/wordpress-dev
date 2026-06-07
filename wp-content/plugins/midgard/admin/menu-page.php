<?php

namespace FF\Midgard;

class Midgard_Menu_Page {

	/**
	 * Constructor configuration (overridden by child classes)
	 */
	protected $config;

	/**
	 * Slug for menu page
	 */
	private $menu_page;

	/**
	 * Start up
	 */
	public function __construct() {
		// Set up variables
		$this->menu_page = 'midgard-app';
		$this->config = array(
			'settings_key'  => 'midgard_app',			//e.g. 'mailchimp_settings'
			'page_title'  	=> 'Midgard Feeds',			//e.g. 'MailChimp Settings'
			'menu_title'  	=> 'Midgard Feeds',			//e.g. 'MailChimp Settings'
			'sub_page_title'=> 'Feed Overview',			//e.g. 'MailChimp Settings'
			'sub_menu_title'=> 'Feed Overview',			//e.g. 'MailChimp Settings'
			'menu_slug'  	=> 'midgard_app',			//e.g. 'mailchimp_settings'
			'menu_icon'  	=> 'dashicons-smartphone',	//e.g. 'dashicons-book'
		);
		$this->settings_key = $this->config['settings_key'];

		// Actions
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );

		// display CSS in <head>
		add_action( 'wp_head', 	  array( $this, 'display_custom_css' ) );
	}

	/**
	 * Add options page
	 */
	public function add_plugin_page() {

		// Add the App menu page (all app custom post types will go under here)
		add_menu_page(
			$this->config['page_title'],		// Page Title
			$this->config['menu_title'],		// Menu Title
			'manage_options',					// Capability (restrict to admins only)
			$this->config['menu_slug'],			// Menu Slug
			'', 								// Callback - leave blank because this is a placeholder only
			$this->config['menu_icon']//,		// Menu Icon
			//100  								// Position
		);

	}


}

// init the menu page
if( is_admin() ) {
	$menu_page = new Midgard_Menu_Page();
}


