<?php

class Gecka_plugin_Pro {

	private $settings;
	
	private $plugin_file;
	private $plugin_version;
	
	private $textdomain;
	
	private $update_checked = false;

	const TIMEOUT = 15;
	const AUTH_TRIES = 3;
	const API_URL = 'http://10.0.4.110/Gecka-apps.com/htdocs/plugin-api/$1/$2';
	const PLUGIN_URL = 'http://gecka-apps.com';
	const PLUGIN = 'gecka-submenu-pro';
	const PLUGIN_NAME = "Gecka Submenu Pro";

	/**
	 * Constructor
	 * 
	 * @param string $plugin_file path to the plugin file from the plugin dir
	 */
	public function __construct($plugin_file, $plugin_version, $textdomain='')  {

		$this->plugin_file 		= $plugin_file;
		$this->plugin_version 	= $plugin_version;
		$this->textdomain 		= $textdomain;
		
		$this->settings = get_option(self::PLUGIN . '_registration_settings');
		if(!$this->settings) $this->settings = array();
		
		add_action( 'admin_init', array( &$this, 'admin_init' ) );
		//add_action( 'install_plugins_pre_plugin-information', array( &$this, 'show_plugin_info' ) );
		
		// override plugin_api call for our plugin
		add_filter('plugins_api', array($this, 'plugins_api'), 10,3);
		
		add_action( 'load-plugins.php', array($this, 'check_for_update' ));
		add_action( 'load-update.php', array($this, 'check_for_update' ));
		add_action( 'load-update-core.php', array($this, 'check_for_update' ));
		
		add_action( 'admin_init', array($this, 'maybe_check_for_update' ));
		
		add_action( 'init', array($this, 'set_update' ));
	
	}

	function get( $key ) {
		
		if( !isset($this->settings[$key]) ) return null;
		
		else return $this->settings[$key];
		
	}
	
	
	/**
	 * Admin Init
	 */
	public function admin_init() {

		// enqueue needed scripts
		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
		
		// link to register unregister your plugin
		if(is_plugin_active( $this->plugin_file ) )
		add_filter( "plugin_action_links_$this->plugin_file", array($this, 'plugin_action_links'), 10, 4 );
		 
		// ajax action to show plugin registration form
		add_action('wp_ajax_' . self::PLUGIN . '_register', array($this, 'ajax'));
	}

	/**
	 * Ajax endpoint
	 */
	public function ajax () {
		
		$messages = '';
		
		$do = !empty($_REQUEST['do']) ? $_REQUEST['do'] : null;
		
		if($do === 'list_hosts') {
			
			$this->list_hosts();
			die;
			
		}
		
		if($do === 'unregister') {
			
			$host = !empty($_REQUEST['host']) ? $_REQUEST['host'] : null;

			if(wp_verify_nonce(!empty($_REQUEST['my_nonce']) ? $_REQUEST['my_nonce']: '', 'unregister_host'))
				$messages = $this->host_unregister($host);
			
		}
		
		if($do === 'development') {
			
			if( !wp_verify_nonce($_REQUEST['dev_nonce'], 'enable_dev') ) return 0;
			
			$dev = isset($_REQUEST['dev']) && $_REQUEST['dev'] ? 1 : 0;
			
			$this->settings['dev'] = $dev;
			update_option(self::PLUGIN . '_registration_settings', $this->settings);
			
			die;
			
		}
		
		if(!$this->is_registered()) {
			if(!empty($_POST['do']) && $_POST['do'] =='register') {
				$this->register();
			}
			else {
				$this->registration_view();
			}
		}
		else{
			if(!empty($_POST['do']) && $_POST['do'] =='unregister') {
				$this->unregister();
			}
			else {
				$this->license_view($messages);
			}
		}
		die;
	}
	/********************************************************
	 * Registration management
	 ********************************************************/
	
	/**
	 * Enqueue needed scripts
	 * - jquery form in plugins page
	 *
	 * @param string $hook_suffix
	 */
	public function enqueue_scripts ($hook_suffix) {
		
		if($hook_suffix !== 'plugins.php') return;
			wp_enqueue_script('jquery-form');
	}
	
