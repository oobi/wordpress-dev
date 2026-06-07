<?php

namespace FF\Calendar;

use Kigkonsult\Icalcreator\Vcalendar;
use Kigkonsult\Icalcreator\Vevent;

/**
 * The class to retrieve and handle remote data
 *
 * @package    ff-calendar
 * @subpackage ff-calendar/admin
 * @author     Firefly Interactive
 */
class Data {

	// the feed options
	public $feeds;
	public $feed_ids;

	// the feed data
	public $data;

	// cache instance
	protected $cache;

	// timezone (either a string timezone or GMT offset depending on what is set in WordPress)
	public $timezone;

	// TODO: remove if not needed
	// // ics file properties
	// public $ics_calname;		// ICS file name
	// public $ics_caldesc;		// ICS file description
	// public $ics_timezone;		// ICS file timezone (IMPORTANT! Some ICS sources do not give timezones to individual events, this is the fallback)

	// date formats
	public $date_format			= 'Y-m-d';
	public $date_format_time 	= 'Y-m-d\TH:i:s';

	/**
	 * Constructor - set some convenience values
	 */
	public function __construct() {
		// get an instance to cache object
		$this->cache = FF_Calendar::get_cache_instance();
		$this->data 		= array();
		$this->feeds    	= array();

		// get timezone
		$tz_string			= get_option( 'timezone_string' );
		$gmt_offset			= get_option( 'gmt_offset' );

		// if the timezone string is set, use that
		if( !empty( $tz_string ) ) {
			$this->timezone = $tz_string;
		}
		// if using gmt offset, format to make readable by PHP DateTimeZone (eg. +0200, -1000, etc)
		else if( !empty( $gmt_offset ) ) {
			$this->timezone = sprintf( '%+05d', $gmt_offset * 100 );
		}
		// otherwise default to UTC
		else {
			$this->timezone = 'UTC';
		}

		// get calendar options
		$options 			= get_option( 'ff_calendar_settings' );
		if( isset($options['calendar_feeds']) ) {
			foreach( $options['calendar_feeds'] as $feed ) {
				$feed['url'] = html_entity_decode( $feed['url'] ); // decode the URL first
				$this->feeds[$feed['id']] = $feed;
				$this->feed_ids[] = $feed['id'];
			};
		}
	}


	/*******************************************************************************************
	 * REST CALLBACKS
	 *******************************************************************************************/

	/**
	 * Get all data
	 */
	public function get_all_data( $request ) {
		// loop through all feeds, get all data
		$data 		= array();
		$response 	= array();

		// set cache key
		$key 		= 'get_all_data';

		// retrieve data from cache
		$response = $this->get_cache_data( $key );

		// if object not in cache, grab directly from source and cache it
		if( !$response ) {
			foreach( $this->feeds as $feed ) {
				$data[$feed['id']] 		= $this->get_feed_data( $feed );
				$response[$feed['id']] 	= $data[$feed['id']];
			}

			// cache the output
			// log the key to error log for debugging
			error_log( 'CACHE KEY: ' . $key );
			$this->set_cache_data( $key, $response );
		}

		return $this->prepare_response( $response );
	}

	/**
	 * Get all feed configs
	 */
	public function get_configs( $request ) {
		return $this->prepare_response( $this->feeds );
	}

	/**
	 * Get feed config by ID
	 */
	public function get_config( $request ) {
		$feed_id = $request['id'];
		$config = $this->get_feed_config( $feed_id );
		return $this->prepare_response( $config );
	}

