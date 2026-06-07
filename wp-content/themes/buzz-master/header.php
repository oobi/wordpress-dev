<?php
/**
 * The common template region for the Header.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Custom
 * @since Custom 1.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<?php // contents of head
	get_template_part( 'blocks/header/head' ); ?>
</head>

<body <?php body_class(); ?>>

	<div id="wrapper">
		<div class="container">

			<?php // get customizer settings
			$header_layout = get_theme_mod( 'ff_theme_header_layout' );

			/***********************************************
			 * BANNER 
			 ***********************************************/

			$image_size = ''; // blank to default to full size
			include( locate_template( 'blocks/header/banner.php' ) );

			/***********************************************
			 * FEATURED IMAGE / NAVBAR  
			 ***********************************************/

			global $post;
			
			// checking for is_issue allows us to show featured image for newsletter-specific category/tag views
			$is_issue 			= isset( $wp_query->query_vars['issue'] );

			// is the plugin active?
			$plugin_active 		= class_exists( 'FF_Newsletter' );

			// archive pages may specify 'issue' parameter which specifies which newsletter to view
			$is_news_taxonomy 	= is_archive() && $is_issue;

			// single posts of newsletter type
			$is_newsletter      = is_single() && $post->post_type == 'newsletter';

			// defaults
			if( !isset( $plugin_active ) ) 		{ $plugin_active = false; }
			if( !isset( $is_news_taxonomy ) ) 	{ $is_news_taxonomy = false; }
			if( !isset( $is_newsletter ) ) 		{ $is_newsletter = false; }
			if( !isset( $post ) ) 				{ $post = false; }

			// if Buzz is active, and is a newsletter archive, newsletter or homepage
			if ( $plugin_active && ( $is_news_taxonomy || $is_newsletter || is_home() ) ) {

				switch( $header_layout ) {
					case 'image-first' 	: 
						get_template_part( 'blocks/header/featured-image', 'newsletter' );
						get_template_part( 'blocks/header/navbar', 'newsletter' );
						break;
					case 'image-second' :
						get_template_part( 'blocks/header/navbar', 'newsletter' );
						get_template_part( 'blocks/header/featured-image', 'newsletter' );
						break;
					case 'image-none'	:
					default 			: 
						get_template_part( 'blocks/header/navbar', 'newsletter' );
				}

			}

			// else if an ARTICLE / POST show nav THEN featured image
			elseif( is_singular() && $post ) {
				get_template_part( 'blocks/header/navbar', $post->post_type );
				get_template_part( 'blocks/header/featured-image-title', $post->post_type );
			}

			// for everything else just show the nav bar
			else {
				get_template_part( 'blocks/header/navbar' );
			}

			/***********************************************
			 * MAIN CONTENT AREA 
			 ***********************************************/ ?>
			 
			<div id="main">
				<div class="inner row">