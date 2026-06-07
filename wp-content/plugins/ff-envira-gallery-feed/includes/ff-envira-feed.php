<?php

namespace FF\Envira;

class FF_Envira_Feed {

	protected $rest_controller;


	/**
	 * Constructor
	 */
	public function __construct() {

	}


	/**
	 * Do plugin initialisation
	 */
	public function run() {
		// load dependencies
		include_once( 'rest_controller.php' );

		// create objects
		$this->rest_controller = new REST_Controller();

		// add hooks
		$this->add_hooks();
	}

	/**
	 * Add required hooks
	 */
	public function add_hooks() {


//$this->rest_controller->get_items(array('page-size'=>10, 'page'=>1));

		// public hooks
		if(! is_admin() ) {
			add_action('rest_api_init', array($this->rest_controller, 'register_routes'));
		}
		// admin hooks
		else {

		}

	}



}