	/**
	 * Get all feeds' events
	 */
	public function get_feeds_events( $request ) {
		// if feed_ids is not specified then get an array of all feed IDs
		$feed_ids 	= $request['id'] ? explode(',', $request['id']) : $this->feed_ids ;
		$events 	= array();
		$response 	= array();
		$limit 		= null;
		$start_date	= null;

		$hash = md5( implode( ',', $feed_ids) );

		// if limiting data by number of events
		if( !empty( $request['limit'] ) ) {
			$limit = array(
				'type' 	=> 'events',
				'num' 	=> (int) intval( $request['limit'] ),
				'offset'=> (int) intval( $request['offset']),
				'start_date'=> $request['start_date']
			);
		}
		// if limiting data by number of days
		elseif( !empty( $request['days'] ) ) {
			$limit = array(
				'type' 		=> 'days',
				'num' 		=> (int) intval( $request['days'] ),
				'start_date'=> $request['start_date']
			);
		}

		// MERGED DATA is an expensive operation so we want to cache it
		// set cache key
		$key 		= 'merged_events_' . $hash;

		// retrieve data from cache
		$merged = $this->get_cache_data( $key );

		// if object not in cache, grab directly from source and cache it
		if( !$merged ) {

			// loop through all feeds, get only events data
			foreach( $feed_ids as $id ) {
				$feed_data = $this->get_feed_data( $id );

				// if feed data is an error, ignore it
				if( is_wp_error( $feed_data ) ) {
					continue;
				}

				$events[$id] = $feed_data;
				$response[$id] = $feed_data['events'];
			}

			// merge all feeds together
			$merged = array();
			foreach( $response as $r ) {
				$merged = array_merge( $r, $merged );
			}

			// sort merged array by date
			usort( $merged, function( $a, $b ) {
				if( $a['start'] == $b['start'] ) {
					return $a['title'] > $b['title'] ? 1 : -1;
				}
				return $a['start'] <=> $b['start'];
			});

			// cache the output
			$this->set_cache_data( $key, $merged );
		}

		// limit events
		$response = $this->limit_events( $merged, $limit );

		return $this->prepare_response( $response );
	}

	/**
	 * Get SINGLE feed events by ID
	 */
	public function get_feed_events( $request ) {
		$feed_id 	= $request['id'];
		$limit 		= null;

		// if limiting data by number of events
		if( !empty( $request['limit'] ) ) {
			$limit = array(
				'type' 	=> 'events',
				'num' 	=> (int) intval( $request['limit'] ),
				'offset'=> (int) intval( $request['offset']),
				'start_date'=> $request['start_date']
			);
		}
		// if limiting data by number of days
		elseif( !empty( $request['days'] ) ) {
			$limit = array(
				'type' 		=> 'days',
				'num' 		=> (int) intval( $request['days'] ),
				'start_date'=> $request['start_date']
			);
		}

		// get events
		$events = $this->get_feed_data( $feed_id );

		// limit events
		$response = $this->limit_events( $events['events'], $limit );

		return $this->prepare_response( $response );
	}

	/**
	 * Get all feeds' categories
	 */
	public function get_feeds_categories( $request ) {
		// if feed_ids is not specified then get an array of all feed IDs
		$feed_ids 	= $request['id'] ? explode(',', $request['id']) : $this->feed_ids ;

		// loop through all feeds, get only categories data
		$categories = array();
		$response = array();
		foreach( $feed_ids as $id ) {
			$categories[$id] = $this->get_feed_data( $id );
			$response = array_merge( $response, $categories[$id]['categories'] );
		}
		return $this->prepare_response( $response );
	}

	/**
	 * Get a feed's categories by ID
	 * @deprecated
	 */
	public function get_feed_categories( $request ) {
		$feed_id = $request['id'];
		$categories = $this->get_feed_data( $feed_id );
		return $this->prepare_response( $categories, 'categories' );
	}




	/*******************************************************************************************
	 * GET DATA METHODS
	 *******************************************************************************************/


	public function get_feed_data( $feed_ref = null ) {
		// if an array, it is a feed config
		if( is_array( $feed_ref ) ) {
			$feed_config = $feed_ref;
		}
		// otherwise it is a single ID, so get the config
		else {
			$feed_config = $this->get_feed_config( $feed_ref );
		}

		// on error return the error
		if( is_wp_error( $feed_config )) {
			return $feed_config;
		}

		// grab the data
		$data = $this->get_data( $feed_config );

		return $data;
	}




	/*******************************************************************************************
	 * EVENT DATA
	 *******************************************************************************************/


	/**
	 * Public API - get feed data by ID
	 * @param 	{Array} 	$feed_config	The feed config
	 * @return 	{String} 					JSON encoded output
	 */
	public function get_data( $feed_config ) {

		// set cache key
		$key = $feed_config['id'] . '_' . $feed_config['name'];
		$key = preg_replace( '/\W+/', '_', $key );

		// retrieve data from cache
		$output = $this->get_cache_data( $key );

		// if object not in cache, grab directly from source and cache it
		if( !$output ) {
			$output = $this->_get_data( $feed_config );

			// cache the output
			$this->set_cache_data( $key, $output );
		}

		return $output;
	}

