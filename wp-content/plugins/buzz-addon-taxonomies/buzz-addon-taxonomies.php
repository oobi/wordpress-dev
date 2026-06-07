<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.fireflyinteractive.net
 * @since             1.0.0
 * @package           Buzz_Addon_Taxonomies
 *
 * @wordpress-plugin
 * Plugin Name:       Buzz Add-On - Taxonomies
 * Plugin URI:        http://www.thebuzz.net.au/add-ons/#taxonomies
 * Description:       Adds Taxonomies to the Buzz Newsletter
 * Version:           2.3
 * Author:            Firefly Interactive
 * Author URI:        http://www.fi.net.au
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       buzz-taxonomies
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Set up constant (for checking in theme)
define( 'BUZZ_ADDON_TAXONOMIES', TRUE );

class Buzz_Addon_Taxonomies {

	public static $category = 'article_category';
	public static $tag      = 'article_tag';
	public static $order    = 'tax_position';

	public function plugin_init() {
		// add Wordpress actions
		add_action( 'init', 					array( $this, 'add_custom_taxonomy' ), 1 );
		add_action( 'init', 					array( $this, 'add_rewrite_rules' ), 10 );
		add_action( 'init', 					array( $this, 'frontend_init' ), 10 );
		add_action( 'restrict_manage_posts', 	array( $this, 'add_taxonomy_filters' ), 10 );
		add_action( 'admin_enqueue_scripts', 	array( $this, 'enqueue_admin_scripts' ), 20); // this must happen AFTER buzz scripts load
		add_action( 'admin_head', 				array( $this, 'admin_init' ), 10);
		add_filter( 'get_user_option_meta-box-order_newsletter', array( $this, 'override_newsletter_meta_box_order'), 10);

		// restrict taxonomy queries to specific issue if defined
		add_action( 'pre_get_posts', array($this,'filter_posts_by_issue') );


		// order terms in category list view
		add_action( 'current_screen', 			array( $this, 'admin_order_terms' ) );

		// Add custom Buzz actions
		add_action( 'buzz_after_custom_meta_box', 					array( $this, 'manage_custom_metaboxes' ), 5, 2 );
		add_filter( 'buzz_newsletter_uncategorised_empty_prompt', 	array( $this, 'change_uncategorised_empty_prompt'), 10, 1);
		add_filter( 'buzz_newsletter_child_meta_box_title', 		array( $this, 'change_uncategorised_article_meta_title' ), 10, 1 );
		add_filter( 'buzz_newsletter_child_meta_box_query_args', 	array( $this, 'change_uncategorised_article_meta_query' ), 10, 2 );

		// ajax actions
		add_action( 'wp_ajax_update-article-categories', array( $this, 'update_article_category'), 10 );
		add_action( 'wp_ajax_update_taxonomy_order', array( $this, 'update_article_category_order' ), 10 );
	}

	/**************************************************************************
	 * STATIC METHODS
	 **************************************************************************/

	public static function get_terms( $taxonomy, $args=array()) {
		$args = array_merge( array(
			'taxonomy' 		=> $taxonomy,
			'hide_empty'	=> true
		), $args );


		return get_terms( $args );
	}

	public static function get_categories( $hide_empty=true, $args=array() ) {
		$args['hide_empty'] = $hide_empty;
		return self::get_terms(self::$category, $args);
	}

	public static function get_tags( $hide_empty=true, $args=array() ) {
		$args['hide_empty'] = $hide_empty;
		return self::get_terms(self::$tag, $args);
	}

	/**************************************************************************
	 * INIT
	 **************************************************************************/

	/**
	 * The code that runs during plugin activation.
	 */
	public function activate() {
		// make sure the rewrite rules have been added
		$this->add_rewrite_rules();

		// flush rules
		flush_rewrite_rules();
	}

	/**
	 * The code that runs during plugin deactivation.
	 */
	public function deactivate() {
		global $wp_rewrite;

		// make sure the rewrite rules have been removed
   		unset($wp_rewrite->extra_rules_top[$this->rewrite_rule]);

		// flush rules
		flush_rewrite_rules();
	}

