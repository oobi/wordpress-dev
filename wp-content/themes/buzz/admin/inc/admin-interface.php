<?php
/**
 * hide dashboard clutter
 */
if(!function_exists('ff_remove_dashboard_widgets')) :
function ff_remove_dashboard_widgets(){
	global $wp_meta_boxes;
	//Right Now - Comments, Posts, Pages at a glance
	//unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
	//Recent Comments
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
	//Incoming Links
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
	//Plugins - Popular, New and Recently updated WordPress Plugins
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);

	//Wordpress Development Blog Feed
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
	//Other WordPress News Feed
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
	//Quick Press Form
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
	//Recent Drafts List
	//unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts']);
}
endif;
add_action('wp_dashboard_setup', 'ff_remove_dashboard_widgets');

/**
 * Add our custom post types to 'at a glace' widget on the dashboard
 */
if(!function_exists('ff_custom_glance_items')) :
function ff_custom_glance_items( $items = array() ) {

    $post_types = array( 'newsletter', 'article' );

    foreach( $post_types as $type ) {

        if( ! post_type_exists( $type ) ) continue;

        $num_posts = wp_count_posts( $type );

        if( $num_posts ) {

            $published = intval( $num_posts->publish );
            $post_type = get_post_type_object( $type );

            $text = _n( '%s ' . $post_type->labels->singular_name, '%s ' . $post_type->labels->name, $published, 'your_textdomain' );
            $text = sprintf( $text, number_format_i18n( $published ) );

            if ( current_user_can( $post_type->cap->edit_posts ) ) {
                $items[] = sprintf( '<a class="%1$s-count" href="edit.php?post_type=%1$s">%2$s</a>', $type, $text ) . "\n";
            } else {
                $items[] = sprintf( '<span class="%1$s-count">%2$s</span>', $type, $text ) . "\n";
            }
        }
    }

    return $items;
}
endif;
add_filter( 'dashboard_glance_items', 'ff_custom_glance_items', 10, 1 );

/**
 * Reduce admin menu clutter
 */
if(!function_exists('ff_hide_menus')) :
function ff_hide_menus() {
  if(!current_user_can('administrator')) {
		//remove_menu_page( 'index.php' );                  //Dashboard
		remove_menu_page( 'edit.php' );                   //Posts
		//remove_menu_page( 'upload.php' );                 //Media
		//remove_menu_page( 'edit.php?post_type=page' );    //Pages
		//remove_menu_page( 'edit-comments.php' );          //Comments
		//remove_menu_page( 'themes.php' );                 //Appearance
		//remove_menu_page( 'plugins.php' );                //Plugins
		//remove_menu_page( 'users.php' );                  //Users
		remove_menu_page( 'tools.php' );                  //Tools
		//remove_menu_page( 'options-general.php' );        //Settings
	}
}
endif;
add_action('admin_head', 'ff_hide_menus');

/**
 * add developer credit to footer of admin pages
 */
if(!function_exists('ff_modify_admin_footer')) :
function ff_modify_admin_footer () {
  echo 'Powered by <a href="http://www.thebuzz.net.au" target="_blank">The Buzz</a> and <a href="http://WordPress.org" target="_blank">WordPress</a>.';
}
endif;
add_filter('admin_footer_text', 'ff_modify_admin_footer');

/**
 * add firefly logo in place of wordpress logo on login screen
 */
if(!function_exists('ff_custom_login_logo')) :
function ff_custom_login_logo() {

	// get image url
	$image_url = esc_url( get_theme_mod( 'ff_newsletter_logo' ) );

	echo '<style type="text/css">';
	if( $image_url ) {

		// store the image ID in a var
		$image_id = ff_get_image_id($image_url);

		// retrieve the 'logo' size of the image
		$image_thumb = wp_get_attachment_image_src($image_id, 'logo');
		$logo_url = $image_thumb[0];

		// get custom theme colours
		$header_bgcolor = get_theme_mod( 'ff_newsletter_header_bgcolor' );
		$header_txtcolor = get_theme_mod( 'ff_newsletter_header_txtcolor' );
		$btn_bgcolor = get_theme_mod( 'ff_newsletter_menu_bgcolor' );
		$btn_txtcolor = get_theme_mod( 'ff_newsletter_menu_txtcolor' );

		// get background image and options
		$bg_image = get_background_image();
		$bg_color = get_theme_mod( 'background_color', get_theme_support( 'custom-background', 'default-color' ) );
		$bg_repeat = get_theme_mod( 'background_repeat', get_theme_support( 'custom-background', 'default-repeat' ) );
		$bg_attachment = get_theme_mod( 'background_attachment', get_theme_support( 'custom-background', 'default-attachment' ) );
		$bg_position = get_theme_mod( 'background_position_x', get_theme_support( 'custom-background', 'default-position-x' ) );

		echo 'HTML 			{ height:auto;
							  background-color:' . $bg_color . ';
							  background-image:url(' . $bg_image . ');
							  background-repeat: ' . $bg_repeat . ';
							  background-position: top ' . $bg_position . ';
							  background-attachment:' . $bg_attachment . ';
							}
			#login 			{ padding:20px 0; margin-top:20px; width:350px;
							  background:' . $header_bgcolor . '; }

			.login 			{ padding:0;
							  background:none; }
			.login h1 		{ position:relative; width:100%; height:120px; margin-bottom:80px;
							  background:url("'. $logo_url .'") center center no-repeat; background-size:contain; }
			.login H1 A 	{ position:absolute; top:100%; text-indent:0; width:100%; height:auto; display:block; margin:0; padding-top:20px;
							  background:none !important; }
			.login A,
			.login A:hover 	{ color: ' . $header_txtcolor . ' !important; }

			.login .button-primary,
			.login .button-primary:hover { background:' . $btn_bgcolor . '; border-color:' . $btn_bgcolor . '; -webkit-box-shadow:none; box-shadow:none;
											color:' . $btn_txtcolor . '; }
			';

	} else {
		echo '.login h1 a { background-size:auto; width:100%; height:180px;
						  background-position:center center; background-image:url('.get_bloginfo('template_directory').'/admin/images/Buzz_180.png) !important; }';
	}
	echo '</style>';

}
endif;
add_action('login_head', 'ff_custom_login_logo');
add_action('password_protected_login_head', 'ff_custom_login_logo');

/**
 * add excerpt box to pages
 */
if(!function_exists('ff_add_page_excerpt_support')) :
function ff_add_page_excerpt_support(){
   add_post_type_support( 'page', 'excerpt' );
}
endif;
add_action('admin_init', 'ff_add_page_excerpt_support');

/**
 * disable all default comment options
 */
if(!function_exists('ff_override_comments')) :
function ff_override_comments() {
	update_option('default_pingback_flag',  0);
	update_option('default_ping_status', 	0);
	update_option('default_comment_status', 0);
}
endif;
add_action('admin_menu', 'ff_override_comments', 999);