	/**
	 * Cache some data if cache is active
	 * @param $key - the cache key
	 * @param $data - the data to cache
	 */
	protected function set_cache_data( $key, $data ) {
		// is caching on?
		$cache_active = FF_Calendar::is_cache_active();

		// if cache is NOT active then abort
		if( ! $cache_active || is_wp_error($data) ) return;

		// get or create the cache item identified by key
		$cache_item = $this->cache->getItem( $key );

		// add a tag for grouping and identification
		$tag = get_site_url();
		// replace any non-word chars with underscores
		$tag = preg_replace( '/\W+/', '_', $tag );

		$timeout = FF_Calendar::get_cache_time();

		$cache_item->set( $data )
				   ->expiresAfter( intval( $timeout ) ) //in seconds, also accepts Datetime
				   ->addTag( $tag );
		$this->cache->save( $cache_item ); // Save the cache item just like you do with doctrine and entities
	}

	/**
	 * Return cached data if cache is active
	 * @param $key - the cache key
	 * @return $data - cached data or false if cache not active
	 */
	protected function get_cache_data( $key ) {
		// is caching on?
		$cache_active = FF_Calendar::is_cache_active();

		// if cache is NOT active then abort and return nothing
		if( ! $cache_active ) return false;

		// try to get cached item
		$cache_item = $this->cache->getItem( $key );
		$output = $cache_item->get();

		if( is_null($output) || empty($output) ) {
			return false;
		}

		return $output;
	}

	/**
	 * Retrieve data for specified feed
	 * @param 	{Array} 	$feed_config	The feed config
	 * @return 	{Object} 					The feed data
	 */
	protected function _get_data( $feed_config ) {

		$config = array( 'unique_id' 	=> 'ff-calendar',
						 //'url'			=> $feed_config['url'],
						 // filename required to parse Redlands feed,
						 // works on other sites without it.
						 // TODO: find a work around which doesn't require a
						 //		 hardcoded filename
					 	//  'filename'		=> 'calendar.ics'
						);

		$vcalendar = new Vcalendar( $config );

		ExceptionThrower::Start();

		// TEST MODE - supplied data
		// do we already have data in the feed config?
		// if so then use it
		// NB: we use this in unit tests to supply test data
		if( isset($feed_config['data']) ) {
			$ical_raw = $feed_config['data'];
		}
		// LIVE MODE - remote data
		// othwewise go get me some data from the URL
		else {

			$response = wp_remote_get( html_entity_decode( $feed_config['url'] ), array(
				'timeout'	=> 30
			));
			$ical_raw = '';

			if( is_array( $response )) {
				$ical_raw = $response['body']	;
			} else if( is_wp_error($response)) {
				return $response;
			} else {
				return new \WP_error( 'load-error', 'Unable to load calendar ICS from source.');
			}

		}

		try{
			$vcalendar->parse( $ical_raw );
		} catch(\Exception $ex) {
			return new \WP_Error('ics-error', 'Could not parse ICS feed');
		}

		ExceptionThrower::Stop();

		// TODO: remove if not needed
		// // get the global calendar properties
		// $this->ics_calname 	= $this->get_flat_property( $vcalendar, 'X-WR-CALNAME' );
		// $this->ics_caldesc 	= $this->get_flat_property( $vcalendar, 'X-WR-CALDESC' );
		// $this->ics_timezone = $this->get_flat_property( $vcalendar, 'X-WR-TIMEZONE' );

		// put data in required format
		return $this->transform_data( $vcalendar, $feed_config );
	}

