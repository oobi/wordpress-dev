<?php 
/*********************************************************************
 * The lower section of the header. Usually contains navbar and/or featured image
 *
 * This file must be called using include( locate_template() ) 
 * AFTER the following variables have been set:
 * 	- $plugin_active (bool)
 * 	- $is_news_taxonomy (bool)
 * 	- $is_newsletter (bool)
 * 	- $post (WP post object)
 *********************************************************************/

// defaults
if( !isset( $plugin_active ) ) 		{ $plugin_active = false; }
if( !isset( $is_news_taxonomy ) ) 	{ $is_news_taxonomy = false; }
if( !isset( $is_newsletter ) ) 		{ $is_newsletter = false; }
if( !isset( $post ) ) 				{ $post = false; }

if ( $plugin_active && ( $is_news_taxonomy || $is_newsletter || is_home() ) ) {
	get_template_part( 'blocks/header/navbar', 'newsletter' );
}

// ARTICLES / POSTS show nav THEN featured image
elseif( is_singular() && $post ) {
	get_template_part( 'blocks/header/navbar', 'newsletter' );
	get_template_part( 'blocks/header/featured-image-title', $post->post_type );
}

// for everything else just show the nav bar
else {
	get_template_part( 'blocks/header/navbar' );
}
?>