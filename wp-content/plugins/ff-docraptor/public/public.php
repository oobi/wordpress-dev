<?php

namespace FF\DocRaptor;

/**
 * The public functionality of the plugin.
 *
 * @link       www.fi.net.au
 * @since      1.0.0
 *
 * @package    DocRaptor
 * @subpackage DocRaptor/public
 */

class DocRaptorPublic {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	public function add_query_vars($public_query_vars) {
		array_push($public_query_vars, 'docraptor');
		return $public_query_vars;
	}

	public function add_rewrite_rules() {
		// rewrite month archive to docraptor
		// from 2014/07/docraptor
		// to /?m=201407&docraptor=1
		add_rewrite_tag('%docraptor%','([^&]+)');
		add_rewrite_rule('^([0-9]+)/([0-9]+)/docraptor?.*?$', 'index.php?m=$matches[1]$matches[2]&docraptor=1','top');
	}

}
