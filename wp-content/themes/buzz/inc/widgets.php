<?php
/**
 * Register our sidebars and widgetized areas.
 *
 * @package WordPress
 * @subpackage Firefly
 * @since Firefly 1.0
 */

if ( ! function_exists( 'ff_sidebars_init' ) ) :
/**
 * Register template sidebar regions
 */
function ff_sidebars_init() {

	// register custom widget areas
	register_sidebar( array(
		'name'          => 'Footer Column 1',
		'id'            => 'footer_widget_1',
		'description'   => 'This column appears on all widget layouts',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3>',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => 'Footer Column 2',
		'id'            => 'footer_widget_2',
		'description'   => 'This column appears on all widget layouts except the One Column layout',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3>',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => 'Footer Column 3',
		'id'            => 'footer_widget_3',
		'description'   => 'This column appears only on the Three Column and the Four Column layouts',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3>',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => 'Footer Column 4',
		'id'            => 'footer_widget_4',
		'description'   => 'This column appears only on the Four Column layout',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3>',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => 'Newsletter Sidebar',
		'id'            => 'sidebar_newsletter',
		'description'   => 'This widget appears on the newsletter page if using a theme with a sidebar',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3>',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => 'Article Sidebar',
		'id'            => 'sidebar_article',
		'description'   => 'This widget appears on the article page if using a theme with a sidebar',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3>',
		'after_title'   => '</h3>',
	) );

}
endif;
add_action( 'widgets_init', 'ff_sidebars_init' );

if ( ! function_exists( 'ff_widgets_init' ) ) :
/**
 * Remove selected default widgets and register custom widgets.
 */
function ff_widgets_init() {
	// Remove selected default widgets
	unregister_widget('WP_Widget_Pages');
	unregister_widget('WP_Widget_Calendar');
	unregister_widget('WP_Widget_Archives');
	unregister_widget('WP_Widget_Links');
	unregister_widget('WP_Widget_Meta');
//	unregister_widget('WP_Widget_Search');
	unregister_widget('WP_Widget_Text');
	unregister_widget('WP_Widget_Categories');
	unregister_widget('WP_Widget_Recent_Posts');
	unregister_widget('WP_Widget_Recent_Comments');
	unregister_widget('WP_Widget_RSS');
	unregister_widget('WP_Widget_Tag_Cloud');
	//unregister_widget('WP_Nav_Menu_Widget');

	// Register custom widgets
	//require( get_template_directory() . '/inc/widgets/ff_twitter_widget.php' );
	//register_widget( 'ff_twitter_widget' );
}
endif;
add_action( 'widgets_init', 'ff_widgets_init' );
