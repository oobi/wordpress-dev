<?php
/**
 * Class CalendarEventTestBase
 *
 * @package Ff_Calendar
 */

/**
 * Test the events output
 */
class CalendarEventTestBase extends WP_UnitTestCase {
	protected static $calendar;
	protected static $data;
	protected static $feed_id = 'data';
	protected static $datafile = '';

	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		// do this before creating calendar
		self::setupDatabase();

		// set up class
		self::$calendar = new FF\Calendar\Data();

		// grab some data
		self::$data = self::get_feed_data( self::$feed_id );
	}

	protected static function setupDatabase() {
		// before getting the class instance, we add test data to database
		$option = 'ff_calendar_settings';
		$new_value = [
			'calendar_feeds' =>	[
				[
					'id'		=> 'data',
					'name'		=> 'Event Data',
					'url'		=> '', // blank
					'data'		=> file_get_contents( __DIR__ . '/data/' . self::$datafile )
				]
			],
		];
		update_option( $option, $new_value );
	}

	protected static function get_feed_data( $id, $limit = '', $days = '' ) {
		$request = [ 'id' => $id, 'limit' => $limit, 'days' => $days ];
		return self::$calendar->get_feed_events( $request );
	}


	public static function tearDownAfterClass() {
		parent::tearDownAfterClass();
	}


}
