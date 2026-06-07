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
	register_sidebar( array(
		'name' 			=> __( 'Main Sidebar', 'firefly' ),
		'id' 			=> 'sidebar-main',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' 	=> "</div>",
		'before_title' 	=> '<h3 class="widget-title">',
		'after_title' 	=> '</h3>'
	) );

	register_sidebar( array(
		'name' 			=> __( 'Footer', 'firefly' ),
		'id' 			=> 'sidebar-footer',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' 	=> "</div>",
		'before_title' 	=> '<h3 class="widget-title">',
		'after_title' 	=> '</h3>'
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
	unregister_widget('WP_Nav_Menu_Widget');

	// Register custom widgets
	require( get_template_directory() . '/inc/widgets/TextNoWrapper.php' );
	require( get_template_directory() . '/inc/widgets/TextCustomWrapper.php' );
	register_widget( 'TextNoWrapper' );
	register_widget( 'TextCustomWrapper' );
}
endif;
add_action( 'widgets_init', 'ff_widgets_init' );

if ( ! function_exists( 'ff_empty_sidebars' ) ) :
/**
 * Empty sidebars of all widgets to ensure that default widgets are not seen.
 */
function ff_empty_sidebars() {
	global $wp_registered_widgets;
	$sidebars = wp_get_sidebars_widgets();
	$clean    = array();
	foreach($sidebars as $widgets) {
		foreach($widgets as $widget_id) {
			$widget = $wp_registered_widgets[$widget_id];
			if($widget) {
				$obj		 = $widget['callback'][0];
				$instance    = $obj->number;
				$option_name = $obj->option_name;
				$options	 = get_option($option_name); 	// get options of all instances
				unset($options[$instance]);					// remove this instance's options
	  			update_option($option_name, $options);
			}
		}
		array_push($clean, array());
	}
	wp_set_sidebars_widgets($clean);
}
endif;
add_action("theme_introduce", "ff_empty_sidebars");
