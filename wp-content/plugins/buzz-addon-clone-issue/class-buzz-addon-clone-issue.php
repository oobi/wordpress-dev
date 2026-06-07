<?php

class Buzz_Addon_Clone_Issue {

	protected $textdomain = 'buzz-clone';
	protected $transient  = 'buzz-addon-clone-notice';


    public function plugin_init() {

		// add the clone action to newsletter row action
		add_filter( 'post_row_actions', array( $this, 'add_archive_link' ), 10, 2 );

		// handle the clone action
		add_action( 'admin_action_buzz_clone', array( $this, 'handle_clone_action') );

		// show any admin notices
		add_action( 'admin_notices', array($this, 'show_admin_notice'));
	}

	/**
	 * handle the clone admin action
	 */
	public function handle_clone_action() {
		$id = $_REQUEST['post'] ?? 0 ;

		// clone the issue
		$result = $this->clone_issue( $id );

		// set happy state for admin notice
		if( $result ) {
			set_transient($this->transient, 'updated', 30);
		// sad
		} else {
			set_transient($this->transient, 'error', 30);
		}

		// redirect to newsletter list when done
		wp_redirect( admin_url( '/edit.php?post_type=newsletter' ), 301 );
		exit();
	}

	/**
	 * Show an admin notice if the transient is set
	 */
	public function show_admin_notice() {
		$transient = get_transient( $this->transient );

		if( $transient !== false ){
			printf( '<div class="%s notice is-dismissible"><p>%s</p></div>',
				$transient,
				__('Clone successful', $this->textdomain)
			);
		}

		// Delete transient, only display this notice once.
		delete_transient( $this->transient );
	}


	/**
     * Add link on archive
     *
     * @uses   get_post_type_object, get_archive_post_link, current_user_can, esc_attr
     * @access public
     * @since  0.0.1
     * @param  array string $actions
     * @param  integer $id
     * @return array $actions
     */
    public function add_archive_link( $actions, $post ) {
		global $current_screen, $mode;

		// only for newsletter post type
		if( $post->post_type != 'newsletter') {
			return $actions;
		}

		$post_type_object = get_post_type_object( $post->post_type );

		// only if current user is an editor or better
		if ( ! current_user_can( $post_type_object->cap->publish_posts, $post->ID ) ) {
			return $actions;
		}


		// Add clone link
		$actions['buzz_clone'] = sprintf( '<a href="%s" title="%s">%s</a>',
			$this->get_clone_link( $post->ID ),
			esc_attr( __( 'Clone this issue and related articles', $this->textdomain  ) ),
			__( 'Clone Issue', $this->textdomain  )
		);


        return $actions;
    }

	/**
	 * Generate a clone link for given post ID
	 */
	protected function get_clone_link( $id=0 ) {
		$post = get_post( $id );

		if( ! $post ) return '';

		$action = '?action=buzz_clone&post=' . $id;
		$admin_url = admin_url( "admin.php". $action );

		return wp_nonce_url( $admin_url, 'buzz_clone' . $post->ID);
	}


	/**
	 * Clone an issue
	 */
	protected function clone_issue( $id=0 ) {
		$post = get_post( $id );


		// invalid type to clone
		if( !$post || $post->post_type != 'newsletter') return false;

		$post_type_object = get_post_type_object( $post->post_type );

		// only if current user is an editor or better
		if ( ! current_user_can( $post_type_object->cap->publish_posts, $post->ID ) ) {
			return false;
		}

		// clone the issue post
		$new_post = $this->clone_post( $post, $post->post_title . ' (clone)' );

		// now that we've cloned the issue, clone its children (articles)
		if($new_post) {

			// query articles in current issue
			$articles = FF_Newsletter_Common::get_articles(array(
				'parent-id'	=> $post->ID
			));

			// clone each article, setting the meta value of the parent to the new issue
			foreach( $articles as $article ) {
				$this->clone_post( $article, false, array('ff_parent_id' => $new_post->ID) );
			}

		} else {
			return false;
		}

		// clone articles
		return true;
	}


