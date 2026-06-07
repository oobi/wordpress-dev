<?php 
/*********************************************************************
 * Email View Featured Image
 *
 * This file must be called from within an email template
 *
 * This file must be called using include( locate_template() ) 
 * AFTER the following variables have been set:
 * 	- $image_size (string)
 *********************************************************************/

// defaults
if( !isset( $image_size ) ) 			{ $image_size = 'email-banner'; }

if( has_post_thumbnail() ) : ?>
	<tr><td id="featured-image">
		<?php
			$attr = array(
				'class' => 'col',
				'alt'   => get_bloginfo( 'description', 'display' )
			);
			the_post_thumbnail( $image_size, $attr );
		?>
	</td></tr>
<?php endif;

// remove defaults (to prevent affecting other includes)
unset( $image_size );
?>