	/**
	 * Initialise the admin
	 */
	public function admin_init() {
		$screen = get_current_screen();

		// add category order js only to article_category edit screen
		if( isset( $screen ) && isset( $screen->base ) && isset( $screen->taxonomy ) ) {
			if( $screen->base == 'edit-tags' && $screen->taxonomy == self::$category ) {
				$this->ensure_term_has_tax_position( self::$category );

				// re-order the categories
				add_filter( 'terms_clauses', array( $this, 'alter_category_order' ), 10, 3 );

			} elseif( preg_match( '/newsletter|article/', $screen->post_type ) ) {
				// re-order the categories
				add_filter( 'terms_clauses', array( $this, 'alter_category_order' ), 10, 3 );
			}
		}

	}

	/**
	 * Order the terms on the admin side.
	 */
	public function admin_order_terms( WP_Screen $screen ) {
		if ( empty( $_GET['orderby'] ) && 'edit-tags' === $screen->base && $screen->taxonomy == self::$category ) {
			$this->ensure_term_has_tax_position( self::$category );
			add_filter( 'terms_clauses', array( $this, 'alter_category_order' ), 10, 3 );
		}
	}

	/**
	 * Initialise front end
	 */
	public function frontend_init() {
		if( ! is_admin() ) {
			// re-order the categories
			add_filter( 'terms_clauses', array( $this, 'alter_category_order' ), 10, 3 );
		}
	}

	/**
	 * Enqueue custom JavaScript
	 */
	public function enqueue_admin_scripts() {
		// Only use this JS on Edit Article pages
		$screen = get_current_screen();

		// newsletter screen needs jquery-ui for drag/drop
		if( isset( $screen ) && preg_match( '/newsletter|article/', $screen->post_type ) ) {
			$prerequisites = array('jquery', 'jquery-ui-sortable');
			wp_enqueue_script( 'buzz-taxonomies-admin', plugin_dir_url( __FILE__ ) . 'js/admin.js', $prerequisites, '1.2', true );
		}

		// add category order js only to article_category edit screen
		if( isset( $screen ) && isset( $screen->base ) && isset( $screen->taxonomy ) ) {
			if( $screen->base == 'edit-tags' && $screen->taxonomy == self::$category ) {

				// category order CSS
				wp_enqueue_style( 'buzz-taxonomies-category-order-css', plugin_dir_url( __FILE__ ) . 'css/category-order.css', array(), '1.0' );

				// category order JS
				$prerequisites = array('jquery', 'jquery-ui-sortable');
				wp_enqueue_script( 'buzz-taxonomies-category-order-js', plugin_dir_url( __FILE__ ) . 'js/category-order.js', $prerequisites, '1.0', true );
				wp_localize_script( 'buzz-taxonomies-category-order-js', 'buzz_taxonomies', array(
					'ajax_url' => esc_url( admin_url( 'admin-ajax.php' ) ),
					'preloader_url' => esc_url( admin_url( 'images/wpspin_light.gif' ) ),
				) );

			}
		}
	}

	/*
	* Make sure the passed taxonomy has a tax_position set in term meta. If not, add one
	*/
	public function ensure_term_has_tax_position( $taxonomy=null ) {
		if( isset( $taxonomy ) ) {
			$terms = get_terms( $taxonomy, array( 'hide_empty' => false ) );
			$x = 1;
			foreach( $terms as $term ) {
				if( ! get_term_meta( $term->term_id, self::$order, true ) ) {
					update_term_meta( $term->term_id, self::$order, $x );
					$x++;
				}
			}
		}
	}

	/**************************************************************************
	 * SET UP TAXONOMY FUNCTIONALITY
	 **************************************************************************/