	/**
	 * Clone a post
	 * @param $post - post to cone
	 * @return - new post object
	 */
	protected function clone_post( $post, $title=false, $meta=array() ) {
		$new_post_status = 'draft';
		$new_post = false;

		// use current user as author unless we can clone the original author
		$new_post_author = wp_get_current_user();
		$new_post_author_id = $new_post_author->ID;

		// check if the user has the right capability
		if(is_post_type_hierarchical( $post->post_type )){
			if(current_user_can('edit_others_pages')){
				$new_post_author_id = $post->post_author;
			}
		} else {
			if(current_user_can('edit_others_posts')){
				$new_post_author_id = $post->post_author;
			}
		}

		// insert_post arguments
		$args = array(
			'comment_status' 		=> $post->comment_status,
			'ping_status' 			=> $post->ping_status,
			'post_author' 			=> $new_post_author_id,
			'post_content' 			=> $post->post_content,
			'post_content_filtered' => $post->post_content_filtered,
			'post_excerpt' 			=> $post->post_excerpt,
			'post_password' 		=> $post->post_password,
			'post_status' 			=> $new_post_status,
			'post_title' 			=> $title ? $title : $post->post_title,
			'post_type' 			=> $post->post_type
		);

		$new_post_id = wp_insert_post(wp_slash($args));

		// if we got a good copy return the new post object
		if($new_post_id !== 0 && !is_wp_error($new_post_id)) {
			$new_post = get_post( $new_post_id );

			// clone meta values (includes post thumbnail)
			$this->clone_meta( $post, $new_post, $meta );

			// clone taxonomy terms
			$this->clone_taxonomy_terms( $post, $new_post );
		}

		// new post object or false if failed
		return  $new_post;
	}

	/**
	 * Clone meta values between posts
	 * @param $src - source post
	 * @param $dest - destination post
	 */
	protected function clone_meta( $src, $dest, $override=array() ) {
		// we don't want these keys
		$meta_blacklist[] = '_edit_lock'; // edit lock
		$meta_blacklist[] = '_edit_last'; // edit lock

		// generate array of keys to copy
		$post_meta_keys = get_post_custom_keys($src->ID);
		$meta_keys 		= array_diff($post_meta_keys, $meta_blacklist);

		// copy the keys
		foreach ($meta_keys as $meta_key) {
			$meta_values = get_post_custom_values($meta_key, $src->ID);
			foreach ($meta_values as $meta_value) {
				$value = array_key_exists( $meta_key, $override ) ? $override[$meta_key] : $meta_value;
				$value = maybe_unserialize($value);
				$result = add_post_meta($dest->ID, $meta_key, $this->addslashes_deep($value) );
			}
		}

	}

	/**
	 * Copy the taxonomies of a post to another post
	 */
	protected function clone_taxonomy_terms( $src, $dest ) {
		global $wpdb;
		if (isset($wpdb->terms)) {
			// Clear default category (added by wp_insert_post)
			wp_set_object_terms( $dest->ID, NULL, 'category' );

			$taxonomies = get_object_taxonomies($src->post_type);

			// several plugins just add support to post-formats but don't register post_format taxonomy
			if(post_type_supports($src->post_type, 'post-formats') && !in_array('post_format', $taxonomies)){
				$taxonomies[] = 'post_format';
			}

			foreach ($taxonomies as $taxonomy) {
				$post_terms = wp_get_object_terms($src->ID, $taxonomy, array( 'orderby' => 'term_order' ));
				$terms = array();

				foreach( $post_terms as $term ) {
					$terms[] = $term->slug;
				}

				wp_set_object_terms($dest->ID, $terms, $taxonomy);
			}
		}
	}


	/*
	 * Workaround for inconsistent wp_slash.
	 * Works only with WP 4.4+ (map_deep)
	 */
	public function addslashes_deep( $value ) {
		if (function_exists('map_deep')){
			return map_deep( $value, array( $this, 'addslashes_to_strings_only'));
		} else {
			return wp_slash( $value );
		}
	}

	public function addslashes_to_strings_only( $value ) {
		return is_string( $value ) ? addslashes( $value ) : $value;
	}

}