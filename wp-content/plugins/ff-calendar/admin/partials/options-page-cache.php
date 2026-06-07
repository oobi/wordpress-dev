<?php

use Phpfastcache\CacheManager;
use FF\Calendar\FF_Calendar;

/**
* The settings page "Cache Control" tab
*
* @link       http://www.fi.net.au
* @since      1.0.0
*
* @package    FF_Calendar
* @subpackage FF_Calendar/admin/partials
*/
?>

<h2>Cache Control</h2>

<?php

/**
* Clear items from cache
* @param $key
*/

if( array_key_exists( 'ff-calendar-clear-cache', $_POST ) ) {
	clear_cache( $_POST['ff-calendar-clear-cache'] );
} else {
	render_page_content();
}

/**
 * Clear items from cache
 * @param $key
 */
function clear_cache( $key ) {

	$cache = FF_Calendar::get_cache_instance();

	if($key == 'all') {
		$cache->clear();
	} ?>

	<div class="updated">
		<p>Cache cleared. Refresh the page to see updated stats.</p>
		<p><button class="button-primary" onclick="document.location.reload()"><?php esc_attr_e( 'Reload stats', 'ff_calendar' ); ?></button></p>
	</div>
	<?php
	return;
}

function render_page_content() {

	$cache = FF_Calendar::get_cache_instance();
	$stats = $cache->getStats();
	$tag   = get_site_url();
	$tag = preg_replace( '/\W+/', '_', $tag );
	$items = $cache->getItemsByTag( $tag );

	?>

	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">

			<!-- main content -->
			<div id="post-body-content">

				<div class="meta-box-sortables ui-sortable">

					<div class="postbox">

						<h3><span><?php esc_attr_e( 'Cache Control', 'midgard' ); ?></span></h3>

						<div class="inside">
							<form action="" method="POST">
								<input type="hidden" name="ff-calendar-clear-cache" value="all">
								<input class="button-primary" type="submit" name="clear-cache" value="<?php esc_attr_e( 'Empty the cache', 'midgard'); ?>" />
							</form>
							<p><?php esc_attr_e( 'This will clear all cached items from disk.', 'midgard'); ?></p>

						</div><!-- .inside -->
					</div><!-- .postbox -->

					<div class="postbox">

						<div class="inside">

							<table class="widefat">
								<tr>
									<th class="row-title"><?php esc_attr_e( 'Key', 'midgard' ); ?></th>
									<?php /*<?php esc_attr_e( 'Expires', 'midgard' ); ?></th> */ ?>
									<th><?php esc_attr_e( 'TTL (seconds)', 'midgard' ); ?></th>
								</tr>

								<?php foreach( $items as $key=>$item ) : ?>
									<tr valign="top">
										<td scope="row"><?php echo $key; ?></td>
										<?php /*<td><?php echo date_format( $item->getExpirationDate(), 'r'); ?></td> */ ?>
										<td><?php echo $item->getTtl(); ?></td>
									</tr>
								<?php endforeach; ?>
							</table>

						</div><!-- .inside -->
					</div><!-- .postbox -->

				</div><!-- .meta-box-sortables .ui-sortable -->
			</div>	<!-- post-body-content -->

			<!-- sidebar -->
			<div id="postbox-container-1" class="postbox-container">

				<div class="meta-box-sortables">

					<div class="postbox">

						<h3><span><?php esc_attr_e(
						'Cache Stats', 'midgard'
					); ?></span></h3>

					<div class="inside">
						<p><?php echo $stats->getInfo(); ?></p>
						<p>Cache size: <strong><?php echo $stats->getSize(); ?></strong> bytes</p>
					</div><!-- .inside -->

				</div><!-- .postbox -->
			</div><!-- .meta-box-sortables -->
		</div><!-- #postbox-container-1 .postbox-container -->
	</div>

	<?php
}