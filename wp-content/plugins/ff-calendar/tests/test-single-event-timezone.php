<?php
/**
 * Test the events output
 */
class TestSingleTimezone extends CalendarEventTestBase {

	public static function setUpBeforeClass() {
		self::$datafile = 'single-event-with-time.ics';
		parent::setUpBeforeClass();
	}

	// is the event a REST response
	public function test_rest_response() {
		$output = self::$data;
		$this->assertInstanceOf( 'WP_REST_Response', $output );
	}

	// assert $output->data is array with one element
	public function test_array_format() {
		$output = self::$data;
		$this->assertTrue( is_array( $output->data) );
		$this->assertEquals( count($output->data), 1 );
	}

	// all day flag should be true
	public function test_all_day() {
		$output = self::$data;
		$event = $output->data[0];
		$this->assertFalse( $event['allDay'] );
	}

	// // start date should be midnight on 1st June in the current year
	// public function test_start_date() {
	// 	$output = self::$data;
	// 	$event = $output->data[0];
	// 	$this->assertEquals( $event['start'], '2018-06-01T00:00:00' );
	// }

	// // end date should be 2018-06-02 midnight
	// public function test_end_date() {
	// 	$output = self::$data;
	// 	$event = $output->data[0];
	// 	$this->assertEquals( $event['end'], date('Y') . '-06-02T00:00:00' );
	// }

	// recurring should be false
	public function test_is_recurring() {
		$output = self::$data;
		$event = $output->data[0];
		$this->assertFalse( $event['isRecurring'] );
	}

}
