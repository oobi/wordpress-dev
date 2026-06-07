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

			<?php
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

			// use include/locate template (not get_template_part) in order to pass through above variables
			include( locate_template( 'blocks/header/lower.php' ) );

			/***********************************************
			 * MAIN CONTENT AREA 
			 ***********************************************/ ?>
			 
			<div id="main">
				<div class="inner row">