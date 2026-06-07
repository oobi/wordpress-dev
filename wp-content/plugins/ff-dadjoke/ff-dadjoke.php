<?php
/**
 * @package ff_dadjoke
 * @version 1.0
 */
/*
Plugin Name: Firefly Dad Jokes
Description: Retrieve a random selection of dad jokes for display in UI
Author: Chris Carey
Version: 1.0
Author URI: https://www.fi.net.au
*/

function ff_dadjoke_getjoke() {
	// get jokes from transient
	$jokes = json_decode(get_transient('ff_dadjokes'), true);
	
	// no jokes? Grab some from API
	if(! $jokes ) {		
		$url = 'https://icanhazdadjoke.com/search?limit=30';
		$response = wp_remote_get($url, array(
			'timeout' => 5,
			'headers' => array('Accept' => 'application/json')
		));
		
		if ( is_array( $response ) ) {
		  $jokes = strip_tags(wp_remote_retrieve_body($response)); // use the content  
		}
	
		// do a little checking to ensure jokes in correct format
		$jokes = json_decode($jokes, true);
		$jokes = is_array($jokes) && array_key_exists('results', $jokes) ? $jokes['results'] : array();
		
		// flatten response
		$jokes = array_map( function($value) {
			return isset($value['joke']) ? $value['joke'] : '';
		}, $jokes);
		
		// store as transient for a month
		set_transient('ff_dadjokes', json_encode($jokes), 30 * DAY_IN_SECONDS ); 
	}
	
	if( ! is_array($jokes) ) {
		return '';
	}
	
	// And then randomly choose a line
	return wptexturize( $jokes[ mt_rand( 0, count( $jokes ) - 1 ) ] );
}

// This just echoes the chosen line, we'll position it later
function ff_dadjoke() {
	$chosen = ff_dadjoke_getjoke();
	echo "<p id='dadjoke'>$chosen</p>";
}

// Now we set that function up to execute when the admin_notices action is called
add_action( 'admin_notices', 'ff_dadjoke' );

// We need some CSS to position the paragraph
function ff_dadjoke_css() {
	// This makes sure that the positioning is also good for right-to-left languages
	$x = is_rtl() ? 'left' : 'right';

	echo "
	<style type='text/css'>
	#dadjoke {
		float: $x;
		padding-$x: 15px;
		padding-top: 5px;		
		margin: 0;
		font-size: 11px;
	}
	</style>
	";
}

add_action( 'admin_head', 'ff_dadjoke_css' );

?>
