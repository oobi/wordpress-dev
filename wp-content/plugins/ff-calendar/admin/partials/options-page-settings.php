<?php

/**
 * The settings page "Generate Shortcode" tab
 *
 * @link       http://www.fi.net.au
 * @since      1.0.0
 *
 * @package    FF_Calendar
 * @subpackage FF_Calendar/admin/partials
 */
?>

<form method="post" action="options.php">
<?php
	settings_fields( 'cache_group' );			// Option group
	settings_fields( 'feed_group' );			// Option group
	do_settings_sections( $this->settings_page );	// Page
	submit_button();
?>
</form>

<?php 