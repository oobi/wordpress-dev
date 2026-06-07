<?php 
/*********************************************************************
 * Email View Banner
 *
 * This file must be called from within an email template
 *********************************************************************/

ob_start(); ?>

<?php
$email_header_image = get_theme_mod('ff_email_header_image');
if( $email_header_image ) : ?>

	<img class="custom-header" src="<?php echo esc_url( $email_header_image ); ?>" alt="<?php bloginfo('title'); ?>" width="640" />

<?php else : ?>

	<?php // if header text is available, display it
	if( display_header_text() ) : ?>

		<table border="0" cellpadding="0" cellspacing="0" width="100%" class="header-content">
			<tr>
				<?php // logo
				if ( get_theme_mod( 'ff_newsletter_logo' ) ) : ?>
					<td class="site-logo" valign="top" align="center">
						<a href="<?php echo get_site_url(); ?>" title="<?php bloginfo('name'); ?>">
							<img src='<?php echo esc_url( get_theme_mod( 'ff_newsletter_logo' ) ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'>
						</a>
					</td>
				<?php endif; ?>
			</tr>
			<tr>
				<td class="site-text" valign="top" align="center">
					<h1 class="site-title"><?php bloginfo( 'name' ); ?></h1>
					<?php
					$description = get_bloginfo( 'description', 'display' );
					if ( $description || is_customize_preview() ) : ?>
						<p class="site-description"><?php echo $description; ?></p>
					<?php endif; ?>
				</td>

			</tr>
		</table>

	<?php endif; ?>

<?php endif; ?>

<?php
	$banner_content = ob_get_contents();
	ob_end_clean();
	$banner_content = preg_replace('/>\s+</', '><', $banner_content);
?>

<tr><td id="banner" class="<?php echo display_header_text() ? "text-banner" : "image-banner"; ?>">
	<?php echo $banner_content; ?>
</td></tr><!-- #banner -->