<?php 
/*********************************************************************
 * Banner
 *
 * This file must be called from within the loop 
 *
 * This file must be called using include( locate_template() ) 
 * AFTER the following variables have been set:
 * 	- $image_size (string)
 *********************************************************************/

// defaults 
if( !isset( $image_size ) ) 			{ $image_size = 'logo'; } ?>


<div id="banner" class="<?php echo display_header_text() ? "text-banner" : "image-banner"; ?>">
	<a href="<?php echo get_site_url(); ?>" title="<?php bloginfo('name'); ?>">

		<?php // if header text is available, display it
		if( display_header_text() ) : ?>

			<div class="header-content">
				<!-- logo -->
				<?php if ( get_theme_mod( 'ff_newsletter_logo' ) ) : ?>
					<div class='site-logo'>
						<?php
							// get image url
							$image_url = esc_url( get_theme_mod( 'ff_newsletter_logo' ) );

							// store the image ID in a var
							$image_id = ff_get_image_id($image_url);

							// retrieve the thumbnail size of our image
							$image_thumb = wp_get_attachment_image_src($image_id, $image_size);
						?>
						<img src='<?php echo $image_thumb[0]; ?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'>
					</div>
				<?php endif; ?>

				<!-- title and tagline -->
				<div class="site-text">
					<h1 class="site-title"><?php bloginfo( 'name' ); ?></h1>
					<?php
					$description = get_bloginfo( 'description', 'display' );
					if ( $description || is_customize_preview() ) : ?>
						<p class="site-description"><?php echo $description; ?></p>
					<?php endif; ?>
				</div>

			</div>

		<?php endif; ?>

		<!-- header image -->
		<?php
		$header_image  = get_header_image();
		$mobile_header = get_theme_mod( 'ff_mobile_header_image' );
		$blog_title    = get_bloginfo('title');

		if( !empty( $header_image ) ) : ?>
			<img class="custom-header" src="<?php echo $header_image; ?>" alt="<?php echo $blog_title; ?>">
		<?php endif; ?>

		<!-- mobile header image -->
		<?php
		if( !empty( $mobile_header ) ) : ?>
			<img class="mobile-header" src="<?php echo $mobile_header; ?>" alt="<?php echo $blog_title; ?>">
		<?php elseif ( !empty( $header_image ) ) : ?>
			<img class="mobile-header" src="<?php echo $header_image; ?>" alt="<?php echo $blog_title; ?>">
		<?php endif; ?>

	</a>
</div><!-- #banner -->

<?php // remove defaults (to prevent affecting other includes)
unset( $image_size );
?>