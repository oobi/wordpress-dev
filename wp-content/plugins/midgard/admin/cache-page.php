<?php

namespace FF\Midgard;

use phpFastCache\CacheManager;

class Midgard_Cache_Page {

	/**
	 * Constructor configuration (overridden by child classes)
	 */
	protected $config;

	/**
	 * Slug for feed test page
	 */
	private $page_slug;

	/**
	 * Post ID of the feed to be displayed on the page
	 */
	private $feed_id;

	/**
	 * Start up
	 */
	public function __construct() {
		// Set up variables
		$this->page_slug = 'feed-cache-page';
		$this->config = array(
			'parent_slug'	=> 'midgard_app',
			'page_title'  	=> __( 'Feed Cache', 'midgard'),
			'menu_title'  	=> __( 'Feed Cache', 'midgard'),
			'menu_slug'  	=> 'feed_cache_page'
		);

		// Actions
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
	}

	/**
	 * Add options page
	 */
	public function add_plugin_page() {
		// This page will be under "Feeds"
		add_submenu_page(
			$this->config['parent_slug'],		// Parent Menu item
			$this->config['page_title'],		// Page Title
			$this->config['menu_title'],		// Menu Title
			'manage_options',					// Capability
			$this->config['menu_slug'],			// Menu Slug
			array( $this, 'create_admin_page' )	// Callback function
		);
	}

	/**
	 * Page HTML callback
	 */
	// OVERRIDE METHOD
	public function create_admin_page() { ?>
		<div class="wrap">
			<h2><?php esc_attr_e( 'Feed Cache', 'midgard' ); ?></h2>

			<div id="poststuff">


				<?php
					// clear cache?
					if(array_key_exists('midgard-clear-cache', $_POST)) {
						$this->clear_cache( $_POST['midgard-clear-cache']);
					}
					// show cache stats
					else {
						$this->show_cache_stats();
					}
				?>

			</div>
		</div>
		<?php
	}

	/**
	 * Clear items from cache
	 * @param $key
	 */
	public function clear_cache( $key ) {
		$cache = Midgard::get_cache_instance();

		if($key == 'all') {
			$cache->clear();
		}
		?>
		<div class="updated">
			<p>Cache cleared. Refresh the page to see updated stats.</p>
			<p><button class="button-primary" onclick="document.location.reload()"><?php esc_attr_e( 'Reload stats', 'midgard'); ?></button></p>
		</div>
		<?php
		return;
	}

	/**
	 * Show cache stats
	 */
	public function show_cache_stats() {
		// get cache info
		$cache = Midgard::get_cache_instance();
		$stats = $cache->getStats();
		$tag   = get_site_url();
		$items = $cache->getItemsByTag( $tag );

		?>

			<div id="post-body" class="metabox-holder columns-2">

				<!-- main content -->
				<div id="post-body-content">

					<div class="meta-box-sortables ui-sortable">

						<div class="postbox">

							<h3><span><?php esc_attr_e( 'Cache Control', 'midgard' ); ?></span></h3>

							<div class="inside">
								<form action="" method="POST">
									<input type="hidden" name="midgard-clear-cache" value="all">
									<input class="button-primary" type="submit" name="clear-cache" value="<?php esc_attr_e( 'Empty the cache', 'midgard'); ?>" />
								</form>
								<p><?php esc_attr_e( 'This will clear all cached items from disk.', 'midgard'); ?></p>

							</div><!-- .inside -->
						</div><!-- .postbox -->

						<div class="postbox">

							<h3><span><?php esc_attr_e( 'Cache Overview', 'midgard' ); ?></span></h3>

							<div class="inside">

								<table class="widefat">
									<tr>
										<th class="row-title"><?php esc_attr_e( 'Key', 'midgard' ); ?></th>
										<?php /*<?php esc_attr_e( 'Expires', 'midgard' ); ?></th> */ ?>
										<th><?php esc_attr_e( 'TTL (seconds)', 'midgard' ); ?></th>
									</tr>

									<?php foreach( $items as $key=>$item ) : ?>
										<tr valign="top">
											<td scope="row"><a href="<?php echo $key;?>"><?php echo $key; ?></a></td>
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

				</form>
			</div><!-- #post-body .metabox-holder .columns-2 -->
			<br class="clear">
			<?php
	}

}

// init the options page
if( is_admin() ) {
	$feed_test_page = new Midgard_Cache_Page();
}