	/**
	 * Add a register/unregister row actions link for the plugin in the plugins list view
	 *
	 * @param array $actions
	 * @param string $plugin_file
	 * @param array $plugin_data
	 * @param string $context
	 * @return array The filtered row actions list
	 */
	public function plugin_action_links ($actions, $plugin_file, $plugin_data, $context) {
		
		if($this->is_registered()) 
			$actions['unregister'] = '<a href="'. admin_url('admin-ajax.php') .'?action=' . self::PLUGIN . '_register" class="thickbox" title="'.sprintf( __('Manager your %s registration', $this->textdomain), esc_attr($plugin_data['Name'])).'" >' . __('Manage registration', $this->textdomain) . '</a>';
		else 
			$actions['register'] = '<a href="'. admin_url('admin-ajax.php') .'?action=' . self::PLUGIN . '_register" class="thickbox" title="'.sprintf( __('%s registration', $this->textdomain), esc_attr($plugin_data['Name'])).'" >' . __('Register', $this->textdomain) . '</a>';
		
		return $actions;
	}

	
	
	/**
	 * Do the website registration
	 * Ajax requested
	 */
	public function register () {
		
		$errors = new WP_Error();
		
		if(!wp_verify_nonce(!empty($_POST['my_nonce']) ? $_POST['my_nonce']: '', 'register')) $errors->add('error', __('<strong>Error:</strong> Invalid nonce, please refresh the form', $this->textdomain) );
		
		$user_email 		= !empty($_POST['user_email']) && $_POST['user_email'] ? $_POST['user_email'] : '';
		$user_api_secret 	= !empty($_POST['user_api_secret']) && $_POST['user_api_secret'] ? $_POST['user_api_secret'] : '';
		$user_license 		= !empty($_POST['user_license']) && $_POST['user_license'] ? $_POST['user_license'] : '';
		
		
		
		if(!is_email($user_email)) 	$errors->add('error', __('<strong>Error:</strong> your email address is invalid', $this->textdomain) );
		if(!$user_api_secret) 		$errors->add('error', __('<strong>Error:</strong> please enter your Api Secret Key', $this->textdomain));
		if(!$user_license) 			$errors->add('error', __('<strong>Error:</strong> please enter your plugin license number', $this->textdomain));
		
		$error_maybe = $errors->get_error_messages('error');

		if( empty($error_maybe) ) {
			
			$this->settings['user_email'] = $user_email;
			$this->settings['user_api_secret'] = $user_api_secret;
			$this->settings['user_license'] = $user_license;
			
			update_option(self::PLUGIN . '_registration_settings', $this->settings);

			$response = $this->host_register();
			
			if( is_wp_error($response) ) $errors = $response;
			else {
				
				$this->settings['is_registered'] 	= true;
				$this->settings['registered_host'] 	= $this->get_host();
				update_option(self::PLUGIN . '_registration_settings', $this->settings);

				
				$msgs = (array)$response;
				$msg = '<p>'.implode('<br />', $msgs).'</p>';
				
			}
			
		}
		
		if($codes = $errors->get_error_codes()) {
			
			$msg = '<p>'.$this->parse_errors($errors).'</p>';
			echo json_encode(array('success'=>'0', 'message'=>$msg));
			die;
		}
		
		// no errors
		echo json_encode(array('success'=>'1', 'message'=>$msg));
		die;
		
	}
	
