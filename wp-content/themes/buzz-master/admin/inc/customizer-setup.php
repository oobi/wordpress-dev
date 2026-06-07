<?php
/**
 * Firefly Customizer support
 *
 * @package WordPress
 * @subpackage Firefly
 * @since Firefly 1.0
 */

/**
 * Implement Customizer additions and adjustments.
 *
 * @since Firefly 1.0
 *
 * @param WP_Customize_Manager $wp_customize Customizer object.
 */
if(!function_exists('ff_customize_register')) :
function ff_customize_register( $wp_customize ) {

	// check if dependency plugins are active
	$is_print_active	= defined( 'BUZZ_ADDON_PRINT_VIEW' );
	$is_email_active 	= defined( 'BUZZ_ADDON_EMAIL_VIEW' );

	// define transport type for colours and link underline
	// 'refresh' for debugging
	$transport = 'postMessage';

	// default colours
	$default_colors = ff_get_theme_default_css_tokens()['color'];


	/****************************************************************
	 * LOAD AND REGISTER CUSTOM CONTROLS
	 ****************************************************************/

    require_once( trailingslashit( get_template_directory() ) . 'admin/inc/custom-radio-image-control.php' );
    $wp_customize->register_control_type( 'FF_Custom_Radio_Image_Control' );


	/****************************************************************
	 * REMOVE UNUSED SECTIONS/CONTROLS
	 ****************************************************************/

	/* static front page section */
	$wp_customize->remove_section('static_front_page');

	/* header text colour default setting */
	$wp_customize->remove_control('header_textcolor');


	/****************************************************************
	 * THEME LAYOUT
	 ****************************************************************/

	// add Theme Layout panel
	$wp_customize->add_panel( 'ff_theme_layout', array( 
		'title' 		=> __( 'Theme Layout', 'ff_newsletter' ),
		'description'	=> __( 'Mix and match sections to create a custom theme layout.', 'ff_newsletter' ),
    	'priority'		=> 10
	) );


	// Header
	$wp_customize->add_section( 'ff_theme_header', array(
		'title' 	=> __('Header', 'ff_newsletter'),
	    'description' => __( 'Customise the order of the Navbar and Featured Image that appear on the home page.', 'ff_newsletter' ),
    	//'priority'	=> 80,
		'panel' 	=> 'ff_theme_layout'
	) );

	$wp_customize->add_setting( 'ff_theme_header_layout' );
	$wp_customize->add_control( new FF_Custom_Radio_Image_Control( $wp_customize, 'ff_theme_header_layout', array(
		'label'          => __( 'Header Layout', 'ff_newsletter' ),
		'section'        => 'ff_theme_header',
		'settings'       => 'ff_theme_header_layout',
		'type'           => 'radioimage',  	   // for some wordpress reason this type can't match the $type in FF_Custom_Radio_Image_Control
											   // This value just overrides the class public var $type
											   // no idea why but it breaks if we try to make it match
											   // if you DON'T send it through then the field won't render
											   // TODO: try and work this out
		'choices'        => array(
			'image-first'	=> '<img src="' . get_template_directory_uri() . '/admin/images/customizer/header/image-first.png" alt="' . __( 'Featured Image above the Navbar', 'ff_newsletter' ) . '" title="' . __( 'Featured Image above the Navbar', 'ff_newsletter' ) . '">',
			'image-second'	=> '<img src="' . get_template_directory_uri() . '/admin/images/customizer/header/image-second.png" alt="' . __( 'Featured Image beneath the Navbar', 'ff_newsletter' ) . '" title="' . __( 'Featured Image beneath the Navbar', 'ff_newsletter' ) . '">',
			'image-none'	=> '<img src="' . get_template_directory_uri() . '/admin/images/customizer/header/image-none.png" alt="' . __( 'No Featured Image', 'ff_newsletter' ) . '" title="' . __( 'No Featured Image', 'ff_newsletter' ) . '">',
		)
	) ) );


	// Articles
	$wp_customize->add_section( 'ff_theme_articles', array(
		'title' 	=> __('Articles', 'ff_newsletter'),
	    'description' => __( 'Choose the layout of the Articles.', 'ff_newsletter' ),
    	//'priority'	=> 80,
		'panel' 	=> 'ff_theme_layout'
	) );

	$wp_customize->add_setting( 'ff_theme_articles_layout' );
	$wp_customize->add_control( new FF_Custom_Radio_Image_Control( $wp_customize, 'ff_theme_articles_layout', array(
		'label'          => __( 'Articles Layout', 'ff_newsletter' ),
		'section'        => 'ff_theme_articles',
		'settings'       => 'ff_theme_articles_layout',
		'type'           => 'radioimage',  	   // for some wordpress reason this type can't match the $type in FF_Custom_Radio_Image_Control
											   // This value just overrides the class public var $type
											   // no idea why but it breaks if we try to make it match
											   // if you DON'T send it through then the field won't render
											   // TODO: try and work this out
		'choices'        => array(
			'featured-top'	=> '<img src="' . get_template_directory_uri() . '/admin/images/customizer/articles/featured-top.png" alt="' . __( 'Featured Articles on top', 'ff_newsletter' ) . '" title="' . __( 'Featured Articles on top', 'ff_newsletter' ) . '">',
			'featured-left'	=> '<img src="' . get_template_directory_uri() . '/admin/images/customizer/articles/featured-left.png" alt="' . __( 'Featured Articles to the left', 'ff_newsletter' ) . '" title="' . __( 'Featured Articles to the left', 'ff_newsletter' ) . '">',
			'featured-right'=> '<img src="' . get_template_directory_uri() . '/admin/images/customizer/articles/featured-right.png" alt="' . __( 'Featured Articles to the right', 'ff_newsletter' ) . '" title="' . __( 'Featured Articles to the right', 'ff_newsletter' ) . '">',
		)
	) ) );


	// Featured Articles
	$wp_customize->add_section( 'ff_theme_featured_articles', array(
		'title' 	=> __('Featured Articles', 'ff_newsletter'),
	    'description' => __( 'Choose the layout and content of the Featured Articles.', 'ff_newsletter' ),
    	//'priority'	=> 80,
		'panel' 	=> 'ff_theme_layout'
	) );

	$wp_customize->add_setting( 'ff_theme_featured_articles_layout' );
	$wp_customize->add_control( new FF_Custom_Radio_Image_Control( $wp_customize, 'ff_theme_featured_articles_layout', array(
		'label'          => __( 'Featured Articles Layout', 'ff_newsletter' ),
		'section'        => 'ff_theme_featured_articles',
		'settings'       => 'ff_theme_featured_articles_layout',
		'type'           => 'radioimage',  	   // for some wordpress reason this type can't match the $type in FF_Custom_Radio_Image_Control
											   // This value just overrides the class public var $type
											   // no idea why but it breaks if we try to make it match
											   // if you DON'T send it through then the field won't render
											   // TODO: try and work this out
		'choices'        => array(
			'double-2-col-text'	=> '<img src="' . get_template_directory_uri() . '/admin/images/customizer/articles/featured-double-2-col-text.png" alt="' . __( 'Two Articles in Two Columns with no Featured Image', 'ff_newsletter' ) . '" title="' . __( 'Two Articles in Two Columns with no Featured Image', 'ff_newsletter' ) . '">',
			'double-2-col'		=> '<img src="' . get_template_directory_uri() . '/admin/images/customizer/articles/featured-double-2-col.png" alt="' . __( 'Two Articles in Two Columns', 'ff_newsletter' ) . '" title="' . __( 'Two Articles in Two Columns', 'ff_newsletter' ) . '">',
			'double-2-col-alt'	=> '<img src="' . get_template_directory_uri() . '/admin/images/customizer/articles/featured-double-2-col-alt.png" alt="' . __( 'Two Articles in Two Columns with overlayed text', 'ff_newsletter' ) . '" title="' . __( 'Two Articles in Two Columns with overlayed text', 'ff_newsletter' ) . '">',
			'single-2-col'		=> '<img src="' . get_template_directory_uri() . '/admin/images/customizer/articles/featured-single-2-col.png" alt="' . __( 'One Article in Two Columns', 'ff_newsletter' ) . '" title="' . __( 'One Article in Two Columns', 'ff_newsletter' ) . '">',
			'single-2-col-alt'	=> '<img src="' . get_template_directory_uri() . '/admin/images/customizer/articles/featured-single-2-col-alt.png" alt="' . __( 'One Article in Two Columns with background colour', 'ff_newsletter' ) . '" title="' . __( 'One Article in Two Columns with background colour', 'ff_newsletter' ) . '">',
			'single-1-col'		=> '<img src="' . get_template_directory_uri() . '/admin/images/customizer/articles/featured-single-1-col.png" alt="' . __( 'One Article in One Column', 'ff_newsletter' ) . '" title="' . __( 'One Article in One Column', 'ff_newsletter' ) . '">',
		)
	) ) );


	// Index Articles
	$wp_customize->add_section( 'ff_theme_index_articles', array(
		'title' 	=> __('Index Articles', 'ff_newsletter'),
	    'description' => __( 'Choose the layout and content of the Index Articles.', 'ff_newsletter' ),
    	//'priority'	=> 80,
		'panel' 	=> 'ff_theme_layout'
	) );

	$wp_customize->add_setting( 'ff_theme_index_articles_layout' );
	$wp_customize->add_control( new FF_Custom_Radio_Image_Control( $wp_customize, 'ff_theme_index_articles_layout', array(
		'label'          => __( 'Index Articles Layout', 'ff_newsletter' ),
		'section'        => 'ff_theme_index_articles',
		'settings'       => 'ff_theme_index_articles_layout',
		'type'           => 'radioimage',  	   // for some wordpress reason this type can't match the $type in FF_Custom_Radio_Image_Control
											   // This value just overrides the class public var $type
											   // no idea why but it breaks if we try to make it match
											   // if you DON'T send it through then the field won't render
											   // TODO: try and work this out
		'choices'        => array(
			'four-col-grid'			=> '<img src="' . get_template_directory_uri() . '/admin/images/customizer/articles/index-4-col-grid.png" alt="' . __( 'Four Column Grid', 'ff_newsletter' ) . '" title="' . __( 'Four Column Grid', 'ff_newsletter' ) . '">',
			'three-col-grid'		=> '<img src="' . get_template_directory_uri() . '/admin/images/customizer/articles/index-3-col-grid.png" alt="' . __( 'Three Column Grid', 'ff_newsletter' ) . '" title="' . __( 'Three Column Grid', 'ff_newsletter' ) . '">',
			'two-col-grid'			=> '<img src="' . get_template_directory_uri() . '/admin/images/customizer/articles/index-2-col-grid.png" alt="' . __( 'Two Column Grid', 'ff_newsletter' ) . '" title="' . __( 'Two Column Grid', 'ff_newsletter' ) . '">',
			'three-col-alt-grid'	=> '<img src="' . get_template_directory_uri() . '/admin/images/customizer/articles/index-3-col-alt-grid.png" alt="' . __( 'Three Column Grid with alternate full-width row', 'ff_newsletter' ) . '" title="' . __( 'Three Column Grid with alternate full-width row', 'ff_newsletter' ) . '">',
			'two-col-list-highlight'=> '<img src="' . get_template_directory_uri() . '/admin/images/customizer/articles/index-2-col-list-highlight.png" alt="' . __( 'Two Column List with highlighted first row', 'ff_newsletter' ) . '" title="' . __( 'Two Column List with highlighted first row', 'ff_newsletter' ) . '">',
		)
	) ) );

	$wp_customize->add_setting( 'ff_theme_index_articles_image', array(
		'default'        => TRUE
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ff_theme_index_articles_image', array(
		'label'          => __( 'Show featured image on index articles?', 'ff_newsletter' ),
		'section'        => 'ff_theme_index_articles',
		'settings'       => 'ff_theme_index_articles_image',
		'type'           => 'checkbox'
	) ) );

	$wp_customize->add_setting( 'ff_theme_index_articles_excerpt', array(
		'default'        => TRUE
	)  );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ff_theme_index_articles_excerpt', array(
		'label'          => __( 'Show excerpt on index articles?', 'ff_newsletter' ),
		'section'        => 'ff_theme_index_articles',
		'settings'       => 'ff_theme_index_articles_excerpt',
		'type'           => 'checkbox'
	) ) );


	// Widget Area
	$wp_customize->add_section( 'ff_theme_layout_widget_area', array(
		'title' 	=> __('Footer Widget Area', 'ff_newsletter'),
	    'description' => __( 'Select the configuration of widgets that appear in the Footer Widget Area.', 'ff_newsletter' ),
    	//'priority'	=> 80,
		'panel' 	=> 'ff_theme_layout'
	) );
	$wp_customize->add_setting( 'ff_widget_layout' );
	$wp_customize->add_control( new FF_Custom_Radio_Image_Control( $wp_customize, 'ff_widget_layout', array(
		'label'          => __( 'Widget Area Layout', 'ff_newsletter' ),
		'section'        => 'ff_theme_layout_widget_area',
		'settings'       => 'ff_widget_layout',
		'type'           => 'radioimage',  	   // for some wordpress reason this type can't match the $type in FF_Custom_Radio_Image_Control
											   // This value just overrides the class public var $type
											   // no idea why but it breaks if we try to make it match
											   // if you DON'T send it through then the field won't render
											   // TODO: try and work this out
		'choices'        => array(
			'col-12'		=> '<img src="' . get_template_directory_uri() . '/admin/images/customizer/widgets/12.png" alt="' . __( '1 Column', 'ff_newsletter' ) . '" title="' . __( '1 Column', 'ff_newsletter' ) . '">',
			'col-6-6'		=> '<img src="' . get_template_directory_uri() . '/admin/images/customizer/widgets/6-6.png" alt="' . __( '2 Columns - Even width', 'ff_newsletter' ) . '" title="' . __( '2 Columns - Even width', 'ff_newsletter' ) . '">',
			'col-9-3'		=> '<img src="' . get_template_directory_uri() . '/admin/images/customizer/widgets/9-3.png" alt="' . __( '2 Columns - Large Left', 'ff_newsletter' ) . '" title="' . __( '2 Columns - Large Left', 'ff_newsletter' ) . '">',
			'col-3-9'		=> '<img src="' . get_template_directory_uri() . '/admin/images/customizer/widgets/3-9.png" alt="' . __( '2 Columns - Large Right', 'ff_newsletter' ) . '" title="' . __( '2 Columns - Large Right', 'ff_newsletter' ) . '">',
			'col-6-3-3'		=> '<img src="' . get_template_directory_uri() . '/admin/images/customizer/widgets/6-3-3.png" alt="' . __( '3 Columns - Large Left', 'ff_newsletter' ) . '" title="' . __( '3 Columns - Large Left', 'ff_newsletter' ) . '">',
			'col-3-3-6'		=> '<img src="' . get_template_directory_uri() . '/admin/images/customizer/widgets/3-3-6.png" alt="' . __( '3 Columns - Large Right', 'ff_newsletter' ) . '" title="' . __( '3 Columns - Large Right', 'ff_newsletter' ) . '">',
			'col-3-6-3'		=> '<img src="' . get_template_directory_uri() . '/admin/images/customizer/widgets/3-6-3.png" alt="' . __( '3 Columns - Large Middle', 'ff_newsletter' ) . '" title="' . __( '3 Columns - Large Middle', 'ff_newsletter' ) . '">',
			'col-3-3-3-3'	=> '<img src="' . get_template_directory_uri() . '/admin/images/customizer/widgets/3-3-3-3.png" alt="' . __( '4 Columns', 'ff_newsletter' ) . '" title="' . __( '4 Columns', 'ff_newsletter' ) . '">'
		)
	) ) );

	/****************************************************************
	 * ADD NEW SECTIONS
	 ****************************************************************/

	








	// Alternative Headers Section
	$wp_customize->add_section( 'ff_alt_headers', array(
		'title' 	=> __('Alternative Headers', 'ff_newsletter'),
	    'description' => __( 'Alternative header images used in the Print, Mobile and Email.', 'ff_newsletter' ),
    	'priority'	=> 60
	) );

	// Print Header Background
	if( $is_print_active ) {
		$wp_customize->add_setting( 'ff_print_header_image' );
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'ff_print_header_image', array(
		    'label'    => __( 'Print Header', 'ff_newsletter' ),
		    'description' => __( 'Recommended print header size: <b>720 x 120</b> pixels. Print-friendly colours also recommended.', 'ff_newsletter' ),
		    'section'  => 'ff_alt_headers',
		    'settings' => 'ff_print_header_image'
		) ) );
	}

	// Mobile Header Background
	$wp_customize->add_setting( 'ff_mobile_header_image' );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'ff_mobile_header_image', array(
	    'label'    => __( 'Mobile Header', 'ff_newsletter' ),
	    'description' => __( 'Recommended mobile header size: <b>720 x 120</b> pixels', 'ff_newsletter' ),
	    'section'  => 'ff_alt_headers',
	    'settings' => 'ff_mobile_header_image'
	) ) );

	// Email Header Background
	if( $is_email_active ) {
		$wp_customize->add_setting( 'ff_email_header_image' );
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'ff_email_header_image', array(
		    'label'    => __( 'Email Header', 'ff_newsletter' ),
		    'description' => __( 'Recommended email header size: <b>640 x 120</b> pixels', 'ff_newsletter' ),
		    'section'  => 'ff_alt_headers',
		    'settings' => 'ff_email_header_image'
		) ) );
	}

	// Social Media Sharing
	$wp_customize->add_section( 'ff_social_sharing', array(
		'title' 	=> __( 'Social Media', 'ff_newsletter' ),
		'description'=> __( 'Enable social media options to allow Issues and Articles to be shared on the selected platforms.', 'ff_newsletter' ),
    	'priority'	=> 80
	) );

	// print
	if( $is_print_active ) {
		$wp_customize->add_setting( 'ff_social_sharing_print' );
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ff_social_sharing_print', array(
			'label'          => __( 'Print', 'ff_newsletter' ),
			'section'        => 'ff_social_sharing',
			'settings'       => 'ff_social_sharing_print',
			'type'           => 'checkbox'
		) ) );
	}

	// facebook
	$wp_customize->add_setting( 'ff_social_sharing_facebook' );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ff_social_sharing_facebook', array(
		'label'          => __( 'Facebook Sharing', 'ff_newsletter' ),
		'section'        => 'ff_social_sharing',
		'settings'       => 'ff_social_sharing_facebook',
		'type'           => 'checkbox'
	) ) );

	// twitter
	$wp_customize->add_setting( 'ff_social_sharing_twitter' );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ff_social_sharing_twitter', array(
		'label'          => __( 'Twitter Sharing', 'ff_newsletter' ),
		'section'        => 'ff_social_sharing',
		'settings'       => 'ff_social_sharing_twitter',
		'type'           => 'checkbox'
	) ) );

	// linkedin
	$wp_customize->add_setting( 'ff_social_sharing_linkedin' );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ff_social_sharing_linkedin', array(
		'label'          => __( 'LinkedIn Sharing', 'ff_newsletter' ),
		'section'        => 'ff_social_sharing',
		'settings'       => 'ff_social_sharing_linkedin',
		'type'           => 'checkbox'
	) ) );

	// social icon background colour
	$wp_customize->add_setting( 'ff_social_icon_bgcolor', array(
		'default' => $default_colors['ff_social_icon_bgcolor'],
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => $transport
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'ff_social_icon_bgcolor', array(
	    'label'    => __( 'Social Icon Background Colour', 'ff_newsletter' ),
	    'section'  => 'ff_social_sharing',
	    'settings' => 'ff_social_icon_bgcolor',
	    'priority' => 20
	) ) );

	// social icon text colour
	$wp_customize->add_setting( 'ff_social_icon_txtcolor', array(
		'default' => $default_colors['ff_social_icon_txtcolor'],
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => $transport
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'ff_social_icon_txtcolor', array(
	    'label'    => __( 'Social Icon Text Colour', 'ff_newsletter' ),
	    'section'  => 'ff_social_sharing',
	    'settings' => 'ff_social_icon_txtcolor',
	    'priority' => 20
	) ) );

	// enable brand colours (used instead of specified colours)
	$wp_customize->add_setting( 'ff_social_sharing_brandcolors' );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ff_social_sharing_brandcolors', array(
		'label'    => __( 'Use default brand colours for icons', 'ff_newsletter' ),
		'section'  => 'ff_social_sharing',
		'settings' => 'ff_social_sharing_brandcolors',
		'type'     => 'checkbox',
	    'priority' => 20
	) ) );

	// Footer
	$wp_customize->add_section( 'ff_footer', array(
		'title' 	=> __('Footer', 'ff_newsletter'),
		'description' 	=> __( 'Customize the content of the newsletter footer.', 'ff_newsletter' ),
    	'priority'	=> 80
	) );
	$wp_customize->add_setting( 'ff_copyright_text' );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ff_copyright_text', array(
	    'label'    	=> __( 'Copyright Text', 'ff_newsletter' ),
	    'section'  	=> 'ff_footer',
	    'settings' 	=> 'ff_copyright_text',
	    'type'		=> 'text'
	) ) );

	// Footer
	$wp_customize->add_section( 'ff_advanced', array(
		'title' 	=> __('Advanced', 'ff_newsletter'),
		'description' 	=> __( 'Advanced styling options. Do not alter unless you know what you\'re doing.', 'ff_newsletter' ),
    	'priority'	=> 90
	) );
	$wp_customize->add_setting( 'ff_custom_css', array(
		'sanitize_callback' => 'ff_sanitize_text_field'
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ff_custom_css', array(
	    'label'    	=> __( 'Custom CSS', 'ff_newsletter' ),
	    'section'  	=> 'ff_advanced',
	    'settings' 	=> 'ff_custom_css',
	    'type'		=> 'textarea'
	) ) );

	/****************************************************************
	 * NAVBAR / FONTS SECTION
	 ****************************************************************/

	// Navbar / fonts
	$wp_customize->add_section( 'ff_navbar_fonts', array(
		'title' 	=> __('Navbar & Fonts', 'ff_newsletter'),
	    'description' => __( 'Specify navbar settings and custom typefaces.', 'ff_newsletter' ),
    	'priority'	=> 60
	) );

	// text transform for navigation (uppercase/none)
	$wp_customize->add_setting( 'ff_nav_font_transform', array(
		'default' 	=> TRUE
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ff_nav_font_transform', array(
		'label'          => __( 'Uppercase navigation?', 'ff_newsletter' ),
		'section'        => 'ff_navbar_fonts',
		'settings'       => 'ff_nav_font_transform',
		'type'           => 'checkbox'
	) ) );

	// issue title display
	$wp_customize->add_setting( 'ff_issue_title_display', array(
		'default' 	=> TRUE
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ff_issue_title_display', array(
		'label'          => __( 'Display Issue Title?', 'ff_newsletter' ),
		'section'        => 'ff_navbar_fonts',
		'settings'       => 'ff_issue_title_display',
		'type'           => 'checkbox'
	) ) );

	// date format
	$today = time();
	$wp_customize->add_setting( 'ff_date_format' );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ff_date_format', array(
		'label'          => __( 'Date Format', 'ff_newsletter' ),
		'section'        => 'ff_navbar_fonts',
		'settings'       => 'ff_date_format',
		'type'           => 'select',
		'choices'		 => array(
			'd M Y' 	=> date( 'd M Y', $today ),
			'd/m/y' 	=> date( 'd/m/y', $today ),
			'l, d M Y' 	=> date( 'l, d M Y', $today ),
			'F Y' 		=> date( 'F Y', $today )
		)
	) ) );

	// google font URL
	$wp_customize->add_setting( 'ff_google_font_url', array(
		'default'           => '',
		'sanitize_callback' => 'ff_sanitize_text_field'
	));
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ff_google_font_url', array(
	    'label'    	=> __( 'Google Font @import', 'ff_newsletter' ),
	    'description' => __( 'Browse for fonts at <a href="https://www.google.com/fonts" target="_blank">Google Fonts</a>. Copy and paste the whole @import statement<br><small>e.g. @import url(...)</small>', 'ff_newsletter' ),
	    'section'  	=> 'ff_navbar_fonts',
	    'settings' 	=> 'ff_google_font_url',
	    'type'		=> 'text'
	) ) );

	$wp_customize->add_setting( 'ff_body_font', array(
		'sanitize_callback' => 'ff_sanitize_text_field'
	));
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ff_body_font', array(
	    'label'    	=> __( 'Body Font Family', 'ff_newsletter' ),
	    'description' => __( 'This definition will set all fonts. You may use the other settings below to further customise specific elements. <br><small>e.g. Arial, Helvetica, sans-serif</small>', 'ff_newsletter' ),
	    'section'  	=> 'ff_navbar_fonts',
	    'settings' 	=> 'ff_body_font',
	    'type'		=> 'text'
	) ) );

	$wp_customize->add_setting( 'ff_heading_font', array(
		'sanitize_callback' => 'ff_sanitize_text_field'
	));
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ff_heading_font', array(
	    'label'    	=> __( 'Heading Font Family', 'ff_newsletter' ),
	    'description' => __( 'Override font for headings <br><small>e.g. Arial, Helvetica, sans-serif</small>', 'ff_newsletter' ),
	    'section'  	=> 'ff_navbar_fonts',
	    'settings' 	=> 'ff_heading_font',
	    'type'		=> 'text'
	) ) );

	$wp_customize->add_setting( 'ff_banner_font', array(
		'sanitize_callback' => 'ff_sanitize_text_field'
	));
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ff_banner_font', array(
	    'label'    	=> __( 'Banner Font Family', 'ff_newsletter' ),
	    'description' => __( 'Override the font for the newsletter header. This is only shown if you are not using an image banner.<br><small>e.g. Arial, Helvetica, sans-serif</small>', 'ff_newsletter' ),
	    'section'  	=> 'ff_navbar_fonts',
	    'settings' 	=> 'ff_banner_font',
	    'type'		=> 'text'
	) ) );

	$wp_customize->add_setting( 'ff_nav_font', array(
		'sanitize_callback' => 'ff_sanitize_text_field'
	));
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ff_nav_font', array(
	    'label'    	=> __( 'Navigation Font Family', 'ff_newsletter' ),
	    'description' => __( 'Override the font for the newsletter navigation bar.<br><small>e.g. Arial, Helvetica, sans-serif</small>', 'ff_newsletter' ),
	    'section'  	=> 'ff_navbar_fonts',
	    'settings' 	=> 'ff_nav_font',
	    'type'		=> 'text'
	) ) );


	/****************************************************************
	 * ADD SETTINGS TO EXISTING SECTIONS
	 ****************************************************************/

	// Add color scheme setting and control.
	$wp_customize->add_setting( 'ff_color_scheme', array(
		'default'           => 'default',
		'sanitize_callback' => 'ff_sanitize_color_scheme',
		'transport'         => $transport
	) );

	$wp_customize->add_control( 'ff_color_scheme', array(
		'label'    => __( 'Base Color Scheme', 'ff_newsletter' ),
		'section'  => 'colors',
		'type'     => 'select',
		'choices'  => ff_get_color_scheme_choices(),
		'priority' => 1,
	) );

	// Add logo field to Header Text & Logo section
	$wp_customize->add_setting( 'ff_newsletter_logo' );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'ff_newsletter_logo', array(
	    'label'    => __( 'Logo', 'ff_newsletter' ),
	    'section'  => 'title_tagline',
	    'settings' => 'ff_newsletter_logo'
	) ) );

	// Add header bg colour to Header Background section
	$wp_customize->add_setting( 'ff_newsletter_header_bgcolor', array(
		'default' => $default_colors['ff_newsletter_header_bgcolor'],
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => $transport
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'ff_newsletter_header_bgcolor', array(
	    'label'    => __( 'Header Background Colour', 'ff_newsletter' ),
	    'section'  => 'header_image',
	    'settings' => 'ff_newsletter_header_bgcolor',
	    'priority' => 0
	) ) );

	// Add header text colour to Header Background section
	$wp_customize->add_setting( 'ff_newsletter_header_txtcolor', array(
		'default' => $default_colors['ff_newsletter_header_txtcolor'],
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => $transport
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'ff_newsletter_header_txtcolor', array(
	    'label'    => __( 'Header Text Colour', 'ff_newsletter' ),
	    'section'  => 'header_image',
	    'settings' => 'ff_newsletter_header_txtcolor',
	    'priority' => 0
	) ) );

	// menu bg colour
	$wp_customize->add_setting( 'ff_newsletter_menu_bgcolor', array(
		'default' => $default_colors['ff_newsletter_menu_bgcolor'],
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => $transport
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'ff_newsletter_menu_bgcolor', array(
	    'label'    => __( 'Menu Background Colour', 'ff_newsletter' ),
	    'section'  => 'colors',
	    'settings' => 'ff_newsletter_menu_bgcolor'
	) ) );

	// menu text colour
	$wp_customize->add_setting( 'ff_newsletter_menu_txtcolor', array(
		'default' => $default_colors['ff_newsletter_menu_txtcolor'],
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => $transport
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'ff_newsletter_menu_txtcolor', array(
	    'label'    => __( 'Menu Text Colour', 'ff_newsletter' ),
	    'section'  => 'colors',
	    'settings' => 'ff_newsletter_menu_txtcolor'
	) ) );

	// heading/link (primary) text colour
	$wp_customize->add_setting( 'ff_newsletter_primary_color', array(
		'default' => $default_colors['ff_newsletter_primary_color'],
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => $transport
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'ff_newsletter_primary_color', array(
	    'label'    => __( 'Content Primary Colour', 'ff_newsletter' ),
	    'section'  => 'colors',
	    'settings' => 'ff_newsletter_primary_color'
	) ) );

	// secondary text colour
	$wp_customize->add_setting( 'ff_newsletter_secondary_color', array(
		'default' => $default_colors['ff_newsletter_secondary_color'],
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => $transport
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'ff_newsletter_secondary_color', array(
	    'label'    => __( 'Content Secondary Colour', 'ff_newsletter' ),
	    'section'  => 'colors',
	    'settings' => 'ff_newsletter_secondary_color'
	) ) );

	// link text colour
	$wp_customize->add_setting( 'ff_newsletter_link_color', array(
		'default' => $default_colors['ff_newsletter_link_color'],
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => $transport
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'ff_newsletter_link_color', array(
	    'label'    => __( 'Content Link Colour', 'ff_newsletter' ),
	    'section'  => 'colors',
	    'settings' => 'ff_newsletter_link_color'
	) ) );

	// link underline style
	$wp_customize->add_setting( 'ff_newsletter_link_decoration', array(
		'default' 	=> TRUE,
		'transport' => $transport
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ff_newsletter_link_decoration', array(
		'label'          => __( 'Underline Links?', 'ff_newsletter' ),
		'section'        => 'colors',
		'settings'       => 'ff_newsletter_link_decoration',
		'type'           => 'checkbox'
	) ) );

	// widgets bg colour
	$wp_customize->add_setting( 'ff_newsletter_widgets_bgcolor', array(
		'default' => $default_colors['ff_newsletter_widgets_bgcolor'],
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => $transport
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'ff_newsletter_widgets_bgcolor', array(
	    'label'    => __( 'Widget Area Background Colour', 'ff_newsletter' ),
	    'section'  => 'colors',
	    'settings' => 'ff_newsletter_widgets_bgcolor'
	) ) );

	// widgets text colour
	$wp_customize->add_setting( 'ff_newsletter_widgets_txtcolor', array(
		'default' => $default_colors['ff_newsletter_widgets_txtcolor'],
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => $transport
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'ff_newsletter_widgets_txtcolor', array(
	    'label'    => __( 'Widget Area Text Colour', 'ff_newsletter' ),
	    'section'  => 'colors',
	    'settings' => 'ff_newsletter_widgets_txtcolor'
	) ) );

	// footer bg colour
	$wp_customize->add_setting( 'ff_newsletter_footer_bgcolor', array(
		'default' => $default_colors['ff_newsletter_footer_bgcolor'],
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => $transport
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'ff_newsletter_footer_bgcolor', array(
	    'label'    => __( 'Footer Background Colour', 'ff_newsletter' ),
	    'section'  => 'colors',
	    'settings' => 'ff_newsletter_footer_bgcolor'
	) ) );

	// footer text colour
	$wp_customize->add_setting( 'ff_newsletter_footer_txtcolor', array(
		'default' => $default_colors['ff_newsletter_footer_txtcolor'],
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => $transport
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'ff_newsletter_footer_txtcolor', array(
	    'label'    => __( 'Footer Text Colour', 'ff_newsletter' ),
	    'section'  => 'colors',
	    'settings' => 'ff_newsletter_footer_txtcolor'
	) ) );

	/* EDIT TEXT IN EXISTING SECTIONS/SETTINGS */
	// Edit field labels and section descriptions in order to make options extra clear.

	// Header Text & Logo
	$wp_customize->get_section( 'title_tagline' )->title 			= __( 'Title &amp; Logo', 'ff_newsletter' );
	$wp_customize->get_section( 'title_tagline' )->description 		= __( 'Change the Newsletter title/tagline and upload a logo to customise the header.', 'ff_newsletter' );
	$wp_customize->get_control( 'blogname' )->label 				= __( 'Newsletter Title', 'ff_newsletter', 'ff_newsletter' );
	$wp_customize->get_control( 'display_header_text' )->label 		= __( 'Display Title, Tagline &amp; Logo', 'ff_newsletter' );
	$wp_customize->get_control( 'display_header_text' )->priority 	= 0;
	$wp_customize->get_control( 'ff_newsletter_logo' )->description = __( 'Logo image should be no more than <b>150px</b> tall.', 'ff_newsletter' );

	// Header Background
	$wp_customize->get_section( 'header_image' )->title 			= __( 'Header', 'ff_newsletter' );
	$wp_customize->get_control( 'header_image' )->priority 			= 10;
	$wp_customize->get_section( 'header_image' )->description		= __( 'Customize the look of the newsletter header.', 'ff_newsletter' );

	// Theme Colours
	$wp_customize->get_section( 'colors' )->priority 				= 70;
	$wp_customize->get_section( 'colors' )->title 					= __( 'Theme Colours', 'ff_newsletter' );
	$wp_customize->get_section( 'colors' )->description				= __( 'Customize the look of the newsletter elements.', 'ff_newsletter' );

	// Theme Background
	$wp_customize->get_control( 'background_color' )->section 		= 'background_image';
	$wp_customize->get_section( 'background_image' )->title 		= __( ' Theme Background', 'ff_newsletter' );
	$wp_customize->get_section( 'background_image' )->description	= __( 'Customize the look of the background.', 'ff_newsletter' );

}
endif;
add_action( 'customize_register', 'ff_customize_register' );


