<?php
/**
 * Sidebar that includes child pages of the current section page
 *
 * @package WordPress
 * @subpackage Firefly
 * @since Firefly Base 2.0
 */

global $post;
?>
<div id="sidebar" class="hidden-xs col-sm-3">

	<a class="back-link" href="<?php echo ff_get_my_portal_link(); ?>"><i class="fa fa-chevron-left"></i> Back to my portal</a>

	<div class="submenu">

		<?php
		// PAGE SUBMENU
		// get the current section post item
		$current_section = ff_get_current_section_post();

		// output submenu
		printf('<h3 class="current-section"><a href="%s">%s</a></h3>',
						get_post_permalink($current_section->ID),
						$current_section->post_title);

		ff_the_page_sub_menu( $current_section );

		?>
	</div><!-- /.submenu -->

</div>