	/**
	 * Check that the website is registered
	 */
	public function is_registered () {
		
		if( $this->get('registered_host') === $this->get_host() ) return true;
		
		if( $this->get('registered_host') !== $this->get_host() ) {
			$this->unregister();
		}
		
		return false;
		
	}
	public function unregister () {
		$this->settings['is_registered'] 	= false;
		unset($this->settings['registered_host']);
		update_option(self::PLUGIN . '_registration_settings', $this->settings);
	}
	/********************************************************
	 * Licence management function
	 ********************************************************/
	private function list_hosts () {
		
		$response = $this->hosts_list();
		
		if(is_wp_error($response)) echo $this->parse_errors($response, true);
		
		else {
			if((int)$response->limit) {
				
				?>
				<p class="form-input-tip">
					Registred hosts <?php echo sizeof((array)$response->hosts) ?>/<?php echo $response->limit ?>
				</p>
				<?php
				
			}
			
			if(empty($response->hosts)) echo '<p class="description">' . __("No registred hosts", $this->textdomain) . "</p>";
			
			else {
			
				echo '<table width="100%" class="widefat">';
				echo '<thead><tr>';
				echo '<th>';
				_e('Host', $this->textdomain);
				echo '</th>';
				
				echo '<th>';
				_e('Registration date', $this->textdomain);
				echo '</th>';
				echo '<th>';
				echo '&nbsp;';
				echo '</th>';
				echo '</tr></thead>';
				echo '<tbody>';
				
				$alternate = ' class="alternate" ';
				
				foreach ($response->hosts as $host) {
					
					
					if($alternate) $alternate = '';
					else $alternate = ' class="alternate" ';
					
					echo '<tr'.$alternate.'>';
					echo '<td class="row-title">';
					echo esc_html($host->name);
					echo '</td>';
					echo '<td nowrap="nowrap">';
					echo date_i18n(__('l, F jS, Y'), $host->register_time);
					echo '</td>';
					
					$can_unregister = time() > ($host->register_time+2592000) ? true : false;
					$can_unregister = true;
					echo '<td align="right" class="row-actions-visible">';
					
					if($can_unregister)
						echo '<a href="#" class="edit" onclick="unregister_host(this, \''.esc_js($host->name).'\');">'. __('Unregister', $this->textdomain) . '</a>';
					else echo '-';
					
					echo '</td>';
					echo '</tr>';				
				}
				echo '</tbody>';
				echo '</table>';
				echo '<p class="description" >';
				echo _e('You can unregister any host every 30 days.', $this->textdomain);
				echo '</p>';
			}
		}
		
	}
	
	
	/********************************************************
	 * Plugin update 
	 ********************************************************/
		
	/**
	 * Show the plugin information
	 */
	function plugins_api($false, $action, $args) {
		
		if($action !== "plugin_information" || empty($args->slug) || $args->slug !== self::PLUGIN ) return false;
		
		$infos = $this->get_plugin_infos();
		
		if(is_object($infos->sections)) $infos->sections = $d = get_object_vars($infos->sections);
		
		
		
		if($infos) return $infos;
		
	}
	
	/**
	 * Checks for plugin update
	 */
	
	function maybe_check_for_update() {
		
		$infos = get_transient(self::PLUGIN . '_update_infos');
		if ( isset( $infos->last_check ) && 43200 > ( time() - $infos->last_check )) return;
		
		$this->check_for_update();
	}
	
	function check_for_update() {

		if($this->update_checked) return;
		if(!$this->is_registered()) return;
		
		$infos = $this->get_plugin_update_infos();

		if( $infos && $this->is_registered()) {

			delete_transient(self::PLUGIN . '_update_infos');
			
			$transient = new stdClass();
			$transient->last_check = time();
			$transient->plugin = $infos;
			
			set_transient(self::PLUGIN . '_update_infos', $transient);
			
		}
		
		$this->update_checked = true;
		$this->set_update();
	}
	
	function set_update () {

		if(!is_admin())return;
		
		// Check for WordPress 3.0 function
		if ( function_exists( 'is_super_admin' ) ) {
			$options = get_site_transient( "update_plugins" );
		} else {
			$options = function_exists( 'get_transient' ) ? get_transient("update_plugins") : get_option("update_plugins");
		}
		if(!$options || empty($options)) return;
		
		$do_update = true;
				
		$infos 	= get_transient(self::PLUGIN . '_update_infos');
		
		$plugin 	 = empty($infos->plugin) ? new stdClass() : $infos->plugin;
		$new_version = empty($plugin->new_version) ? null : $plugin->new_version;	
		
		if(!$plugin || !$new_version) $do_update = false;
		elseif(!isset($options->checked) || !isset($options->checked[$this->plugin_file])) $do_update = false;
		else {
			$current_version = $options->checked[$this->plugin_file];
			if(version_compare($current_version, $plugin->new_version) === 1) $do_update = false;
		}		
		
		if( $this->is_registered() && $do_update) {
			$plugin_options = isset($options->response[ $this->plugin_file ]) ? $options->response[ $this->plugin_file ] : null;
			
			if( empty( $plugin_options ) ) {
				$options->response[ $this->plugin_file ] = new stdClass();
			}
			$options->response[ $this->plugin_file ]->id = 0;
			$options->response[ $this->plugin_file ]->slug = $plugin->slug;
			$options->response[ $this->plugin_file ]->new_version = $new_version;
			$options->response[ $this->plugin_file ]->url = $plugin->url;
			$options->response[ $this->plugin_file ]->package = $plugin->package;
			$options->response[ $this->plugin_file ]->infos = $plugin->content;
		}
		else if (isset($options->response[ $this->plugin_file ])) unset($options->response[ $this->plugin_file ]);
		
		// WordPress 3.0 changed some stuff, so we check for a WP 3.0 function
		if ( function_exists( 'is_super_admin' ) ) {
			$this->transient_set = true;
			set_site_transient( 'update_plugins', $options );
		} else {
			if ( function_exists( 'set_transient' ) ) {
				$this->transient_set = true;
				set_transient( 'update_plugins', $options );
			}
		}
		
	}
	
