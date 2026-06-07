<?php 
/*********************************************************************
 * Header Featured image
 *
 * This file must be called from within the loop 
 *
 * This file must be called using include( locate_template() ) 
 * AFTER the following variables have been set:
 * 	- $image_size (string)
 *********************************************************************/

// defaults 
if( !isset( $image_size ) ) 			{ $image_size = 'banner'; }

$has_thumb 			= has_post_thumbnail(); 
$default_thumb_path = get_stylesheet_directory() . '/images/default/featured_image.jpg'; 
$default_thumb_uri 	= ff_get_theme_directory() . '/images/default/featured_image.jpg'; ?>

<div id="featured-image" <?php echo !$has_thumb ? 'class="hidden-xs"' : ''; ?>>

	<?php if( $has_thumb ) :
		$attr = array(
			'class' => 'col',
			'alt'   => get_bloginfo( 'description', 'display' )
		);
		the_post_thumbnail( $image_size, $attr );
	elseif( file_exists( $default_thumb_path ) ) : ?>
		<img src="<?php echo $default_thumb_uri; ?>" alt="<?php echo get_bloginfo( 'description', 'display' ); ?>">
	<?php endif; ?>

</div><!-- #featured-image-->

<?php // remove defaults (to prevent affecting other includes)
unset( $image_size );
?>