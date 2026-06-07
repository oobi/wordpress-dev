<?php

class Buzz_Addon_Dates_Data {

	// unique string prepend fieldnames and metabox ID
	public static $meta_key 	 		= '_buzz_newsletter_dates';

	// legacy compatibility -  pre version 1.3
	public static $legacy    		= false;
	public static $legacy_meta_key 	= 'buzz_newsletter_dates';

	/**
	 * Get the date templates
	 *
	 * 'slug' must match up with a corresponding twig in /public/views
	 */
	public static function get_templates() {

		// DATES TEMPLATES
		return [
			[
				'slug'		=> 'list-1col',
				'name'		=> 'List (1 Column)',
			],
			[
				'slug'		=> 'list-2col',
				'name'		=> 'List (2 Columns)',
			],
			[
				'slug'		=> 'row',
				'name'		=> 'Row',
			],
			[
				'slug'		=> 'email-list-2col',
				'name'		=> 'EMAIL - List (2 Columns)',
			],
			[
				'slug'		=> 'email-row',
				'name'		=> 'EMAIL - Row',
			],
		];

	}

	/**
     * Get the dates of a newsletter by ID. If no post ID passed or no dates found, return false
	 *
	 * @param 	{int}	$set 		Date set to grab
	 * @param 	{int}	$merged 	Do we want to merge items with same date into one structure?
	 * @param 	{int}	$id 		The newsletter post ID
     *
     * @return FireflyPost
     */
    public static function get_dates( $args=array(), $id=false ) {
		$args = array_merge(
			array(
				'set'			=> '',
				'merge_dates' 	=> false,
				'show_dates' 	=> false
			), $args
		);

		$set = $args['set'];

		// default ID to current newsletter ID
		if(!$id) {
			// get current post ID
			$id = get_the_ID();

			// if current post is an article, use the parent ID instead
			if( get_post_type( $id ) === 'article' ) {
				$id = get_post_meta( $id, 'ff_parent_id', true );
			}
		}

		// get the dates
		$data = get_post_meta( $id, self::$meta_key, true );

		// if no dates or is not an array, return false
		if( !is_array( $data ) || empty( $data ) ) {

			// TODO: when there is no pre-1.3 data in the wild then we can just return false here
			// START DELETE:
			// have a look for legacy data if we haven't already used it
			if( ! self::$legacy ) {
				self::$legacy = true; // prevent legacy data rendering in more than one widget
				$data = array( $set => get_post_meta( $id, self::$legacy_meta_key, true ) );

				// no legacy data either
				if( !is_array( $data ) || empty( $data ) ) {
					return false;
				}
			}
			// already used legacy data
			else {
				return false;
			}
			// END DELETE

			// return false;

		}

		// get the data from specific date set
		$data = isset( $data[$set] ) ? $data[$set] : array();

		if( $data ) {
			if( $args['merge_dates'] ) {
				// get array of unique dates (for sorting/merging)
				$unique = array_unique( array_column( $data, 'date' ) );

				// optionally sort dates if we are showing them
				if( $args['show_dates'] ) {
					usort( $unique, function( $a, $b ) {
						return strtotime( $a ) - strtotime( $b );
					});
				}

				// create keyed array from sorted unique array
				$data_merged 	= array_fill_keys( $unique, [] );

				// fill the keyed array with dates
				foreach( $data_merged as $date => $value ) {

					$data_merged[$date] = array(
						'date'	=> $date,
						'items'	=> []
					);

					foreach( $data as $d ) {
						if( $date == $d['date'] ) {
							array_push( $data_merged[$date]['items'], $d );
						}
					}
				}

				$data = $data_merged;

			}
			// match original data and merged data format
			// wrap dates in additional array
			else {
				$new_data = [];
				foreach( $data as $date ) {
					$new_data[] = [
						'date' => $date['date'],
						'items' => [$date]
					];
				}
				$data = $new_data;
			}
		}

		// return sorted, merged dates array
		return $data;

    }

}