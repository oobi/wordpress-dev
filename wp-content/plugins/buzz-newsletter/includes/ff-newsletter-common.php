<?php

/**
 * Common methods used in various places in newsletter implementation
 *
 * @link       http://www.fireflyinteractive.net
 * @since      3.0.0
 *
 * @package    ff_newsletter
 * @subpackage ff_newsletter/includes
 */


class FF_Newsletter_Common {

	/**
	 * Determine if the front end is a newsletter view page
	 * That is - it is an article or a newsletter post type, or it is the home view
	 *
	 * @param 	{WP Object} 	$newsletter 	post object to determine the post type
	 * @param 	{string/array} 	$post_type 		post type(s) to check
	 * @return 	{boolean}
	 */
	public static function is_newsletter_view( $newsletter=NULL, $post_type=NULL ) {
		global $post;
		$haystack = array();

		if( !$newsletter ) {
			$newsletter = $post;
		}

		// if I'm not a single post type OR if the global post variable is not defined
		// return false
		if( ( !is_single( $newsletter->ID ) && !is_home() ) || ( !$newsletter ) ) {
			return FALSE;
		}

		// if no post_type passed, create default haystack
		if( !$post_type ) {
			array_push( $haystack, 'newsletter', 'article' );
		}

		// if passed post_type is not an array, add to haystack
		if( !is_array( $post_type ) ) {
			array_push( $haystack, $post_type );
		}

		return in_array( $newsletter->post_type, $haystack );
	}

	/**
	 * Retrieve article query based on custom fields
	 * @see WP_Query options
	 * @since    3.0.0
	 */
	public static function get_article_query($options=array()) {

		$defaults = array(
			'feature'		=> NULL,
			'feature-email'	=> NULL,
			'parent-id'		=> NULL,
			'posts_per_page'=> -1,
			'post_type' 	=> 'article',
			//'post_status'	=> 'publish',
			'post_status'	=> current_user_can('edit_others_posts') ? 'any' : 'publish',
			'orderby'		=> 'menu_order',
			'order'			=> 'ASC'
		);

		$opt = array_merge($defaults, $options);
		$meta =  array();

		// feature : TRUE (include), FALSE (exclude)
		if($opt['feature'] !== NULL) {
			array_push($meta, array(
				'key' 		=> 'ff_featured_article',
				'compare'	=> $opt['feature'] ? 'EXISTS' : 'NOT EXISTS'
			));
		}
		// feature-email : TRUE (include), FALSE (exclude)
		if($opt['feature-email'] !== NULL) {
			array_push($meta, array(
				'key' 		=> 'ff_featured_email',
				'compare'	=> $opt['feature-email'] ? 'EXISTS' : 'NOT EXISTS'
			));
		}
		// parent-id : int
		if($opt['parent-id'] !== NULL) {
			array_push($meta, array(
				'key' 		=> 'ff_parent_id',
				'value' 	=> $opt['parent-id'],
				'compare'	=> '='
			));
		}

		// terms
		$args = array_merge($opt, array(
			'meta_query' => $meta
		));

	    return new WP_Query($args);

	}

	/**
	 * Retrieve article ARRAY based on custom fields
	 * @see get_article_query
	 * @since    3.0.0
	 */
	public static function get_articles($options=array()) {
		$q = self::get_article_query($options);
		return $q->posts;
	}

	/**
	 * Retrieve newsletter ARRAY based on query parameters
	 * @see WP_Query
	 * @since    3.0.0
	 */
	public static function get_newsletters($options=array()) {
		// default parameters for query
		$defaults = array(
			'orderby'		=> 'date',
			'order'			=> 'DESC',
			'post_type'		=> 'newsletter',
			'post_status'	=> 'publish'
		);

		if(!is_array($options)) {
			throw new Exception("Options must be an array", 1);
		}

		// merge passed args with defaults
		$args = array_merge($defaults, $options);

		// get newsletters
		return get_posts( $args );
	}

	/**
	 * Retrieve the parent newsletter for a specified article or newsletter ID
	 * @see WP_Query
	 * @since    3.0.0
	 */
	public static function get_newsletter($post_id = NULL) {
		global $post;
		$newsletter = NULL;

		// if no post ID specified then use global post ID
		if(!$post_id && $post) {
			$post_id = $post->ID;
		}

		// get the post object from the specified ID
		$p = get_post($post_id);

		if($p) {

			switch($p->post_type) {
				// in the case of newsletter we already have what we want
				case 'newsletter' :
					$newsletter = $p;
					break;

				// for articles we want the parent newsletter
				case 'article' :
					$newsletter_id = get_post_meta($post_id, 'ff_parent_id', TRUE);
					$newsletter = get_post($newsletter_id);
					break;
			}

		}

		return $newsletter;
	}