/**
 * Register color schemes for Firefly Newsletter
 *
 * Can be filtered with {@see 'ff_color_schemes'}.
 *
 * The order of colors in a colors array:
 * 1. Main Background Color.
 * 2. Sidebar Background Color.
 * 3. Box Background Color.
 * 4. Main Text and Link Color.
 * 5. Sidebar Text and Link Color.
 * 6. Meta Box Background Color.
 *
 * @since Firefly 1.0
 *
 * @return array An associative array of color scheme options.
 */
function ff_get_color_schemes() {
	$default_colors = ff_get_theme_default_css_tokens()['color'];

	$schemes = array(
		'default' => array(
			'label'  => __( 'Default', 'ff_newsletter' ),
			'colors' => $default_colors
		),
		// CORPORATE
		'corporate'    => array(
			'label'  => __( 'Corporate', 'ff_newsletter' ),
			'colors' => array(
				'background_color'				=> 'cccccc',
				'ff_newsletter_header_bgcolor'		=> '2a4182',
				'ff_newsletter_header_txtcolor'		=> 'FFFFFF',
				'ff_newsletter_menu_bgcolor'		=> '1e2441',
				'ff_newsletter_menu_txtcolor'		=> 'b2b7d2',
				'ff_newsletter_primary_color' 		=> '2a4182',
				'ff_newsletter_secondary_color' 	=> '999999',
				'ff_newsletter_link_color' 			=> '2a4182',
				'ff_social_icon_bgcolor' 			=> '2a4182',
				'ff_social_icon_txtcolor' 			=> 'FFFFFF',
				'ff_newsletter_widgets_bgcolor'		=> '999999',
				'ff_newsletter_widgets_txtcolor' 	=> 'FFFFFF',
				'ff_newsletter_footer_bgcolor' 		=> '333333',
				'ff_newsletter_footer_txtcolor' 	=> '858585'
			)
		),
		// COLOURFUL
		'colourful'    => array(
			'label'  => __( 'Colourful', 'ff_newsletter' ),
			'colors' => array(
				'background_color'				=> 'fdf0d5',
				'ff_newsletter_header_bgcolor'		=> 'd81e5b',
				'ff_newsletter_header_txtcolor'		=> 'FFFFFF',
				'ff_newsletter_menu_bgcolor'		=> '7d9303',
				'ff_newsletter_menu_txtcolor'		=> 'FFFFFF',
				'ff_newsletter_primary_color' 		=> 'd81e5b',
				'ff_newsletter_secondary_color' 	=> '999999',
				'ff_newsletter_link_color' 			=> 'fa8545',
				'ff_social_icon_bgcolor' 			=> 'fa8545',
				'ff_social_icon_txtcolor' 			=> 'FFFFFF',
				'ff_newsletter_widgets_bgcolor'		=> 'fa8545',
				'ff_newsletter_widgets_txtcolor' 	=> 'FFFFFF',
				'ff_newsletter_footer_bgcolor' 		=> 'FFFFFF',
				'ff_newsletter_footer_txtcolor' 	=> '666666'
			)
		),
		// DARK
		'dark'    => array(
			'label'  => __( 'Dark', 'ff_newsletter' ),
			'colors' => array(
				'background_color'				=> '000000',
				'ff_newsletter_header_bgcolor'		=> '333333',
				'ff_newsletter_header_txtcolor'		=> 'FFFFFF',
				'ff_newsletter_menu_bgcolor'		=> '666666',
				'ff_newsletter_menu_txtcolor'		=> 'FFFFFF',
				'ff_newsletter_primary_color' 		=> '333333',
				'ff_newsletter_secondary_color' 	=> '999999',
				'ff_newsletter_link_color' 			=> '333333',
				'ff_social_icon_bgcolor' 			=> '333333',
				'ff_social_icon_txtcolor' 			=> 'FFFFFF',
				'ff_newsletter_widgets_bgcolor'		=> '666666',
				'ff_newsletter_widgets_txtcolor' 	=> 'FFFFFF',
				'ff_newsletter_footer_bgcolor' 		=> '000000',
				'ff_newsletter_footer_txtcolor' 	=> 'FFFFFF'
			)
		),
		// LIGHT
		'light'    => array(
			'label'  => __( 'Light', 'ff_newsletter' ),
			'colors' => array(
				'background_color'				=> 'f0ecec',
				'ff_newsletter_header_bgcolor'		=> 'FFFFFF',
				'ff_newsletter_header_txtcolor'		=> '5e7186',
				'ff_newsletter_menu_bgcolor'		=> 'c8cacd',
				'ff_newsletter_menu_txtcolor'		=> '313a44',
				'ff_newsletter_primary_color' 		=> '5e7186',
				'ff_newsletter_secondary_color' 	=> 'c8cacd',
				'ff_newsletter_link_color' 			=> '5e7186',
				'ff_social_icon_bgcolor' 			=> '5e7186',
				'ff_social_icon_txtcolor' 			=> 'FFFFFF',
				'ff_newsletter_widgets_bgcolor'		=> 'f0efef',
				'ff_newsletter_widgets_txtcolor' 	=> '5e7186',
				'ff_newsletter_footer_bgcolor' 		=> 'FFFFFF',
				'ff_newsletter_footer_txtcolor' 	=> '6e7985'
			)
		),
		// COLLEGE
		'college'    => array(
			'label'  => __( 'Sophisticated', 'ff_newsletter' ),
			'colors' => array(
				'background_color'				=> 'e0ded7',
				'ff_newsletter_header_bgcolor'		=> 'ef4c4c',
				'ff_newsletter_header_txtcolor'		=> 'FFFFFF',
				'ff_newsletter_menu_bgcolor'		=> '333333',
				'ff_newsletter_menu_txtcolor'		=> 'FFFFFF',
				'ff_newsletter_primary_color' 		=> '333333',
				'ff_newsletter_secondary_color' 	=> '999999',
				'ff_newsletter_link_color' 			=> 'ef4c4c',
				'ff_social_icon_bgcolor' 			=> 'ef4c4c',
				'ff_social_icon_txtcolor' 			=> 'FFFFFF',
				'ff_newsletter_widgets_bgcolor'		=> 'CCCCCC',
				'ff_newsletter_widgets_txtcolor' 	=> '333333',
				'ff_newsletter_footer_bgcolor' 		=> '333333',
				'ff_newsletter_footer_txtcolor' 	=> '666666'
			)
		),
		// GARDEN
		'garden'    => array(
			'label'  => __( 'Fresh', 'ff_newsletter' ),
			'colors' => array(
				'background_color'				=> 'f7ebcd',
				'ff_newsletter_header_bgcolor'		=> '3aa9a8',
				'ff_newsletter_header_txtcolor'		=> 'FFFFFF',
				'ff_newsletter_menu_bgcolor'		=> 'FFFFFF',
				'ff_newsletter_menu_txtcolor'		=> '333333',
				'ff_newsletter_primary_color' 		=> '333333',
				'ff_newsletter_secondary_color' 	=> 'f9c648',
				'ff_newsletter_link_color' 			=> '349f9e',
				'ff_social_icon_bgcolor' 			=> 'F9C648',
				'ff_social_icon_txtcolor' 			=> 'FFFFFF',
				'ff_newsletter_widgets_bgcolor'		=> 'c0f4f4',
				'ff_newsletter_widgets_txtcolor' 	=> '333333',
				'ff_newsletter_footer_bgcolor' 		=> 'FFFFFF',
				'ff_newsletter_footer_txtcolor' 	=> '666666'
			)
		),
		// EVENTS
		'events'    => array(
			'label'  => __( 'Contemporary', 'ff_newsletter' ),
			'colors' => array(
				'background_color'				=> '000000',
				'ff_newsletter_header_bgcolor'		=> '1ea6d8',
				'ff_newsletter_header_txtcolor'		=> 'FFFFFF',
				'ff_newsletter_menu_bgcolor'		=> '59595a',
				'ff_newsletter_menu_txtcolor'		=> 'FFFFFF',
				'ff_newsletter_primary_color' 		=> '333333',
				'ff_newsletter_secondary_color' 	=> 'bababa',
				'ff_newsletter_link_color' 			=> '1ea6d8',
				'ff_social_icon_bgcolor' 			=> '1ea6d8',
				'ff_social_icon_txtcolor' 			=> 'FFFFFF',
				'ff_newsletter_widgets_bgcolor'		=> 'cfe7f3',
				'ff_newsletter_widgets_txtcolor' 	=> '333333',
				'ff_newsletter_footer_bgcolor' 		=> '59595a',
				'ff_newsletter_footer_txtcolor' 	=> '999999'
			)
		),
		// TRAVEL
		'travel'    => array(
			'label'  => __( 'Bright', 'ff_newsletter' ),
			'colors' => array(
				'background_color'				=> 'FFFFFF',
				'ff_newsletter_header_bgcolor'		=> '01cbb5',
				'ff_newsletter_header_txtcolor'		=> 'FFFFFF',
				'ff_newsletter_menu_bgcolor'		=> '4b4b4b',
				'ff_newsletter_menu_txtcolor'		=> 'FFFFFF',
				'ff_newsletter_primary_color' 		=> '2e9114',
				'ff_newsletter_secondary_color' 	=> 'bababa',
				'ff_newsletter_link_color' 			=> '000000',
				'ff_social_icon_bgcolor' 			=> '01cbb5',
				'ff_social_icon_txtcolor' 			=> 'FFFFFF',
				'ff_newsletter_widgets_bgcolor'		=> 'edeeee',
				'ff_newsletter_widgets_txtcolor' 	=> '333333',
				'ff_newsletter_footer_bgcolor' 		=> '4b4b4b',
				'ff_newsletter_footer_txtcolor' 	=> '999999'
			)
		)
	);
	return apply_filters( 'ff_color_schemes', $schemes );
}

