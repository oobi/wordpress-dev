<?php

namespace FF\Midgard\ICS;

use FF\Midgard\ExceptionThrower;
use FF\Midgard\Midgard_Plugin_Data_Base;
use kigkonsult\iCalcreator\vcalendar;

/**
 * The class to retrieve and handle remote data
 *
 * @package    midgard_ics
 * @subpackage midgard_ics/admin
 * @author     Firefly Interactive
 */
class Midgard_ICS_Data extends Midgard_Plugin_Data_Base  {

	/**
	 * Retrieve data for specified feed
	 *
	 * @param int $feed_id  (WP $post ID)
	 */
	protected function get_feed_data($feed_id) {

		// get the RSS feed URL
		$feed_url = get_post_meta( $feed_id, 'midgard_feed_uri', true );

		$config = array( 'unique_id' 	=> 'midgard',
						 'url'			=> $feed_url );

		$vcalendar = new vcalendar( $config );

		ExceptionThrower::Start();

		try{
			$vcalendar->parse();
		} catch(\Exception $ex) {
			return $this->error_message('Unable to retrieve data - ' . $ex->getMessage() );
		}

		ExceptionThrower::Stop();

		// put data in required format
		return $this->transform_data( $vcalendar, $feed_id );
	}


	/**
	 * Transform data into standard format
	 *
	 * @param Object $vcalendar - vcalendar instance
	 * @param int $feed_id  (WP $post ID)
	 */
	protected function transform_data( $vcalendar, $feed_id ) {
		// all components
		// $events = $vcalendar->components;

		// select only future events
		$y = date('Y');
		$m = date('n');
		$d = date('j');
		$events = $vcalendar->selectComponents($y, $m, $d, $y+1, $m, $d, "vevent", false, true, false);
		$data = array();

		// events will be false if iCal is invalid
		if(!$events) $events = array();

		foreach( $events as $year => $year_arr ) {
			foreach( $year_arr as $month => $month_arr ) {
				foreach( $month_arr as $day => $day_arr ) {
					foreach( $day_arr as $event ) {
						// date start/end
						$dt_start = $this->get_date_parts( $event->getProperty('dtstart') );
						$dt_end   = $this->get_date_parts( $event->getProperty('dtend') );

						// recurring date start/end
						$dt_recurrence = $event->getProperty('X-RECURRENCE');
						$dt_recur_start = $event->getProperty('X-CURRENT-DTSTART');
						$dt_recur_end = $event->getProperty('X-CURRENT-DTEND');

						// The start/end recurrence dates are in a different format
						// need to do some conversion before you can use

						// if recurrence is active, use these date values instead
						if($dt_recurrence) {
							$dt_start = $this->convert_recurrence_date($dt_recur_start, $dt_start);
							$dt_end = $this->convert_recurrence_date($dt_recur_end, $dt_end);
						}

						$date_start_string = $this->convert_date_format( $dt_start );
						$date_end_string   = $this->convert_date_format( $dt_end );

						$data[] = array(
							'title' 		=> $event->getProperty('summary'),
							'date_start' 	=> $date_start_string,
							'date_end' 		=> $date_end_string,
							'ts_start'		=> $this->wp_strtotime($date_start_string),
							'ts_end'		=> $this->wp_strtotime($date_end_string),
							'ts_start_ms'	=> $this->wp_strtotime($date_start_string, true),
							'ts_end_ms'		=> $this->wp_strtotime($date_end_string, true),
							'all_day'		=> $this->is_all_day($dt_start, $dt_end),
							'description' 	=> $event->getProperty('description'),
							'categories'	=> $event->getProperty('categories'),
							'location'		=> $event->getProperty('location'),
							'recurring'		=> $dt_recurrence ? $dt_recurrence[1] : false
						);

					}
				}
			}
		}

		return parent::transform_data($data, $feed_id);
	}

	/**
	 * Convert date format to readable date format
	 * @param {Array} $date - the date to format.
	 * @return {String}
	 */
	private function convert_date_format( $date=FALSE ) {
		if( $date ) {
			$new_date = $date['year'] . '-' . sprintf( "%02d", $date['month'] ) . '-' . sprintf( "%02d", $date['day'] );

			// add time if set
			if( isset($date['hour']) ) {
				if(!isset($date['minute'])) { $date['minute'] = 0; }
				if(!isset($date['second'])) { $date['second'] = 0; }
				$new_date .= 'T' . sprintf( "%02d", $date['hour'] ) . ':' . sprintf( "%02d", $date['minute'] ) . ':' . sprintf( "%02d", $date['second'] );
			}
			// if not set, set to midnight
			else {
				$new_date .= 'T00:00:00';
			}

			// add timezone if set
			if( array_key_exists( 'tz', $date ) ) {
				$new_date .= $date['tz'];
			}
			// // if not set, set to Z (UTC/Zulu)
			// else {
			// 	$new_date .= 'Z';
			// }

			// convert to local timezone
			$format = 'Y-m-d H:i O';
			$time = $this->wp_strtotime( $new_date );
			$localised = $this->wp_date_localised( $format, $time );

			return $localised;
		}

		return false;
	}

	/**
	 *
	 */
	private function get_date_parts( $date ) {
		$output = array(
			'year'		=> $date['year'],
			'month'		=> $date['month'],
			'day'		=> $date['day'],
			'hour'		=> isset( $date['hour'] ) ? $date['hour'] 	: 0,
			'minute'	=> isset( $date['minute'] ) ? $date['minute'] : 0,
			'second'	=> isset( $date['second'] ) ? $date['second'] : 0,
			'tz'		=> false
		);

		// hour
		if(array_key_exists('hour', $date)) {
			$output['hour'] = intval( $date['hour'] );
		}
		// min or minute
		if(array_key_exists('min', $date)) {
			$output['minute'] = intval( $date['min'] );
		}
		// sec or seconds
		if(array_key_exists('sec', $date)) {
			$output['second'] = intval( $date['sec'] );
		}

		// add timezone if set
		if( array_key_exists( 'tz', $date ) ) {
			$output['tz'] = $date['tz'];
		}

		return $output;
	}