	/**
	 * Retrieve newsletter by slug
	 * @see WP_Query
	 * @since    3.0.0
	 */
	public static function get_newsletter_by_slug($slug) {
		return get_page_by_path($slug, OBJECT, 'newsletter');
	}

	 /**
	 * Get the latest (most recently published) newsletter
	 * @since    3.0.0
	 */
	public static function get_latest_newsletter() {
		$options = array(
			'posts_per_page' => 1
		);
		$newsletter = self::get_newsletters($options);

		return !empty( $newsletter[0] ) ? $newsletter[0] : NULL;
	}

	/**
	 * Get the permalink for the specified newsletter
	 * @param $newsletter - newsletter post object
	 * @return permalink or false on failure
	 * @see get_permalink
	 */
	public static function get_newsletter_url($newsletter=null) {
		if(!$newsletter) {
			$newsletter = self::get_latest_newsletter();
		}
		return get_permalink($newsletter->ID);
	}

	/**
	 * Get the post title for the specified newsletter
	 * @param $newsletter - newsletter post object
	 * @return post title or false on failure
	 * @see get_the_title
	 */
	public static function get_newsletter_title($newsletter=null) {
		if(!$newsletter) {
			$newsletter = self::get_latest_newsletter();
		}
		return get_the_title($newsletter->ID);
	}

	/**
	 * Get the permalink for the specified newsletter
	 * @paran $format - date format string
	 * @param $newsletter - newsletter post object
	 * @return permalink or false on failure
	 * @see get_the_date
	 */
	public static function get_newsletter_date($format='j F Y', $newsletter=null) {
		if(!$newsletter) {
			$newsletter = self::get_latest_newsletter();
		}
		return get_the_date( $format, $newsletter->ID);
	}


	 // TODO: remove when old theme retired
	/**
	 * Get an array of the terms used in a given taxonomy for the specified issue
	 * @param $taxonomy - taxonomy slug
	 * @param $newsletter_id - ID of the relevant newsletter issue
	 * @return Array of search results
	 */
	public static function get_terms_by_issue( $taxonomy, $newsletter_id ) {
		global $wpdb;

		$sql = "SELECT t.*, COUNT(t.term_id) AS count
			FROM $wpdb->terms AS t

			INNER JOIN $wpdb->term_taxonomy AS tt
			ON t.term_id = tt.term_id

			INNER JOIN $wpdb->term_relationships AS tr
			ON t.term_id = tr.term_taxonomy_id

			INNER JOIN $wpdb->postmeta AS pm
			ON tr.object_id = pm.post_id

			WHERE tt.taxonomy IN ('$taxonomy')
			AND pm.meta_key = 'ff_parent_id'
			AND pm.meta_value = $newsletter_id

			GROUP BY t.term_id

			ORDER BY t.name ASC";

		$results = $wpdb->get_results($sql);
		return $results;

	}

	/**
	 * TODO: tag
	 */
	public static function get_option( $option, $default = NULL ) {

		$plugin = FF_Newsletter::get_instance();
		return $plugin->get_option( $option, $default );

	}

	/**
	 * TODO: tag
	 */
	public static function set_option( $option, $value ) {

		$plugin = FF_Newsletter::get_instance();
		$plugin->set_option( $option, $value );

	}

	/**
	 * Add, update or delete custom meta values.
	 *
	 * @since	2.0.0
	 * @access	private
	 * @param	int			$post_id			Post ID of the article
	 * @param	int			$meta_key			Key of the meta value
	 * @param	string		$new_meta_value		The new meta value to be added to the database
	 */
	public static function save_meta_values( $post_id, $meta_key, $new_meta_value ) {

		// Get the meta value of the custom field key.
		$meta_value = get_post_meta( $post_id, $meta_key, true );

		// If a new meta value was added and there was no previous value, add it.
		if ( $new_meta_value && '' == $meta_value ) {
			add_post_meta( $post_id, $meta_key, $new_meta_value, true );

		// If the new meta value does not match the old value, update it.
		} elseif ( $new_meta_value && $new_meta_value != $meta_value ) {
			update_post_meta( $post_id, $meta_key, $new_meta_value );

		// If there is no new meta value but an old value exists, delete it.
		} elseif ( '' == $new_meta_value && $meta_value ) {
			delete_post_meta( $post_id, $meta_key, $meta_value );
		}

	}
}