	/********************************************************
	 * API methods
	 ********************************************************/
	
	/**
	 * Do an api request
	 *
	 * @param string $action
	 * @param string $command
	 * @param array $parameters
	 * @param bool $do_auth
	 */
	function request($action, $command, $parameters, $do_auth = true) {
		
		// auth requested for this action
		if($do_auth) {
			
			// no token available, get one
			if( !$token = $this->get_token() ) {
			
				if( $this->multi_auth() === true ) {
					$token = $this->get_token();
				}
				
			}
			
			if(!$token) return new WP_Error('api_error', __("Could not authenticate. Check your informations.", $this->textdomain));

			$parameters['token'] = md5($token . $this->get('user_api_secret') );

		}

		$parameter['l'] = get_bloginfo('language');
		$parameters = http_build_query($parameters);
		
		$url 	= str_replace(array('$1', '$2'), array($action, $command) , self::API_URL);
			
		$options = array( 'method' => 'POST', 'timeout' => self::TIMEOUT, 'body' => $parameters );
		$options['headers'] = array('Connection' => 'close',
        							'Content-Type' => 'application/x-www-form-urlencoded; charset=' . get_option('blog_charset'),
							        'Content-Length' => strlen( $parameters ),
							        'User-Agent' => 'WordPress/' . get_bloginfo("version") . '/' . self::PLUGIN,
							        'Referer' => get_bloginfo("url")
								    );
			
		$response = wp_remote_request( $url, $options );
		
		if ( !is_wp_error( $response ) ) {

			if ( $response['response']['code'] == 200 ) {
				
				$result = json_decode($response['body']);
				
				if(!$result) return new WP_Error('api_error', __('Invalid response from server.', $this->textdomain), $response['body']);
				
				$result->success = (int)$result->success;
				
				if($result->unregister === '1') $this->unregister();
				
				return $result;
			}
			else {
				return new WP_Error('api_error', __('Invalid response code from server:', $this->textdomain) .' '. $response['response']['code']);
			}

		} else return $response;
			
	}
	
	/**
	 * Get an authentification nonce from the license server
	 * @return string|WP_Error the nonce as a string or a WP_Error object on error
	 */
	function get_nonce () {
			
		$parameters = array ( 'license' => $this->get('user_license') );
		$response = $this->request('auth', 'nonce', $parameters, false);

		if(is_wp_error($response)) return $response;

		if($response->success==1 && $response->nonce) return $response->nonce;
		
		// only reason to get a success=0 is an invalid license
		if($response->success===0) {
			$this->unregister();
		}		
		
		return new WP_Error('api_error', __("Can't get nonce from server", $this->textdomain));
		
	}

	/**
	 * Gets anauthentification token
	 * Enter description here ...
	 */
	function authenticate() {

		// first get a nonce
		$nonce = $this->get_nonce();
		
		if(!$nonce) return false;
		
		$auth_string = md5(ceil(time()/3600) . $this->get('user_email') . 
					   md5( $this->get('user_api_secret') ). 
					   $this->get('user_license') ) . $this->get('user_license');

		
		$parameters = array ( 'auth' => $auth_string,
							  'nonce'=> $nonce );

		$response = $this->request('auth', 'auth', $parameters, false);
		
		if(is_wp_error($response)) return $response;
		if(!$response->success || !$response->token) return new WP_Error('server_error', $response->message);
		
		$token = $response->token;
		$token->time_diff = $token->time - time();
		
		$this->settings['token'] = $token;
		
		update_option(self::PLUGIN . '_registration_settings', $this->settings);
		return true;
			
	}
	
