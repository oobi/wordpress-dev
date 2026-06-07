<?php
/**
 * hide dashboard clutter
 */
function remove_dashboard_widgets(){
	global $wp_meta_boxes;
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
	//unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
}
add_action('wp_dashboard_setup', 'remove_dashboard_widgets');

/**
 * add developer credit to footer of admin pages
 */
function modify_admin_footer () {
  echo 'Created by <a href="http://www.fireflyinteractive.net">Firefly Interactive</a>. ';
  echo 'Powered by <a href="http://WordPress.org">WordPress</a>.';
}
add_filter('admin_footer_text', 'modify_admin_footer');

/**
 * add firefly logo in place of wordpress logo on login screen
 */
function custom_login_logo() {
  echo '<style type="text/css">
    .login h1 a { background-size:auto; width:100%; height:160px; background-position:center center; background-image:url('.get_bloginfo('template_directory').'/images/admin/logo-login.png) !important; background-size:contain; }
    </style>';
}
add_action('login_head', 'custom_login_logo');

/**
 * add excerpt box to pages
 */
function add_page_excerpt_support(){
   add_post_type_support( 'page', 'excerpt' );
}
add_action('admin_init', 'add_page_excerpt_support');

/**
 * disable all default comment options
 */
function override_comments() {
	update_option('default_pingback_flag',  0);
	update_option('default_ping_status', 	0);
	update_option('default_comment_status', 0);
}
add_action('admin_menu', 'override_comments', 999);