if ( ! function_exists( 'ff_get_color_scheme' ) ) :
/**
 * Get the current newsletter color scheme.
 *
 * @since Firefly 1.0
 *
 * @return array An associative array of either the current or default color scheme hex values.
 */
function ff_get_color_scheme() {
	$color_scheme_option = get_theme_mod( 'ff_color_scheme', 'default' );
	$color_schemes       = ff_get_color_schemes();

	if ( array_key_exists( $color_scheme_option, $color_schemes ) ) {
		return $color_schemes[ $color_scheme_option ]['colors'];
	}

	return $color_schemes['default']['colors'];
}
endif; // ff_get_color_scheme

if ( ! function_exists( 'ff_get_color_scheme_choices' ) ) :
/**
 * Returns an array of color scheme choices registered for the newsletter.
 *
 * @since Firefly 1.0
 *
 * @return array Array of color schemes.
 */
function ff_get_color_scheme_choices() {
	$color_schemes                = ff_get_color_schemes();
	$color_scheme_control_options = array();

	foreach ( $color_schemes as $color_scheme => $value ) {
		$color_scheme_control_options[ $color_scheme ] = $value['label'];
	}

	return $color_scheme_control_options;
}
endif; // ff_get_color_scheme_choices

if ( ! function_exists( 'ff_sanitize_color_scheme' ) ) :
/**
 * Sanitization callback for color schemes.
 *
 * @since Firefly 1.0
 *
 * @param string $value Color scheme name value.
 * @return string Color scheme name.
 */