	/**
	 * Transform data into standard format
	 *
	 * @param 	{Object} 	$vcalendar 		vcalendar instance
	 * @param 	{String} 	$feed_config	The feed config
	 */
	protected function transform_data( $vcalendar, $feed_config ) {

		// select all events between two months ago and one year from now
		// see documentation (..\lib\iCalcreator\docs\) for more in-depth explanation of parameters
		// $y = date('Y');
		// $m = date('n');
		// $d = date('j');
		// $events = $vcalendar->selectComponents(
		// 						$y, $m-2, $d, 	// start year, month, day
		// 						$y+1, $m, $d, 	// end year, month, day
		// 						"vevent", 		// component type to select
		// 						false, 			// flat [FALSE] = output in year/month/day keyed array
		// 						true, 			// any [TRUE] = select components with recurrence in period
		// 						false 			// split [FALSE] = one occurrence of component in output array
		// 					);
		$dfrom = new \DateTime( '-1 month' );
		$dto = new \DateTime( '+1 year' );

		$events = $vcalendar->selectComponents( $dfrom, $dto );
		$data = array();

		// The data to be used to create an event array
		$formatted_feed_name = $this->format_class_name( $feed_config['name'] );
		$event_args = array(
			'feed_config'			=> $feed_config,
			'formatted_feed_name'	=> $formatted_feed_name,
			'feed_class'			=> 'ff-feed-' . $formatted_feed_name,
			'all_categories'		=> array()
		);

		// loop through events and transform into readable array
		if( !empty( $events ) ) {
			foreach( $events as $year => $year_arr ) {
				foreach( $year_arr as $month => $month_arr ) {
					foreach( $month_arr as $day => $day_arr ) {
						foreach( $day_arr as $event ) {

							// get a nicely formatted event array
							$data[] = $this->create_event_array( $event, $event_args );

						} // foreach event in day
					} // foreach day in month
				} // foreach month in year
			} // foreach year
		} // if

		// dedupe the categories collection
		$deduped = array_map( 'unserialize', array_unique( array_map( 'serialize', $event_args['all_categories'] ) ) );
		$event_args['all_categories'] = array_values( $deduped );

		// create the response array
		// REST endpoints will return what they need from this array
		$response = array(
			'feed' 			=> $event_args['feed_config'],
			'categories' 	=> $event_args['all_categories'],
			'events' 		=> $data
		);

		return $response;
	}

