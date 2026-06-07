<?php

namespace FF\Midgard\WordPress;

use FF\Midgard\Midgard_Options_Page;

class Midgard_wordpress_Options_Page
{

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
	 * Tab settings
	 */
	private $tab_slug = 'wordpress';
	private $tab_title = 'WordPress';

	/**
	 * Start up
	 */
	public function __construct() {
		// Set up variables
		$this->settings_page = 'midgard-wordpress-settings-page';
		$this->config = array(
			'settings_key'  => 'midgard_wordpress_settings',		//e.g. 'mailchimp_settings
			'page_title'  	=> 'Midgard WordPress Settings',		//e.g. 'MailChimp Settings
			'menu_title'  	=> 'Midgard WordPresss',				//e.g. 'MailChimp Settings
			'menu_slug'  	=> 'midgard_wordpress_settings'			//e.g. 'mailchimp_settings
		);
		$this->settings_key = $this->config['settings_key'];

		// get current settings
		$this->options = get_option( $this->config['settings_key'] ); 	// Option name

		// Actions
		add_action('midgard_settings_tab', array($this, 'settings_tab'));
		add_action('midgard_settings_tab_content', array($this, 'settings_tab_content'));
		add_action('admin_init', array( $this, 'page_init' ) );

		// AJAX actions
		add_action('wp_ajax_midgard-wordpress-get-token', array($this, 'get_token'));
		add_action('wp_ajax_midgard-wordpress-validate-token', array($this, 'validate_token'));

		// custom scripts
		add_action('midgard_options_scripts-' . $this->tab_slug, array($this, 'enqueue_scripts'));
		add_action('midgard_options_styles-' . $this->tab_slug, array($this, 'enqueue_styles'));
	}

	/**
	 * Enqueue custom scripts
	 */
	public function enqueue_scripts() {
		$handle = "midgard-{$this->tab_slug}-options";
		$urls = array(
			'token' 	=> MIDGARD_PLUGIN_WORDPRESS_TOKEN_URL,
			'validate' 	=> MIDGARD_PLUGIN_WORDPRESS_TOKEN_VALIDATE_URL
		);
		wp_enqueue_script( $handle, plugin_dir_url( dirname(__FILE__ ) ) . 'includes/js/midgard-wordpress.js', array( 'jquery' ), MIDGARD_PLUGIN_WORDPRESS_VERSION, false);
		wp_localize_script( $handle, 'MIDGARD_WP_URLS', $urls );
	}

	/**
	 * Enqueue custom scripts
	 */
	 public function enqueue_styles() {
		$handle = "midgard-{$this->tab_slug}-options";
		wp_enqueue_style( $handle, plugin_dir_url( dirname(__FILE__ ) ) . 'includes/css/midgard-wordpress.css');
	}

	/**
	 * Render form output if this is the current tab
	 */
	public function settings_tab_content() {
		if( Midgard_Options_Page::$current_tab == $this->tab_slug) {
			settings_fields( 'midgard_wordpress_auth_group' );		// Option group
			do_settings_sections( $this->settings_page );	// Page
			submit_button();
		}
	}