	 /**
	 * Add rewrite rules to extend category functionality
	 */
	public function add_rewrite_rules() {
		// article categories limited to issue with pagination
		add_rewrite_rule(sprintf('%s/(.+?)/issue/(.+?)/page/?([0-9]{1,})/?$', self::$category),
						 sprintf('index.php?%s=$matches[1]&issue=$matches[2]&paged=$matches[3]', self::$category),
						 'top');

		// article categories limited to issue
		add_rewrite_rule(sprintf('%s/(.+?)/issue/(.+?)/?$', self::$category),
						 sprintf('index.php?%s=$matches[1]&issue=$matches[2]', self::$category),
						 'top');

		// article tags limited to issue with pagination
		add_rewrite_rule(sprintf('%s/(.+?)/issue/(.+?)/page/?([0-9]{1,})/?$', self::$tag),
						 sprintf('index.php?%s=$matches[1]&issue=$matches[2]&paged=$matches[3]', self::$tag),
						 'top');

		// article tags limited to issue
		add_rewrite_rule(sprintf('%s/(.+?)/issue/(.+?)/?$', self::$tag),
						 sprintf('index.php?%s=$matches[1]&issue=$matches[2]', self::$tag),
						 'top');
	}

	/**
	 * Create the custom taxonomies
	 */
	public function add_custom_taxonomy() {

		// Article Categories
	    $labels = array(
	        'name'                       => _x( 'Categories', 'Taxonomy General Name', 'ff_newsletter' ),
	        'singular_name'              => _x( 'Category', 'Taxonomy Singular Name', 'ff_newsletter' ),
	        'menu_name'                  => __( 'Categories', 'ff_newsletter' ),
	        'all_items'                  => __( 'All Categories', 'ff_newsletter' ),
	        'parent_item'                => __( 'Parent Category', 'ff_newsletter' ),
	        'parent_item_colon'          => __( 'Parent Category:', 'ff_newsletter' ),
	        'new_item_name'              => __( 'New Category', 'ff_newsletter' ),
	        'add_new_item'               => __( 'Add Category', 'ff_newsletter' ),
	        'edit_item'                  => __( 'Edit Category', 'ff_newsletter' ),
	        'update_item'                => __( 'Update Category', 'ff_newsletter' ),
	        'view_item'                  => __( 'View Category', 'ff_newsletter' ),
	        'separate_items_with_commas' => __( 'Separate items with commas', 'ff_newsletter' ),
	        'add_or_remove_items'        => __( 'Add or remove items', 'ff_newsletter' ),
	        'choose_from_most_used'      => __( 'Choose from the most used', 'ff_newsletter' ),
	        'popular_items'              => __( 'Popular Items', 'ff_newsletter' ),
	        'search_items'               => __( 'Search Items', 'ff_newsletter' ),
	        'not_found'                  => __( 'Not Found', 'ff_newsletter' ),
	    );
	    $args = array(
	        'labels'                     => $labels,
	        'hierarchical'               => true,
	        'public'                     => true,
	        'show_ui'                    => true,
	        'show_admin_column'          => true,
	        'show_in_nav_menus'          => true,
	        'show_tagcloud'              => true,
			'show_in_rest' 			     => true
	    );
	    register_taxonomy( self::$category, array( 'article' ), $args );

	    // Article Tags
		$labels = array(
	        'name'                       => _x( 'Tags', 'Taxonomy General Name', 'ff_newsletter' ),
	        'singular_name'              => _x( 'Tag', 'Taxonomy Singular Name', 'ff_newsletter' ),
	        'menu_name'                  => __( 'Tags', 'ff_newsletter' ),
	        'all_items'                  => __( 'All Tags', 'ff_newsletter' ),
	        'parent_item'                => __( 'Parent Tag', 'ff_newsletter' ),
	        'parent_item_colon'          => __( 'Parent Tag:', 'ff_newsletter' ),
	        'new_item_name'              => __( 'New Tag', 'ff_newsletter' ),
	        'add_new_item'               => __( 'Add Tag', 'ff_newsletter' ),
	        'edit_item'                  => __( 'Edit Tag', 'ff_newsletter' ),
	        'update_item'                => __( 'Update Tag', 'ff_newsletter' ),
	        'view_item'                  => __( 'View Tag', 'ff_newsletter' ),
	        'separate_items_with_commas' => __( 'Separate items with commas', 'ff_newsletter' ),
	        'add_or_remove_items'        => __( 'Add or remove items', 'ff_newsletter' ),
	        'choose_from_most_used'      => __( 'Choose from the most used', 'ff_newsletter' ),
	        'popular_items'              => __( 'Popular Items', 'ff_newsletter' ),
	        'search_items'               => __( 'Search Items', 'ff_newsletter' ),
	        'not_found'                  => __( 'Not Found', 'ff_newsletter' ),
	    );
	    $args = array(
	        'labels'                     => $labels,
	        'hierarchical'               => false,
	        'public'                     => true,
	        'show_ui'                    => true,
	        'show_admin_column'          => true,
	        'show_in_nav_menus'          => true,
	        'show_tagcloud'              => true,
			'show_in_rest' 				 => true
	    );
	    register_taxonomy( self::$tag, array( 'article' ), $args );

	}