	/**
	 * Try multiple authentifications requests
	 */
	function multi_auth () {
			
		$sucess = false;
		$tries 	= 1;
			
		while(true) {

			$success = $this->authenticate();

			if($success === true) {
				break;
			}

			elseif( $tries >= self::AUTH_TRIES) {
				break;
			}

			$tries++;

		}
		return $success;
			
	}

	/**
	 * Return the token as a string if it is valid
	 * @return bool|string the token or false
	 */
	function get_token () {
		
		if (! $this->get('token') ) return false;
		$token = $this->settings['token'];
		
		if(empty($token->expires) || empty($token->token)) return false;
		
		if($token->expires < time() ) return false;
		
		return $token->token;
		
		
	}
	
	/**
	 * Register current host
	 */
	function host_register () {
		
		$parameters = array ( 'host' => $this->get_host(), 
							  'license' => $this->settings['user_license'],
							  'version' => $this->plugin_version );
			
		$response = $this->request('host', 'register', $parameters);

		if(is_wp_error($response)) return $response;
		if(!$response->success) return new WP_Error('server_error', $response->message);
		
		return $response->message;
	}
	
	/**
	 * Host unregister
	 */
	function host_unregister ($host) {
		
		$parameters = array ( 'host' => $host, 
							  'license' => $this->settings['user_license'] );
			
		$response = $this->request('host', 'unregister', $parameters);


		if(!$response->success) return new WP_Error('server_error', $response->message);
		
		
		if($host === $this->get_host()) {
			
			$this->settings['is_registered'] 	= false;
			unset($this->settings['registered_host']);
			update_option(self::PLUGIN . '_registration_settings', $this->settings);
			
		}
		
		return $response->message;
	}
	
	/**
	 * Host list
	 */
	function hosts_list () {
		
		$parameters = array ( 'license' => $this->settings['user_license'] );
			
		$response = $this->request('hosts', 'list', $parameters);

		if(is_wp_error($response)) return $response;
		if(!$response->success) return new WP_Error('server_error', $response->message);
		
		return $response;
	}
	
	function get_plugin_update_infos () {

		$parameters = array ( 'host' => $this->get_host(),
							  'version' => $this->plugin_version,
							  'license' => $this->settings['user_license'] );
		
		$parameters['beta'] = !empty($this->settings['dev']) && $this->settings['dev'] ? 1 : 0;
		
		$response = $this->request('plugin', 'updateInfos', $parameters);
		
		if(is_wp_error($response)) return $response;
		if(!$response->success) return new WP_Error('server_error', $response->message);
		
		return $response->infos;
		
	}
	
	function get_plugin_infos () {

		$parameters = array ( 'host' => $this->get_host(),
							  'license' => $this->settings['user_license'] );
		$parameters['beta'] = !empty($this->settings['dev']) && $this->settings['dev'] ? 1 : 0;
		
		$response = $this->request('plugin', 'infos', $parameters);
		
		if(is_wp_error($response)) return $response;
		if(!$response->success) return new WP_Error('server_error', $response->message);
		
		return $response->infos;
		
	}

	function get_host () {
		$host = $_SERVER['HTTP_HOST'];
		
		$host = explode(':', $host);
		
		return $host[0];
		
	}
	
	function parse_errors ($wp_errors, $enclose=false) {
		
		$errors = '';
		if(is_wp_error($wp_errors)) {
			$errors = array();
			foreach ($wp_errors->get_error_codes() as $code) {
				
				foreach( $wp_errors->get_error_messages($code) as $message) {
					$errors[] = $message;
				}
				
			}
			$errors = implode('<br />', $errors);
			$messages = '';
		}
		elseif (!empty($wp_errors)) {
			$messages = implode('<br />', (array)$messages);
		}
		
		if(!empty($errors) && $enclose) {
			return '<div class="error"><p>' . $errors . '</p></div>';	
		} else return $errors;
		if(!empty($wp_errors) && $enclose) {
			return '<div class="updated"><p>' . $messages . '</p></div>';	
		} else return $messages;
		
		return '';
		
	}
	
	/********************************************************
	 * Views
	 ********************************************************/