function ff_sanitize_color_scheme( $value ) {
	$color_schemes = ff_get_color_scheme_choices();

	if ( ! array_key_exists( $value, $color_schemes ) ) {
		$value = 'default';
	}

	return $value;
}
endif; // ff_sanitize_color_scheme

if ( ! function_exists( 'ff_customize_control_js' ) ) :
/**
 * Binds JS listener to make Customizer color_scheme control.
 *
 * Passes color scheme data as colorScheme global.
 *
 * @since Twenty Fifteen 1.0
 */
function ff_customize_control_js() {
	wp_enqueue_script( 'ff-color-scheme-control', get_template_directory_uri() . '/js/theme-customizer-color-scheme-control.js', array( 'customize-controls', 'iris', 'underscore', 'wp-util' ), '20141216', true );
	wp_localize_script( 'ff-color-scheme-control', 'colorScheme', ff_get_color_schemes() );
}
endif; // ff_customize_control_js
add_action( 'customize_controls_enqueue_scripts', 'ff_customize_control_js' );

/**
 * Add customizer utility scripts
 *
 * @since Twenty Fifteen 1.0
 */
function ff_customize_utils_js() {
	wp_enqueue_script( 'ff-customize-utils', get_template_directory_uri() . '/admin/js/theme-customizer-utils.js', array( 'customize-preview' ), '20160223', true );
}
add_action( 'customize_preview_init', 'ff_customize_utils_js' );

if ( ! function_exists( 'ff_sanitize_text_field' ) ) :
/**
 * Sanitization callback for text fields
 *
 * @since Firefly 1.0
 *
 * @param string $value Text value
 * @return string Value with tags stripped
 */
function ff_sanitize_text_field( $value ) {
	return strip_tags( $value );
}
endif; // ff_sanitize_text_field