	/**
	 * Render tab for this options page
	 */
	public function settings_tab() {
		echo Midgard_Options_Page::tab_output($this->tab_slug, $this->tab_title);
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////
	// INPUT FIELD DEFINITIONS
	///////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Register and add settings
	 */
	public function page_init() {

		/* ADD SECTIONS */
		add_settings_section(
			'midgard_wordpress_auth_group', 				// ID
			'Remote WordPress Authentication', 				// Title
			array($this, 'remote_wordpress_callback'), 		// Callback
			$this->settings_page 							// Page
		);

		/* REGISTER SETTINGS */
		register_setting(
			'midgard_wordpress_auth_group', 				// Option group
			$this->settings_key, 							// Option name
			array( $this, 'sanitize' ) 						// Sanitize
		);

		add_settings_field(
			'wp_auth_tokens', 								// ID
			null,											// Title
			array( $this, 'empty_callback' ),				// Callback
			$this->settings_page, 							// Page
			'midgard_wordpress_auth_group'					// Section ID
		);

	}

	///////////////////////////////////////////////////////////////////////////////////////////////////
	// INPUT VALIDATION ON SAVE
	///////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize( $input ) {
		// get inputs
		$instances 	= $input['wp_auth_tokens'];

		// validate
		$new = array();
		if( is_array($instances) ) {
			foreach( $instances as $instance ) {
				$n = array_merge(
					array( 'label' => '', 'url' => '', 'token' => '', 'id' => ''),
					$instance
				);
				// generate unique ID if not already specified
				if( empty( $n['id'] ) ) {
					$n['id'] = uniqid();
				}
				$new[] = $n;
			}
		}

		$new_input['wp_auth_tokens'] = json_encode($new);

		return $new_input;
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////
	// INPUT FIELD DISLPAY CALLBACKS
	///////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Wordpress section callback
	 */
	public function remote_wordpress_callback() { ?>
		<ul>
			<li><?php _e('Secure feeds require authentication via a JSON Web Token (JWT). Enter your WordPress details and login credentials below to retrieve a token.', 'midgard-wordpress'); ?></li>
			<li>
				<?php _e('Your login details are not stored, however it is advisable to use a minimum privilege account such as a subscriber.', 'midgard-wordpress'); ?>
				<?php _e('Please do not use administrator logins!', 'midgard-wordpress'); ?>
			</li>
			<li><?php _e('The label field is used to identify the token in the feed editor.', 'midgard-wordpress');?></li>
		</ul>

		<h2><?php _e('Add Auth Token'); ?></h2>
		<div id="midgard-wordpress-new">
			<table class="widefat">
				<tr>
					<th width="15%" class="row-title"><?php _e('Label', 'midgard-wordpress'); ?></th>
					<th width="40%" class="row-title"><?php _e('WordPress URL', 'midgard-wordpress'); ?></th>
					<th width="15%" class="row-title"><?php _e('Login', 'midgard-wordpress'); ?></th>
					<th width="15%" class="row-title"><?php _e('Password', 'midgard-wordpress'); ?></th>
				</tr>
				<tr valign="top">
					<td scope="row">
						<input type="text" class="large-text" id="new-wp-label" name="new-wp-label" value="" placeholder="Your Site">
					</td>
					<td scope="row">
						<input type="text" class="large-text" id="new-wp-url" name="new-wp-url" value="" placeholder="http://your-site.com">
					</td>
					<td scope="row">
						<input type="text" class="large-text" id="new-wp-login" name="new-wp-login" value="" placeholder="login">
					</td>
					<td scope="row">
						<input type="text" class="large-text" id="new-wp-password" name="new-wp-password" value="" placeholder="password">
					</td>
				</tr>
				<tr valign="top">
					<td scope="row" colspan="4">
						<p class="description"><?php _e('Click "Get Token" to contact the remote server to retrieve an auth token or if you already have a token you can paste it in below.', 'midgard-wordpress');?></p>
						<textarea class="large-text" id="new-wp-token" placeholder="Auth Token" name="new-wp-token"></textarea>
						<p id="midgard-wordpress-validation-result" style="float:none; padding-left:30px; width:auto; height:auto; "></p>
						<p>
							<button class="button-secondary" id="midgard-wordpress-btn-token"><?php _e('Get Token', 'midgard-wordpress');?></button>
							<button class="button-primary" id="midgard-wordpress-btn-add"><?php _e('Add WordPress', 'midgard-wordpress');?></button>
						</p>
					</td>
			</table>
		</div>

		<h2><?php _e('Saved Auth Tokens'); ?></h2>

		<?php
			$instances = isset( $this->options['wp_auth_tokens'] ) ? $this->options['wp_auth_tokens'] : '';
			$instances = json_decode( str_replace( "\'", "'" ,  $instances), true );
		?>

		<table class="widefat midgard-wordpress-table">
			<thead>
				<tr>
					<th width="15%"><?php _e( 'Label', 'midgard-wordpress' ); ?></th>
					<th width="30%"><?php esc_attr_e( 'WordPress URL', 'midgard-wordpress' ); ?></th>
					<th><?php esc_attr_e( 'Token', 'midgard-wordpress' ); ?></th>
					<th width="30">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<?php

				if( $instances && is_array($instances)) {

					foreach( $instances as $index=>$wp) {
						$wp = array_merge( array(
							'label'	=> '',
							'url'	=> '',
							'token'	=> '',
							'id'	=> ''
						), $wp);

						printf('<tr class="%s', $index % 2 == 1 ? 'alternate' : '');

						// hidden fields
						printf( '<input type="hidden" class="wp-id" name="midgard-wordpress[%s][id]" value="%s"</td>', $index, $wp['id'] );
						printf( '<input type="hidden" class="wp-label" name="midgard-wordpress[%s][label]" value="%s"</td>', $index, $wp['label'] );
						printf( '<input type="hidden" class="wp-url" name="midgard-wordpress[%s][url]" value="%s"</td>', $index, $wp['url'] );
						printf( '<input type="hidden" class="wp-token" name="midgard-wordpress[%s][token]" value="%s"</td>', $index, $wp['token'] );

						// display cells
						printf( '<td>%s</td>', $wp['label'] );
						printf( '<td>%s</td>', $wp['url'] );
						printf( '<td class="wrap"><small>%s</small></td>', $wp['token'] );

						// delete button
						printf( '<td><a class="midgard-wordpress-delete dashicons dashicons-trash" href="#" ></a></td>');
						echo '</tr>';
					}

				}
				?>

			</tbody>
		</table>

		<?php
	}

	/**
	 * return nothing - we want to hide this field altogether (placeholder)
	 *	the "real" fields are output in the remote_wordpress_callback() method
	 */
	public function empty_callback() {
		echo '';
		return;
	}


	///////////////////////////////////////////////////////////////////////////////////////////////////
	// AJAX ACTIONS
	///////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Get a token from the remote server
	 */
	public function get_token() {
		// sanity check required vars
		$vars = array('endpoint', 'username', 'password');
		foreach( $vars as $var ) {
			if( ! array_key_exists($var, $_POST) || empty($_POST[$var]) ) {
				wp_send_json_error( __('Required information missing. Please specify username, password and WordPress URL', 'midgard-wordpress') );
				die();
			}
		}

		// extract post parameters
		$post_params = array(
			'method'		=> 'POST',
			'body'			=> array(
				'username'	=> $_POST['username'],
				'password'	=> $_POST['password']
			)
		);

		// escape endpoint for safety
		$endpoint = esc_url($_POST['endpoint']);

		// post the request
		$response = wp_remote_post( $endpoint, $post_params );

		// clear buffer in case any warnings to this point
		ob_clean();

		// init return data
		$data = array(
			'success' => false,
			'message' => '',
			'body'    => ''
		);

		// determine success or failure
		if( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			wp_send_json_error( $error_message );
		} else {
			$body = json_decode($response['body'], true);
			$res  = $response['response'];
			$code  = $res['code'];
			$data['body'] = $body;

			switch( $res['code'] ) {
				case '200' :
					if( is_array($body) && array_key_exists('token', $body) ) {
						$data['success'] = true;
					} else {
						$data['message'] = __('Can\'t access validation endpoint. Please check WordPress URL is correct and that the JWT authentication plugin is installed on the remote server', 'midgard-wordpress');
					}
					break;
				case '403' :
					$data['message'] = __('Access forbidden - please check your credentials', 'midgard-wordpress');
					break;
				case '404' :
					$data['message'] = __('Can\'t access validation endpoint. Please check WordPress URL is correct and that the JWT authentication plugin is installed on the remote server', 'midgard-wordpress');
					break;
				default :
					$data['message'] = __('Unknown error', 'midgard-wordpress');
					break;
			}
			wp_send_json( $data );
		}

		// ensure no further output
		wp_die();
	}

	/**
	 * Validate a token against the remote server
	 */
	public function validate_token() {
		die('todo - implement token validate');
	}


}

// init the options page
if( is_admin() ) {
	$options_page = new Midgard_wordpress_Options_Page();
}
