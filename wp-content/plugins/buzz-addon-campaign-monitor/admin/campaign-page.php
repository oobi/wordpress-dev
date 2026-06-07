<?php

/**
 *
 */
class Buzz_Newsletter_Campaign_Monitor_Campaign_Page
{

	// subject tokens
	public static $subject_tokens = array('%site-title%', '%site-description%', '%newsletter-title%', '%year%', '%month%', '%day%');
	public static $default_subject = '%newsletter-title%';

	/**
	 * Load custom js to handle ajax queries
	 */
	public function enqueue_custom_scripts() {
		$plugin_base = plugin_dir_url( dirname( __FILE__ ) );
		$script_version = '1.0';
		wp_enqueue_script('campaign_monitor_js', ( $plugin_base . "/admin/js/campaign.js"), array('jquery'), $script_version, true);
	}
	/**
	 * Add an admin page for managing newsletter campaigns
	 */
	public function add_admin_pages() {
		add_submenu_page( 'edit.php?post_type=newsletter',
						  'Campaign Monitor',
						  'Campaign Monitor',
						  'edit_pages',		// Editor permission level https://wordpress.org/documentation/article/roles-and-capabilities/#editor
						  'buzz-campaign-monitor',
						  array($this, 'ff_newsletter_campaign_management_page') );
		//add_options_page( 'Newsletter Options', 'Newsletter Options', 'manage_options', 'newsletter_options');
	}


	/**
	 * Callback for campaign manegement admin page
	 */
	public function ff_newsletter_campaign_management_page() {

		// get api key and plugin defaults
		$cm_settings 		= get_option( 'buzz_campaignmonitor_settings' );
		$api_key			= $cm_settings['campaignmonitor_api_key'];
		$api_key_is_invalid = $cm_settings['campaignmonitor_api_key_valid'] == 'false' ? TRUE : FALSE;
		$default_from_name 	= $cm_settings['campaignmonitor_from_name'];
		$default_from_email	= $cm_settings['campaignmonitor_from_email'];
		$default_reply_email= $cm_settings['campaignmonitor_reply_email'];

		// set up Firefly Mailer class
		$ffm = new Campaign_Monitor_Mailer_API();
		?>

		<div class="wrap">
			<h2>Email Campaign Management</h2>

			<?php // check if API key is set, throw error if not
			if( !isset( $api_key ) || empty( $api_key ) ) : ?>
				<div id="api-key-not-set" class="error">
					<h2>API Key not set</h2>
					<p>The <b>API Key</b> must be set before you can manage your Email Campaigns. Set the <b>API Key</b> on the
						<a href="<?php echo get_admin_url( get_current_blog_id(), '/options-general.php?page=campaignmonitor_settings' ) ?>">Campaign Monitor Settings</a> page before continuing.</p>
				</div>
				<?php return;
			elseif( $api_key_is_invalid ) : ?>
				<div id="api-key-invalid" class="error">
					<h2>API Key is invalid</h2>
					<p>The provided <b>API Key</b> is invalid. Check the <b>API Key</b> on the
						<a href="<?php echo get_admin_url( get_current_blog_id(), '/options-general.php?page=campaignmonitor_settings' ) ?>">Campaign Monitor Settings</a> page before continuing.</p>
				</div>
				<?php return;
			endif;

			// get the current tab
			$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'current'; ?>

			<!-- page tabs -->
			<h2 class="nav-tab-wrapper">
				<a href="?post_type=newsletter&page=buzz-campaign-monitor&tab=current" class="nav-tab <?php echo $active_tab == 'current' ? 'nav-tab-active' : ''; ?>">Current Campaigns</a>
				<a href="?post_type=newsletter&page=buzz-campaign-monitor&tab=create" class="nav-tab <?php echo $active_tab == 'create' ? 'nav-tab-active' : ''; ?>">Create a Campaign</a>
			</h2>

			<div class="tab-content" id="tab-<?php echo $active_tab; ?>">

			<?php
				// Show tab content depending on which is active
				switch( $active_tab ) {
					case 'create' :
						include('partials/tab-create.php'); break;
					case 'current' :
						include('partials/tab-current.php'); break;
					case 'send' :
						include('partials/tab-send.php'); break;
					case 'delete' :
						include('partials/tab-delete.php'); break;
					case 'update' :
						include('partials/tab-update.php'); break;
					case 'stats' :
						include('partials/tab-stats.php'); break;
					default :
						// show nothing
				}
			?>
			</div>

		</div><!-- /.wrap -->

		<?php

	}

	/**
	 * Resolve subject from tokens
	 */
	public function build_subject( $newsletter_id, $subject ) {
		$newsletter = get_post($newsletter_id);

		// regex tokens and replacements
		$replacements = array(
			get_bloginfo('name'),
			get_bloginfo('description'),
			get_the_title($newsletter),
			get_the_date('Y', $newsletter_id),
			get_the_date('F', $newsletter_id),
			get_the_date('j', $newsletter_id)
		);

		$result = str_replace( self::$subject_tokens, $replacements, $subject);

		return $result;
	}
}
