<?php

namespace FF\Midgard;

/**
 * Common methods used in various places in plugin implementation
 *
 * @link       http://www.fireflyinteractive.net
 * @since      3.0.0
 *
 * @package    midgard
 * @subpackage midgard/includes
 */


class Midgard_Common {

	/**
	 * A registry of available feed type strings
	 */
	protected static $feed_type_registry = array();

	/**
	 * Register hooks for feed type (from sub-plugin)
	 *
	 * @param	string		$feed_type			type name of feed
	 */
	public static function register_feed_type($feed_type) {
		self::$feed_type_registry[] = $feed_type;
	}


	public static function get_data($post_id, $nomap=false) {
		// determine from post meta what the feed type is
		$type = get_post_meta($post_id, 'midgard_feed_type', true);

		// if the required type is registered and active then call its action
		if(in_array($type, self::$feed_type_registry)) {
			// do the action 'midgard_data_get_<type>'
			return apply_filters( "midgard_data_get_$type", $post_id, $nomap );
		}

		// fallback return nothing
		return json_encode(array('error'=>'Invalid feed type ' . $type));
	}


	/**
	 * Add, update or delete custom meta values.
	 *
	 * @since	1.0.0
	 * @access	private
	 * @param	int			$post_id			Post ID of the article
	 * @param	int			$meta_key			Key of the meta value
	 * @param	string		$new				The new meta value to be added to the database
	 */
	public static function save_meta_values( $post_id, $meta_key, $new ) {

		// Get the old meta value of the custom field key.
		$old = get_post_meta( $post_id, $meta_key, true );

		// evaluate if $new is empty data
		$new_empty = ($new === '');

		// If a new meta value was added and there was no previous value, add it.
		if ( !$new_empty ) {

			$result = add_post_meta( $post_id, $meta_key, $new, true );

			// if add failed on unique, update it instead
			if( ! $result ) {
				$result = update_post_meta( $post_id, $meta_key, $new );
			}

		// If there is no new meta value but an old value exists, delete it.
		} elseif ( $new_empty && $old ) {
			delete_post_meta( $post_id, $meta_key, $old );
		}

	}


	/**
	 * Parameters for wp_remote_get
	 */
	public static function wp_remote_get_params() {
		return apply_filters('midgard_wp_remote_get_params', array('timeout' => 30) );
	}
}