	/**************************************************************************
	 * ADD TAXONOMIES TO ARTICLE LIST
	 **************************************************************************/

	/**
	 * Filter the request to just give posts for the given taxonomy, if applicable.
	 */
	public function add_taxonomy_filters() {
		global $typenow;

		// must set this to the post type you want the filter(s) displayed on
		switch($typenow) {

			// articles have category/tag taxonomies
			case 'article' :
				// an array of all the taxonomies you want to display. Use the taxonomy name or slug
				$filters = array(self::$category, self::$tag);
				$this->render_taxonomy_filters($filters);

				break;

			// other post types
			default :
				return;
		}
	}

	/**
	 * render a drop list of taxonomy filter terms
	 */
	public function render_taxonomy_filters( $filters ) {

		foreach ($filters as $tax_slug) {
			if( taxonomy_exists( $tax_slug ) ) {

				// retrieve the taxonomy object
				$tax_obj 		= get_taxonomy($tax_slug);
				$tax_name 		= $tax_obj->labels->name;

				// retrieve array of term objects per taxonomy
	    		$terms = get_terms($tax_slug);

				// get the current taxonomy term from the query to determine if we should select the item in the lsit
				$current_term 	=  array_key_exists($tax_slug, $_GET) ? $_GET[$tax_slug] : NULL;

				// output html for taxonomy dropdown filter
	            echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
	            echo "<option value=''>" . __("All $tax_name", 'ff_newsletter') . "</option>";
	            foreach ($terms as $term) {
	                // output each select option line, check against the last $_GET to show the current option selected
	                echo '<option value='. $term->slug, $term->slug == $current_term ? ' selected="selected"' : '','>' . $term->name .' (' . $term->count .')</option>';
	            }
	            echo "</select>";
			}
		}

	}

	/**************************************************************************
	 * ADD TAXONOMY META BOXES TO NEWSLETER VIEW
	 **************************************************************************/

	/**
	 * Callback - alter metabox title for uncategorised articles
	 */
	public function change_uncategorised_article_meta_title( $title ) {
		return 'Uncategorised Articles';
	}


	/**
	 * Callback - alter metabox title for uncategorised articles
	 */
	public function change_uncategorised_empty_prompt( $title ) {
		return 'There are no uncategorised articles';
	}



	/**
	 * Callback - alter metabox query for uncategorised articles
	 */
	public function change_uncategorised_article_meta_query( $args ) {
		$taxonomy = self::$category;
		$args['tax_query'] = array(
			array(
				'taxonomy' => $taxonomy,
				'terms'    => self::get_categories(false, ['fields'=>'ids']), // get_terms( $taxonomy, [ 'fields' => 'ids', 'hide_empty' => false  ] ),
				'operator' => 'NOT IN'
			)
		);

		return $args;
	}

	/**
	 * Add custom metaboxes
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	public function manage_custom_metaboxes() {
		$terms = self::get_categories(false);

		// Add custom newsletter meta box for each category term
		foreach( $terms as $term ) {
			add_meta_box(
				'newsletter-category-meta-box-' . $term->term_id,      	// Unique ID
				esc_html__( $term->name ), 								// Title
				array( $this, 'newsletter_category_meta_box' ), 			// Callback function - create the HTML for the meta box
				'newsletter',         									// Admin page (or post type)
				'normal',         										// Context
				'default',         										// Priority
				['term' => $term] 										// callback args
			);
		}
	}

	/**
	 * Generate contents of category metabox
	 */
	public function newsletter_category_meta_box( $object, $box ) {
		$term = $box['args']['term'];

		// Get child articles
		$args = array(
			'parent-id' 	=> get_the_ID(),
			'post_status'	=> 'any',
			'orderby'		=> 'menu_order',
			'order'			=> 'ASC',
			'tax_query' => array(
				array(
					'taxonomy' => self::$category,
					'field' => 'term_id',
					'terms' => [$term->term_id]
				)
			)
		);
		$children = FF_Newsletter_Common::get_articles($args);

		$ui = new FF_Newsletter_UI();

		// output current children
		echo "<ul data-category-id=\"{$term->term_id}\" class=\"buzz-article-sortable-list buzz-article-default\">";
			foreach($children as $child) {
				echo $ui->display_child_article($child, 'li');
			}
		echo "</ul>";

		printf('<i class="buzz-no-articles">%s</i>',
					'There are no articles in this category' );
	}


