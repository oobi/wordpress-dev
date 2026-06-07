<?php

namespace FF\DocRaptor;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       www.fi.net.au
 * @since      1.0.0
 *
 * @package    DocRaptor
 * @subpackage DocRaptor/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    DocRaptor
 * @subpackage DocRaptor/admin
 * @author     Firefly Interactive <info@fi.net.au>
 */
class DocRaptorAdmin {

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
	 * key which stores settings in the database (e.g. email_view_swettings)
	 */
	protected $settings_key;

	/**
	 * Constructor configuration (overridden by child classes)
	 */
	protected $config;

	/**
	 * Slug for settings page
	 */
	private $settings_page;

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

		// Set up variables
		$this->settings_page = 'docraptor-settings-page';
		$this->config = array(
			'settings_key'  => 'docraptor_settings',		//e.g. 'mailchimp_settings
			'page_title'  	=> 'DocRaptor Generator',		//e.g. 'MailChimp Settings
			'menu_title'  	=> 'DocRaptor Generator',		//e.g. 'MailChimp Settings
			'menu_slug'  	=> 'docraptor_settings'		//e.g. 'mailchimp_settings
		);
		$this->settings_key = $this->config['settings_key'];


		// Actions
		add_action( 'admin_menu', array( $this, 'add_management_page' ) );

		// ajax handlers
		add_action( 'wp_ajax_docraptor_start', array( $this, 'ajax_generator_start' ) );
		add_action( 'wp_ajax_docraptor_status', array( $this, 'ajax_generator_status' ) );

		// query vars
		add_filter('query_vars', array($this, 'add_query_vars'));
		add_filter('init', array($this, 'add_rewrite_rules'));
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

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// CONFIG WARNING
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Show an admin message that config is incomplete
	 */
	public function setup_incomplete_admin_notice() {
		$class 		= "update-nag";
		$message 	= sprintf( 'DocRaptor functionality is disabled. You must complete the setup and add your DocRaptor API key to wp-config.php.');
		echo 		"<div class=\"$class\"> <p>$message</p></div>";
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// OPTIONS PAGE
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Provide hook for sub-plugins to enqueue styles
	 */
	public function enqueue_options_styles() {
		if( !is_admin() ) return;
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Provide hook for sub-plugins to enqueue scripts
	 */
	public function enqueue_options_scripts() {
		if( !is_admin() ) return;
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/admin.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Add options page
	 */
	public function add_management_page() {
		// admins only
		if( ! current_user_can('manage_options') ) {
			return false;
		}

		// This page will be under "Settings"
		$page = add_management_page(
			$this->config['page_title'],		// Page Title
			$this->config['menu_title'],		// Menu Title
			'manage_options',					// Capability
			$this->config['menu_slug'],			// Menu Slug
			array( $this, 'create_management_page' )	// Callback function
		);

		add_action( "admin_print_styles-{$page}", array($this, 'enqueue_options_styles') );
		add_action( "admin_print_scripts-{$page}", array($this, 'enqueue_options_scripts') );
	}

	/**
	 * Options page callback
	 */
	public function create_management_page() {
		// Set class property
		$this->options = get_option( $this->config['settings_key'] ); 	// Option name
		?>
		<div class="wrap ff-docraptor">

			<h2><?php echo $this->config['page_title']; ?></h2>

			<p>Select the month for which you would like to generate an archive PDF</p>
			<p>You should generate TEST pdfs if you are just checking formatting.<br>You can generate only a limited number of production PDFs per month.</p>

			<div class="ff-docraptor-params">
				<?php
					$archives = wp_get_archives( [
						'type'            => 'monthly',
						// 'limit'           => '',
						'format'          => 'option',
						'before'          => '',
						'after'           => '',
						'show_post_count' => false,
						'echo'            => 0,
						'order'           => 'DESC',
						'post_type'     => 'post'
					] );

					echo '<select name="ff-docraptor-month" required><option value="">Select a month</option>' . $archives . '</select>';
				?>
				<select name="ff-docraptor-mode" required>
					<option value="">Select PDF mode</option>
					<option value="test">Test</option>
					<option value="prod">Production</option>
				</select>

				<button class="ff-docraptor-btn button-primary" disabled>Generate PDF</button>
			</div>

			<div class="ff-docraptor-status">

			</div>
		</div>
		<?php
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// AJAX Handlers
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function ajax_generator_start() {
		$test = $_POST['mode'] == 'test';
		$url = $_POST['month'];

		$status = $this->generate_pdf( $url, $test );

		$response = ['status' => $status];
		echo json_encode( $response );

		wp_die();
	}

	public function ajax_generator_status() {
		$status_id = $_POST['status_id'];

		$api_key = DOCRAPTOR_API_KEY;
		$url = "https://docraptor.com/status/{$status_id}?user_credentials={$api_key}";
		$remote = wp_remote_get($url);
		$response_j = wp_remote_retrieve_body($remote);
		echo $response_j;

		wp_die();
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// Generate PDF
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function generate_pdf( $content_url='', $test=false ) {
		// generate PDF
		// increase max execution time to avoid timeouts
		set_time_limit(300);

		// set DocRaptor URL
		$api_key = DOCRAPTOR_API_KEY;
		$url = "https://docraptor.com/docs?user_credentials={$api_key}";
		$filename = "archive-" . date( 'Y-m' ) . '.pdf';

		$remote = wp_remote_get(trailingslashit($content_url) . 'docraptor', );
		$content = wp_remote_retrieve_body( $remote );

		$post_array = array(
			'doc[name]' 				=> $filename,
			'doc[document_type]' 		=> 'pdf',
			'doc[test]' 				=> $printPreviewMode ? 'true' : 'false',
			'doc[async]'				=> 'true', // asynchronous job
			'doc[document_content]' 	=> $content
		);

		$postdata = http_build_query($post_array);

		$opts = array(
			'method'  => 'POST',
			'headers'  => 'Content-type: application/x-www-form-urlencoded',
			'body' => $postdata,
			'timeout' => 300
		);


		$remote = wp_remote_get($url, $opts); // if NOT using async then this will be the doc
		$response_j = wp_remote_retrieve_body( $remote );
		$response 	= json_decode($response_j, true);

		return $response['status_id'] ?: false;
	}
}
