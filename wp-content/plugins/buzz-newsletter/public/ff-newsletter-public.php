<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.fireflyinteractive.net
 * @since      3.0.0
 *
 * @package    ff_newsletter
 * @subpackage ff_newsletter/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    ff_newsletter
 * @subpackage ff_newsletter/public
 * @author     Firefly Interactive
 */
class FF_Newsletter_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var      string    $ff_newsletter    The ID of this plugin.
	 */
	private $ff_newsletter;

	/**
	 * The version of this plugin.
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    3.0.0
	 * @param    string    $ff_newsletter   The name of the plugin.
	 * @param    string    $version    		The version of this plugin.
	 */
	public function __construct( $ff_newsletter, $version ) {

		$this->ff_newsletter = $ff_newsletter;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    3.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in FF_Newsletter_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The FF_Newsletter_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->ff_newsletter, plugin_dir_url( __FILE__ ) . 'css/ff-newsletter-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    3.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in FF_Newsletter_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The FF_Newsletter_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->ff_newsletter, plugin_dir_url( __FILE__ ) . 'js/ff-newsletter-public.js', array( 'jquery' ), $this->version, false );

	}

	///////////////////////////////////////////////////////////////////////////////////////
	// FLAG UNPUBLISH
	///////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Rewrite the title tag to include a flag showing unpublished status
	 * This is called on init() because we need current_user_can method to be available
	 */
	public function flag_unpublished_articles( ) {

		if( !is_admin() && current_user_can( 'edit_others_posts')) {
			add_filter( 'the_title', array($this, '_flag_unpublished_articles'), 10, 2 );
		}
	}

	/**
	 * Callback for the_title
	 */
	public function _flag_unpublished_articles( $title, $id=null ) {
		$post = get_post($id);

		if( $post && $post->post_type == 'article' && $post->post_status != 'publish' ) {
			$title .= ' <small class="post-status">(' . $post->post_status . ')</small>';
		}

		return $title;
	}

	///////////////////////////////////////////////////////////////////////////////////////
	// URL REWRITES
	///////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Add custom query vars
	 *
	 * @since    3.0.0
	 */
	public function add_url_vars($public_query_vars) {
		array_push($public_query_vars, 'issue' );
	    return $public_query_vars;
	}

	/**
	 * Add rewrite rules to redirect query vars to different views
	 *
	 * @since    3.0.0
	 */
	public function add_rewrite_rules() {

		// latest
	    add_rewrite_rule('newsletter/?$', 'index.php?newsletter=latest','top');

	}

	/**
	 * Redirect base URL and "/newsletter" to latest newsletter
	 *
	 * @since    3.0.0
	 */
	public function redirect_to_latest() {
		global $wp_query;

		$query_vars = $wp_query->query_vars ?? [];

		if(array_key_exists('newsletter', $query_vars)
			&& $wp_query->query_vars['newsletter'] == 'latest') {

			$latest  = FF_Newsletter_Common::get_latest_newsletter();
			$newsUrl = FF_Newsletter_Common::get_newsletter_url( $latest );
			wp_redirect( $newsUrl );
			exit;
		}
	}

	///////////////////////////////////////////////////////////////////////////////////////
	// QUERY / TAXONOMY REWRITES
	///////////////////////////////////////////////////////////////////////////////////////

	/**
	 *
	 */
	public function alter_taxonomy_query( $query ) {
		// gets the global query var object
		global $wp_query;

		// get query vars
		$is_taxonomy_query = isset($wp_query->query_vars['issue']);

		// set the query if issue is set
		if($is_taxonomy_query) {
			$newsletter = get_page_by_path( $wp_query->query_vars['issue'], 'OBJECT', 'newsletter' );
			if($newsletter) {
				$query->set('post_type', 'article');
				$query->set('meta_key', 'ff_parent_id' );
				$query->set('meta_value', $newsletter->ID );
				//$query->set('p' , null);
				//$query->set( 'page_id' ,null);
			}
		}
	}

	///////////////////////////////////////////////////////////////////////////////////////
	// MENUS
	///////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Register the menu areas to be used by the theme
	 * TODO: Remove this function once all clients have been transferred to new theme
	 */
	function register_menus() {
		// if nav menu primary does not exist, that means they're on the old theme and menu must be registered
		if( !has_nav_menu( 'primary' ) ) {
			register_nav_menu( 'navbar-menu', __( 'Navbar Menu' ) );
		}
	}

} // end class