	/**
	 * Create an event array containing the data we want from the ICS
	 *
	 * @param 	{Event Object}	$event 			- The event object
	 * @param 	{Array}			$args 			- The event args array, passed by reference
	 */
	private function create_event_array( $event, &$args ) {
		// string which delimits multiple categories in string a;b;c;
		$category_delimiter = ';';

		// is this a recurring event?
		// only the first appearance of the event will have an rrule, subsequent events have an X-RECURRENCE custom value
		$is_recurring = !!$event->getRrule();

		// get date start/end
		$dt_start = $event->getDtstart( );
		$dt_end   = $event->getDtend();

		$dt_recur_start = '';
		$dt_recur_end = '';

		// if event is recurring
		if( $is_recurring ) {
			// get the number associated with the recurrence iteration (i.e. this is the nth occurence of this event)
			$dt_recurrence = $this->get_flat_property( $event, 'X-RECURRENCE' );

			// first appearance of recurring event does not have X-RECURRENCE (only 2nd, etc)
			if( !$dt_recurrence ) {
				$dt_recurrence = 1;
			}

			// recurring events dtstart refers to the "parent" event. We need to get the correct date for this recurrence
			// get the recurrance build_date start/end

			$dt_recur_start = $this->get_flat_property( $event, 'X-CURRENT-DTSTART' );
			$dt_recur_end 	= $this->get_flat_property( $event, 'X-CURRENT-DTEND' );

			// The start/end recurrence dates are in STRING format
			// If the recurrence date is NOT specified then it's the first instance so needs to be parsed as array (build_date)
			// otherwise needs to be parsed as STRING
			// output is the same either way
			if( empty( $dt_recur_start ) ) {
				$event_start = $this->build_date( $dt_start );
			} else {
				$event_start = $this->build_date( $dt_start, $dt_recur_start );
			}

			if( empty( $dt_recur_end ) ) {
				$event_end = $this->build_date( $dt_end );
			} else {
				$event_end = $this->build_date( $dt_end, $dt_recur_end, true );
			}

		}
		// else it is a normal (non-recurring) event
		else {
			// just set the values to the regular start/end times
			$event_start 	= $this->build_date( $dt_start );
			$event_end 		= $this->build_date( $dt_end );

			// set recurring flag to false
			$dt_recurrence 	= false;
		}

		// check if all day event
		$is_all_day = $this->is_all_day( $event_start, $event_end );

		// format description
		$description = str_replace( array("\r\n", "\r", "\n"), "<br>", stripcslashes( $event->getDescription( ) ) );

		// if category-key defined in feed config, find key in event and use as category
		$event_categories = array();
		if( array_key_exists( 'category-key', $args['feed_config'] ) && !empty( $args['feed_config']['category-key'] ) ) {
			$cats = $event->getXprop( $args['feed_config']['category-key'] );

			// if event has a category, add it to the array that goes into event object
			if( $cats ) {
				if( is_array( $cats )) {
					$cats_array = $cats;
				} else {
					$cats_array = explode( $category_delimiter, $cats );
				}
				foreach( $cats_array as $c ) {
					array_push( $event_categories, array(
						'label' => $c,
						'className' => $this->format_class_name( $c, 'ff-cat-' ) )
					);
				}
			}
		}
		// if no category-key, use the feed name as the category
		// every event should always have a category for the front-end filters to work
		else {
			array_push( $event_categories, array( 'label' => $args['feed_config']['name'], 'className' => 'ff-cat-' . $args['formatted_feed_name'] ) );
		}

		// stash the event category in a collection to sort through later
		$args['all_categories'] = array_merge( $args['all_categories'], $event_categories );

		// concat all event classes
		$event_class = $args['feed_class'];
		foreach( $event_categories as $ec ) {
			$event_class .= ' ' . $ec['className'];
		}

		// return the event array
		return array(
			'title' 		=> $event->getSummary(),
			'start'			=> $dt_start->format( $this->date_format_time ),
			'end'			=> $dt_end->format( $this->date_format_time ),
			// convert date to milliseconds
			'start_ms'		=> $dt_start->format( 'U' ) * 1000,
			'end_ms'		=> $dt_start->format( 'U' ) * 1000,
			'allDay'		=> $is_all_day,
			'timezone'		=> $this->timezone,
			'className'		=> $is_all_day ? $event_class . ' fc-all-day' : $event_class,
			'description' 	=> $description,
			'categories'	=> !empty( $event_categories ) ? $event_categories : false,
			'location'		=> $event->getLocation( ),
			'recurrence'	=> $dt_recurrence,
			'isRecurring'	=> $is_recurring,

			// DEBUG
			'dt_start' 		=> $dt_start,
			'dt_end' 		=> $dt_end,
			'dt_recur_start'=> $dt_recur_start,
			'dt_recur_end' 	=> $dt_recur_end,
			// 'add_time'		=> $event_start['add_time']
		);
	}


	/*******************************************************************************************
	 * DATE UTILS
	 *******************************************************************************************/

	/**
	 * Build a formatted date array from ARRAY, STRING or DATETIME input
	 * @param	{Array}		$date				The date array.
	 * 											- For a normal event, this will be the event date
	 * 											- For a recurring event, this will be the "parent" event date
	 * @param	{String}	$recurring_date		The recurring date string
	 * @param	{Boolean}	$is_end				Whether the passed event is the end date
	 */
	private function build_date( $date, $recurring_date=false, $is_end=false ) {
		// check the date is an array
		if ( is_array( $date ) ) {

			// if we are passed a recurring date as well, use this and pass the array date as the "parent event"
			if( is_string( $recurring_date ) ) {
				$parent_event = $date;
				return $this->_build_date_from_string( $recurring_date, $parent_event, $is_end );
			}

			// build the date from an array
			return $this->_build_date_from_array( $date );
		}

		// if not passed an array, return
		return false;
	}