	/**
	 * Convert recurrence date format to "standard" date format
	 * @param {Array} $input - the date to format.
	 * @param {Array} $default - the value to use if formatting is unsuccessful (bad input)
	 * @example input may take the following forms depending on whether it specifies a time or just a date:
	 * 		 ["X-CURRENT-DTSTART","2016-08-30"] or ["X-CURRENT-DTSTART","2016-08-29 09:00:00 Australia\/Sydney"]
	 *
	 *
	 * @return {Array}
	 */
	 private function convert_recurrence_date($input, $fallback) {
		if( !is_array($input) || count($input) < 2 ) {
			return $fallback;
		}

		$date = date_parse($input[1]);
		return $this->get_date_parts( $date );
	 }

	 /**
	  * Convert date parts array back into timestamp
	  */
	 private function get_timestamp( $input, $use_milliseconds=false ) {
		 $str = sprintf( '%04d-%02d-%02d ',
						  $input['year'],
						  $input['month'],
						  $input['day'] );

		 if( array_key_exists('hour', $input) ) {
			$str .= sprintf('%02d', $input['hour']);
		 }
		 if( array_key_exists('minute', $input) ) {
			$str .= sprintf(':%02d', $input['minute']);
		 }
		 if( array_key_exists('second', $input) ) {
			$str .= sprintf(':%02d', $input['second']);
		 }

		 $ts = $this->wp_strtotime($str);

		 // make timestamp use milliseconds
		 if( $use_milliseconds ) {
			 $ts = $ts*1000;
		 }

		 return $ts;
	 }

	/**
	 * Determine if event will be flagged as "all day"
	 *
	 * @param Array $dt_start - array containing date
	 * @param Array $dt_start - array containing date
	 */
	private function is_all_day( $dt_start=FALSE, $dt_end=FALSE ) {
		// reject invalid start
		if( !$dt_start ) return false;

		if( !is_array( $dt_end ) ) $dt_end = array();

		// force valid keys
		$req      = array( 'year'=> 1970, 'month' => 1, 'day' => 1 );
		$dt_start = array_merge( $req, $dt_start );
		$dt_end   = array_merge( $req, $dt_end );

		// set up date strings and defaults
		$start 		= $dt_start['year'] . '-' . $dt_start['month'] . '-' . $dt_start['day'];
		$end   		= $dt_end['year'] . '-' . $dt_end['month'] . '-' . $dt_end['day'];
		$has_time 	= false;

		// check if event has time specified
		if( array_key_exists( 'hour', $dt_start ) && array_key_exists( 'hour', $dt_end ) ) {
			$has_time = true;
		}

		$is_all_day = false;
		// Determine whether event is all day if:
		// if time is not specified
		if( !$has_time ) {
			$is_all_day = true;
		}
		// OR if both times are midnight (00)
		elseif( $has_time ) {
			if( $dt_start['hour'] == 0 && $dt_end['hour'] == 0 ) {
				$is_all_day = true;
			}
		}

		return $is_all_day;
	}

	private function wp_strtotime($str, $ms=false) {
		// This function behaves a bit like PHP's StrToTime() function, but taking into account the Wordpress site's timezone
		// CAUTION: It will throw an exception when it receives invalid input - please catch it accordingly
		// From https://mediarealm.com.au/
		$tz_string = get_option('timezone_string');
		$tz_offset = get_option('gmt_offset', 0);

		if (!empty($tz_string)) {
			// If site timezone option string exists, use it
			$timezone = $tz_string;
		} elseif ($tz_offset == 0) {
			// get UTC offset, if it isn’t set then return UTC
			$timezone = 'UTC';
		} else {
			$timezone = $tz_offset;
			if(substr($tz_offset, 0, 1) != "-" && substr($tz_offset, 0, 1) != "+" && substr($tz_offset, 0, 1) != "U") {
				$timezone = "+" . $tz_offset;
			}
		}
		$datetime = new \DateTime($str, new \DateTimeZone($timezone));

		// datetime expressed as seconds since epoch
		$value =  intval( $datetime->format('U') );

		// milliseconds? Express as milliseconds since epoch
		if ($ms ) $value *= 1000;

		return $value;
	}

	private function wp_date_localised($format, $timestamp = null) {
		// This function behaves a bit like PHP's Date() function, but taking into account the Wordpress site's timezone
		// CAUTION: It will throw an exception when it receives invalid input - please catch it accordingly
		// From https://mediarealm.com.au/
		$tz_string = get_option('timezone_string');
		$tz_offset = get_option('gmt_offset', 0);
		if (!empty($tz_string)) {
			// If site timezone option string exists, use it
			$timezone = $tz_string;
		} elseif ($tz_offset == 0) {
			// get UTC offset, if it isn’t set then return UTC
			$timezone = 'UTC';
		} else {
			$timezone = $tz_offset;
			if(substr($tz_offset, 0, 1) != "-" && substr($tz_offset, 0, 1) != "+" && substr($tz_offset, 0, 1) != "U") {
				$timezone = "+" . $tz_offset;
			}
		}
		if($timestamp === null) {
			$timestamp = time();
		}
		$datetime = new \DateTime();
		$datetime->setTimestamp($timestamp);
		$datetime->setTimezone(new \DateTimeZone($timezone));
		return $datetime->format($format);
	}

}
