<?php 
/*********************************************************************
 * Newsletter Featured image with title
 *
 * This file must be called from within the loop 
 *
 * This file must be called using include( locate_template() ) 
 * AFTER the following variables have been set:
 * 	- $image_size (string)
 *********************************************************************/

// defaults 
if( !isset( $image_size ) ) 			{ $image_size = 'banner'; }

global $post;
$current_newsletter = $post;

// checking for is_issue allows us to show featured image for newsletter-specific category/tag views
$is_issue = isset( $wp_query->query_vars['issue'] );

if( $is_issue ) {
	$issue = $wp_query->query_vars['issue'];
	$current_newsletter = ff_get_newsletter_by_slug($issue);
}

if( $is_issue || has_post_thumbnail( $current_newsletter->ID ) ) :?>
    <div id="featured-image">
        <?php
        $attr = array(
            'class' => 'col',
            'alt'   => get_bloginfo( 'description', 'display' )
        );

        echo get_the_post_thumbnail( $current_newsletter->ID, $image_size, $attr ); ?>

		<div class="hidden-xs">
			<?php ff_share_links( FALSE, $current_newsletter ); ?>
		</div>

    </div><!-- #featured-image-->
<?php
endif; 

// remove defaults (to prevent affecting other includes)
unset( $image_size );
?>