	/**
	 * Default the ICS date object to ensure we always have hour/min/sec
	 * @param {Array} $date - The date object to default
	 * 						- Date object comes with two keys
	 * 						- 'value' contains date broken down by year, month, day, time etc
	 * 						- 'params' if present contains timezone info
	 * @return {Array} 		- The defaulted date
	 */
	private function _build_date_from_array( $date ) {
		// all day events can be sometimes marked with a param. If so, grab it
		$add_time = true;
		if( isset( $date['params'] ) ) {
			$add_time = ( isset( $date['params']['VALUE'] ) && $date['params']['VALUE'] == 'DATE' ) ? false : true;
		}

		// dates may be split into 'value' and 'params' keys where 'params' may contain the timezone info
		// pull out values/params so we can check for timezones
		if( isset( $date['value'] ) ) {
			$date_value = $date['value'];

			// if we have params, get the timezone - fallback to WP timezone if not available
			if( !empty( $date['params'] ) ) {
				$tz = isset( $date['params']['TZID'] ) ? $date['params']['TZID'] : $this->timezone;
			}
		}
		// otherwise we don't have split value/params, so assign date_value and continue
		else {
			$date_value = $date;
		}

		// timezones sometimes appear in amongst the values rather than in params
		// check the date values for timezone (can appear as either 'tz_id' or 'tz')
		if( isset( $date_value['tz_id'] ) ) {
			$tz = $date_value['tz_id'];
		}
		else if( isset( $date_value['tz'] ) ) {
			$tz = $date_value['tz'];
		}
		// otherwise if no timezone found in params OR in amongst other values then assume data is in same timezone as WordPress
		else {
			$tz = $this->timezone;
		}

		// extract value from event date
		$value = array(
			'year' 		=> isset($date_value['year'])  ? $date_value['year']   : 1970,
			'month'		=> isset($date_value['month']) ? $date_value['month']  : 1,
			'day'		=> isset($date_value['day'])   ? $date_value['day']    : 1,
			'hour'		=> isset($date_value['hour'])  ? $date_value['hour']   : 0,
			// min and sec may be expressed as 'minute' and 'second' so check for both and standardise
			'min'		=> isset($date_value['min']) ? $date_value['min'] : (isset($date_value['minute']) ? $date_value['minute'] : 0),
			'sec'		=> isset($date_value['sec']) ? $date_value['sec'] : (isset($date_value['second']) ? $date_value['second'] : 0),
			'tz'		=> $tz,
			'add_time'	=> $add_time
		);

		return $value;
	}

	/**
	 * Convert recurrence date format to "standard" date format
	 * @param 	{String} 	$date 			the date to format.
	 * @param 	{Array} 	$parent_event 	the parent event used to determine is_all_day status
	 * @param 	{Boolean} 	$is_end 		is the end date
	 *
	 * @example input may take the following forms depending on whether it specifies just a date or a date & time:
	 * 		 ["X-CURRENT-DTSTART","2016-08-30"] or ["X-CURRENT-DTSTART","2016-08-29 09:00:00 Australia\/Sydney"]
	 *
	 * @return {Array}
	 */
	private function _build_date_from_string( $date, $parent_event, $is_end=false ) {
		// recurrence dates are received in string format
		// parse the recurrence date into a keyed date array
		$parsed_date = date_parse( $date );

		// if this is the end time
		if( $is_end ) {
			// non-zero time?
			$is_midnight = intval($parsed_date['hour']) + intval($parsed_date['minute']) + intval($parsed_date['second']);

			// if the time of the END date is midnight
			// then we add a day to the date so that the event occurs on midnight of the correct day instead of on the same day as START
			if($is_midnight == 0) {
				$str_date = $this->build_string_date( $parsed_date );
				$end = new \DateTime($str_date);
				$end->add( new \DateInterval('P1D') );
				$parsed_date = date_parse( $end->format( $this->date_format_time ) );
			}
		}

		// if parent date has params, add those to new date as well
		if( isset( $parent_event['params'] ) ) {
			$add_time = ( isset( $date['params']['VALUE'] ) && $date['params']['VALUE'] == 'DATE' ) ? false : true;
			$new_date['value'] = $parsed_date;
			$new_date['params'] = $parent_event['params'];
		}
		// otherwise just use the parsed date as-is
		else {
			$new_date = $parsed_date;
		}

		// return the built date
		return $this->build_date( $new_date );
	}

	/**
	 * Retrieve a flattened property from the event
	 * @param {Object} $event - the event object
	 * @param {string} $prop - the property to retrieve
	 *
	 * @return {string/boolean}
	 */
	private function get_flat_property( $event, $prop ){
		$value =  $event->getXprop( $prop );
		if( is_array( $value ) ) {
			if( count( $value ) > 1) {
				return $value[1];
			} else {
				return false;
			}
		}
		return $value;
	}