	/**
	 * Override user placement of metaboxes so we get articles in the right order
	 */
	public function override_newsletter_meta_box_order( $order ) {
		$terms = get_terms( array(
			'taxonomy' => self::$category,
    		'hide_empty' => false,
		) );

		// main articles meta box
		$order['normal'] = 'newsletter-child-meta-box';

		// category metas in order
		foreach( $terms as $term ) {
			$order['normal'] .= ",newsletter-category-meta-box-{$term->term_id}";
		}

		return $order;
	}

	/**
	 * AJAX: Update the article category
	 * Expects a POST object parameters:
	 * 	categoryId - term id
	 *  articles 	- array of article IDs
	 * @since	3.0.0
	 * @access	public
	 */
	public function update_article_category() {
		global $wpdb;

		$category_id = $_POST['category'] ?? false;
		$articles   = $_POST['articles'] ?? [];

		// update category
		foreach( $articles as $post_id ) {
			wp_set_post_terms( $post_id, $category_id, self::$category, false );
		}
	}

	/**
	 * AJAX: Update the article category order
	 */
	public function update_article_category_order() {
		$array_data = $_POST['updated_array'];
		foreach( $array_data as $taxonomy_data ) {
			update_term_meta( $taxonomy_data[0], self::$order, (int) ( $taxonomy_data[1] + 1 ) );
		}
		wp_die();
		exit;
	}

	/**
	 * Re-order the categories on the edit screen based on the tax_position value
	 */
	public function alter_category_order( $pieces, $taxonomies, $args ) {
		foreach( $taxonomies as $taxonomy ) {
			global $wpdb;

			$order = self::$order;
			$join_statement = " LEFT JOIN $wpdb->termmeta AS term_meta ON t.term_id = term_meta.term_id AND term_meta.meta_key = '$order'";

			if( strstr( $pieces['join'], $join_statement ) === false ) {
				$pieces['join'] .= $join_statement;
			}
			$pieces['orderby'] = "ORDER BY CAST( term_meta.meta_value AS UNSIGNED )";
		}
		return $pieces;
	}

	/**
	 * Restrict articles to issue if parameter defined
	 */
	public function filter_posts_by_issue ( $query ) {
		$issue = get_query_var('issue');

		if (strlen($issue) > 0 && ($query->is_main_query()) && (is_tax(self::$category) || is_tax(self::$tag))) {

			// if issue is 'latest' get ID of latest newsletter
			if($issue == 'latest') {
				$newsletter = FF_Newsletter_Common::get_latest_newsletter();
				$issue = $newsletter ? $newsletter->ID : 0;
			}

			if( $issue > 0 ) {
				$query->set('meta_query', array(
					array(
						'key' => 'ff_parent_id',
						'value' => $issue,
						'compare' => '='
					)
				));
			}
		}
	}
}

/**
 * Init Buzz_Addon_Taxonomies Class
 */
$buzz_addon = new Buzz_Addon_Taxonomies();

/**
 * Register Activation and Deactivation functions
 */
register_activation_hook( __FILE__, array( $buzz_addon, 'activate' ) );
register_deactivation_hook( __FILE__, array( $buzz_addon, 'deactivate' ) );

/**
 * Add plugin actions if Buzz Newsletter plugin is active
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if( is_plugin_active( 'buzz-newsletter/buzz-newsletter.php' ) ) {

	// Initialise the plugin
	$buzz_addon->plugin_init();

}
