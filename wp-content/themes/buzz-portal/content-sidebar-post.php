<?php
/**
 * Sidebar that includes child pages of the current section page
 *
 * @package WordPress
 * @subpackage Firefly
 * @since Firefly Base 2.0
 */
?>
<div id="sidebar" class="hidden-xs col-sm-3">

	<a class="back-link" href="<?php echo ff_get_my_portal_link(); ?>"><i class="fa fa-chevron-left"></i> Back to my portal</a>

	<div class="submenu">

		<?php
		// POSTS SUBMENU
		global $post;

		// get the current section post item
		$current_section = ff_get_posts_page();

		printf('<h3 class="current-section"><a href="%s">%s</a></h3>',
						get_post_permalink($current_section->ID),
						$current_section->post_title);


		$current_post_id = $post->ID;
		$myposts = get_posts();
		echo '<ul>';
		foreach($myposts as $post) {
			setup_postdata($post);
			printf('<li class="%s"><a href="%s"><span class="posted-on">%s</span> %s</a></li>' . "\n",
					$post->ID == $current_post_id ? 'current_page_item' : '',
					get_post_permalink($post->ID),
					get_the_date('l jS F Y', $post->ID),
					$post->post_title);
		}
		wp_reset_postdata();
		echo '</ul>';
		?>
	</div><!-- /.submenu -->

</div>