	/**
	 * Build a string date from an event array
	 * @param 	{Array}		$event_date 		The event array
	 */
	private function build_string_date( $event_date ) {
		// format date YYYY-mm-dd
		// e.g. 2018-02-01
		$event_string = sprintf('%d-%02d-%02d',
							$event_date['year'],
							$event_date['month'],
							$event_date['day']);

		// Add time to formatted date YYYY-mm-ddThh:mm:ss
		// e.g. 2018-02-01T12:23:01
		if( isset( $event_date['add_time'] ) && $event_date['add_time'] ) {
			$event_string .= sprintf('T%02d:%02d:%02d',
							$event_date['hour'],
							$event_date['min'],
							$event_date['sec']);
		}

		// add timezone
		if( isset( $event_date['tz'] ) ) {
			$event_string .= sprintf(' %s', $event_date['tz'] );
		}

		return $event_string;
	}

	/**
	 * Convert date format to readable date format
	 * @param 	{Array} 	$event_date 	the date to format.
	 * @param 	{Boolean} 	$use_ms 		return the date as a timestamp with milliseconds.
	 * @return 	{String}
	 */
	private function convert_date_format( $event_date=false, $use_ms=false ) {
		if( $event_date ) {
			// format date as a string
			$event_string = $this->build_string_date( $event_date );

			// if time is included, get the timezone converted datetime with time
			if( isset( $event_date['add_time'] ) && $event_date['add_time'] ) {
				$new_tz_date = $this->datetime_in_timezone( $event_string, $this->date_format_time );
			}
			// otherwise convert without time
			else {
				$new_tz_date = $this->datetime_in_timezone( $event_string, $this->date_format );
			}

			// if returning as a millisecond timestamp
			if( $use_ms ) {
				$new_tz_date = strtotime( $new_tz_date . ' ' . $this->timezone ) * 1000;
			}

			return $new_tz_date;
		}

		return false;
	}



	/**
	 * Determine if event will be flagged as "all day"
	 *
	 * @param {Array} $dt_start - array containing date start
	 * @param {Array} $dt_end - array containing date end
	 */
	private function is_all_day( $event_start=false, $event_end=false ) {
		// reject invalid start
		if( !$event_start ) return false;

		if( !is_array( $event_end ) ) $event_end = array();

		// force valid keys
		$req      = array( 'year'=> 1970, 'month' => 1, 'day' => 1, 'hour' => 0, 'min' => 0, 'sec' => 0, 'tz' => '' );
		$event_start = array_merge( $req, $event_start );
		$event_end   = array_merge( $req, $event_end );

		// set up date strings and defaults
		$start 		= $event_start['year'] . '-' . $event_start['month'] . '-' . $event_start['day'];
		$end   		= $event_end['year'] . '-' . $event_end['month'] . '-' . $event_end['day'];
		$has_time 	= false;

		$is_all_day = false;

		$start_total = intval($event_start['hour']) + intval($event_start['min']) + intval($event_start['sec']);
		$end_total = intval($event_end['hour']) + intval($event_end['min']) + intval($event_end['sec']);

		// if both start and end are zero then it's an all day event
		if( $start_total == 0 && $end_total == 0 ) {
			$is_all_day = true;
		}

		return $is_all_day;
	}




	/*******************************************************************************************
	 * HELPER METHODS
	 *******************************************************************************************/

	/**
	 * Get the feed config by ID
	 * If no ID passed, return all configs
	 *
	 * @param 	{String}	$feed_id 		The Feed ID
	 * @return 	{Array|WP_Error}			The Feed Config or Error
	 */
	 public function get_feed_config( $feed_id ) {
		// if invalid ID, throw error
		if( !array_key_exists( $feed_id, $this->feeds ) ) {
			return new \WP_Error( '404', 'Feed ID not found' );
		}

		// return feed config
		return $this->feeds[$feed_id];
	}

	/**
	 * Format a string into a CSS-compatible class name
	 *
	 * @param 	{String}	$name 		The string to format
	 * @param 	{String}	$prefix		The string to prefix the class with
	 * @return 	{String} 				The formatted string
	 */
	public function format_class_name( $name = null, $prefix = '' ) {
		if( !$name ) {
			return '';
		}

		// make lower case and replace non-alphabetical/numeric characters
		return $prefix . strtolower( preg_replace( '/[^a-zA-Z0-9]+/', '-', $name ) );
	}