	function registration_view () {
		
		?>
		<div class="wrap register_plugin">
		<h2><?php _e('Register', $this->textdomain) ?> <?php echo self::PLUGIN_NAME ?></h2>
		
		<p><?php printf(__('By registering our plugin you get access to free updates and support. If you need to buy a license <a href="">click Here</a>', $this->textdomain), self::PLUGIN_URL) ?></p>
		<form id="registration_form" action="<?php echo admin_url('admin-ajax.php') ?>?action=<?php echo self::PLUGIN . '_register' ?>" method="post">
		
		<div id="registration_form_response"></div>
		
		<p><?php  _e('You are about to register the host', $this->textdomain) ?> <em><?php echo $this->get_host() ?></em>.</p>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><label for="user_email"><?php _e('Your email', $this->textdomain) ?>:</label></th>
					<td><input type="text" name="user_email" id="user_email" value="<?php echo esc_attr($this->settings['user_email']) ?>" /></td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><label for="user_api_secret"><?php _e('Your API Secret Key', $this->textdomain) ?>:</label></th>
					<td><input type="text" name="user_api_secret" id="user_api_secret" value="<?php echo esc_attr($this->settings['user_api_secret']) ?>" /></td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><label for="user_license"><?php _e('Your Licence number', $this->textdomain) ?>:</label></th>
					<td><input type="text" name="user_license" id="user_license" value="<?php echo esc_attr($this->settings['user_license']) ?>" /></td>
				</tr>
				
			</tbody>
		</table>

		<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Register', $this->textdomain) ?>" /></p>
		<input type="hidden" name="do" value="register" />
		<input type="hidden" name="action" value="<?php echo self::PLUGIN . '_register' ?>" />
		<?php wp_nonce_field('register', 'my_nonce') ?>
		</form>
		
		<script type="text/javascript">
		<!--
		jQuery(document).ready( function ($) {

			$('#registration_form').ajaxForm({dataType: 'json', success: process_response, beforeSubmit: before_submit});


			function before_submit (arr, $form, options) { 
				$('#registration_form_response').removeClass('updated error').addClass('updated');
				$('#registration_form_response').html('<p><?php _e('Registering...', $this->textdomain) ?></p>');
			}
			
			function process_response(response) {

				$('#registration_form_response').html(response.message);
				if(response.success == '1') {
					$('#registration_form_response').removeClass('updated error').addClass('updated');
					tb_show('manage', '<?php echo admin_url('admin-ajax.php') ?>?action=<?php echo self::PLUGIN . '_register' ?>');
					}
				else $('#registration_form_response').removeClass('updated error').addClass('error');
			}
			
		});
		//-->
		</script>
		</div>
		<?php
		
	}
	
	function license_view($messages) {
		
		$dev_nonce = wp_create_nonce("enable_dev");
		$dev_enabled = !empty($this->settings['dev']) && $this->settings['dev'] ? 1 : 0;
		
		?>
		<div class="wrap">
		<h2>Your <?php echo self::PLUGIN_NAME ?> license</h2>
		<?php 

		if($message = $this->parse_errors($messages, true)) echo $message;
		
		?>
		<p class="form-input-tip">
		<strong>Licence number:</strong> <?php echo $this->settings['user_license'] ?>
		</p>
		<div style="float: right">
		<label><input type="checkbox" name="enable_dev" id="enable_dev" value="1" <?php checked('1', $dev_enabled); ?> /> Enable development versions</label>
		</div>
		<h3>Registred hosts</h3>
		<div id="registred_host">
					
		</div>
		<script type="text/javascript">
		<!--
		jQuery(document).ready( function ($) {

			function update_hosts_list () {

				$('#registred_host').html('<div class="loading description">Loading hosts list...</div>');
				$('#registred_host').load ('<?php echo admin_url('admin-ajax.php?action='.self::PLUGIN . '_register&do=list_hosts')?>');
			}
			update_hosts_list();

			$('#enable_dev').click(enable_dev);

			function enable_dev() {
				var dev = 0;
				if($(this).is(':checked') ) dev=1;

				$.ajax({ url: '<?php echo admin_url('admin-ajax.php?action=' . self::PLUGIN . '_register&do=development&dev_nonce='.$dev_nonce) ?>&dev='+dev });
			}

			
		});

		function unregister_host (a, host) {

			tb_show('manage', '<?php echo admin_url('admin-ajax.php') ?>?do=unregister&action=<?php echo self::PLUGIN . '_register&my_nonce='.wp_create_nonce('unregister_host') ?>&width='+(TB_WIDTH-30)+'&host='+host);
			
		}
		//-->
		</script>
		</div>
		<?php 
	}
}