	/**
	 * Filter out the events that are not in the future or within limits
	 * @param 	{Array} 	$events 		The events array to filter
	 * @param 	{Array}		$limit 			The limit object
	 */
	public function limit_events( $events=array(), $limit=array() ) {
		// if no limit object, return all events
		if( empty( $limit ) ) {
			return $events;
		}

		// wordpress timezone
		$wp_timezone		= new \DateTimeZone( $this->timezone );

		// get current date (no time) in WP timezone
		$now 		= new \DateTime('now');
		$now->setTimezone( $wp_timezone );
		$today 		= $now->format( $this->date_format );

		// get current date (with time) in WP timezone
		$today_with_time 	= new \DateTime('now');
		$today_with_time->setTimezone( $wp_timezone );
		$today_with_time 	=  $today_with_time->format( $this->date_format_time );

		// default the limit array
		$limit 				= array_merge( array( 'type' => '', 'num' => 0, 'offset' => 0 ), $limit );

		// limit by number of days
		if( $limit['type'] == 'days' ) {
			// get the start date + day limit
			$future_limit = $now->modify('+' . $limit['num'] . ' days');
			$future_limit = $future_limit->format($this->date_format);

			// return all events in the next x days
			$limited = array_filter( $events, function( $e ) use( $today, $today_with_time, $future_limit ) {
				// get start/end date in wp timezone
				$e_start = $this->datetime_in_timezone( $e['start'], $this->date_format );
				$e_end = $this->datetime_in_timezone( $e['end'], $this->date_format_time );

				// if the event starts today (date only) or ends today (date and time)
				// AND the event starts the set limit or ends within the set limit
				// then include it in the filtered results
				return ( $e_start >= $today || $e_end >= $today_with_time )
						&& ( $e_start < $future_limit || $e_end <= $future_limit );
			});

			// slice to reset array keys
			return array_values($limited);
		}
		// limit by number of events
		elseif( $limit['type'] == 'events' ) {
			// return all events in the future
			$limited = array_filter( $events, function( $e ) use( $today, $today_with_time ) {
				// get start/end date in wp timezone
				$e_start = $this->datetime_in_timezone( $e['start'], $this->date_format );
				$e_end = $this->datetime_in_timezone( $e['end'], $this->date_format_time );

				// if the event starts today (date only) or ends today (date and time)
				// then include it in the filtered results
				return ( $e_start >= $today || $e_end >= $today_with_time );
			});

			// get and return the next x events
			return array_slice( $limited, $limit['offset'], $limit['num'] );
		}

		// if somehow not limiting by days or events, return all
		return $events;
	}


	/**
	 * prepare a REST response and handle error
	 * @param $response - the data we will send back as our response
	 * @param $key (optional) - the key in the data we wish to return
	 */
	public function prepare_response( $response, $key=false ) {
		$output = null;

		// if it's an error or no key, output the whole response
		if( is_wp_error( $response) || !$key ) {
			$output = $response;
		}
		// else if the key exists in the response, return data in that key
		else if( $key && isset( $response[$key] ) ) {
			$output = $response[$key];
		}
		// throw error otherwise
		else {
			$output = new \WP_Error( 'config', "Key $key does not exist" );
		}

		// TODO: if it's a WP error then format it
		return rest_ensure_response( $output );
	}

	/**
	 * Create new DateTime formatted string with set timezone
	 * @param $input - date string
	 * @param $format - date format to return
	 * @return formatted datetime string
	 */
	private function datetime_in_timezone( $input, $format=null ) {
		// if input is not a date string, return false
		if( !is_string( $input ) ) {
			return false;
		}

		// default format
		if( is_null( $format ) ) {
			$format = $this->date_format;
		}

		// wordpress timezone
		$wp_timezone = new \DateTimeZone( $this->timezone );

		// get current date (no time) in WP timezone
		$now = new \DateTime( $input );
		$now->setTimezone( $wp_timezone );
		return $now->format( $format );
	}
}
