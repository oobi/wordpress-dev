<?php
/**
 * Implement Custom Header functionality for Firefly Newsletter Theme
 *
 * @package WordPress
 * @subpackage Firefly
 * @since Firefly 1.0
 */

// if set to TRUE then the CSS will not cache
// this is a development setting
if( !defined( 'FF_NO_CACHE_CUSTOMIZER_CSS' ) ) {
	define('FF_NO_CACHE_CUSTOMIZER_CSS', FALSE);
}

/**
 * Define theme default colours
 * These colours should be used in custom.css like so. The !!...!! tokens are replaced at runtime
 * #something { color: !!ff_newsletter_header_bgcolor!!; }
 *
 * These colours are theme specific
 */
if ( ! function_exists( 'ff_get_theme_default_css_tokens' ) ) :
function ff_get_theme_default_css_tokens() {

	/**********************************************************************************
	 * NOTE: "contrast" is a special keyword.
	 *
	 * It is reserved for tokens that are calculated to provide contrast
	 * with their corresponding colours.
	 *
	 * Eg. 'ff_newsletter_primary_color_contrast' is set to a light
	 * or dark hex colour depending on the lightness of 'ff_newsletter_primary_color'
	 *
	 * Do not use "contrast" in token names not intended to be calculated in this way
	 **********************************************************************************/

	$defaults = array(
		'color' => array(
			'background_color'							=> 'e0ded7',
			// theme colors
			'ff_newsletter_header_bgcolor'				=> 'ef4c4c',
			'ff_newsletter_header_txtcolor'				=> 'FFFFFF',
			'ff_newsletter_menu_bgcolor'				=> '333333',
			'ff_newsletter_menu_txtcolor'				=> 'FFFFFF',
			'ff_newsletter_primary_color' 				=> '333333',
			'ff_newsletter_primary_color_contrast'		=> 'FFFFFF',
			'ff_newsletter_secondary_color' 			=> '999999',
			'ff_newsletter_secondary_color_contrast' 	=> 'FFFFFF',
			'ff_newsletter_link_color' 					=> 'ef4c4c',
			'ff_social_icon_bgcolor' 					=> 'ef4c4c',
			'ff_social_icon_txtcolor' 					=> 'FFFFFF',
			'ff_newsletter_widgets_bgcolor'				=> 'f1f1f1',
			'ff_newsletter_widgets_txtcolor' 			=> '333333',
			'ff_newsletter_footer_bgcolor' 				=> '333333',
			'ff_newsletter_footer_txtcolor' 			=> '666666'
		),
		'bool' => array(
			// link underline
			'ff_newsletter_link_decoration'		=> array('underline', 'none'),
			'ff_nav_font_transform'				=> array('uppercase', 'none')
		),
		'string' => array(
			// 	ff_google_font_url // used in functions.php to embed font
			'ff_body_font'		=> "Arial, Helvetica, sans-serif",
			'ff_heading_font'	=> "'Raleway', Arial, sans-serif",
			'ff_banner_font'	=> "inherit",
			'ff_nav_font'		=> "inherit",
			'ff_nav_font'		=> "inherit"
		)

	);
	return $defaults;
}
endif;

/**
 * Binds JS handlers to make the Customizer preview reload changes asynchronously.
 *
 * @since Twenty Fifteen 1.0
 */
function ff_customize_preview_js() {
	wp_enqueue_script( 'ff-customize-preview', get_template_directory_uri() . '/admin/js/theme-customizer.js', array( 'customize-preview' ), '20170529', true );
}
add_action( 'customize_preview_init', 'ff_customize_preview_js' );

/**
 * Get a default colour definition by KEY from the defaults
 */
if ( ! function_exists( 'ff_get_theme_default_color' ) ) :
function ff_get_theme_default_color($key, $default='') {
	$defaults = ff_get_theme_default_css_tokens();
	if(is_array($defaults) && isset($defaults['color'][$key])) {
		return $defaults['color'][$key];
	} else {
		return $default;
	}
}
endif;


/**
 * Set options for default header background pattern images
 */
if ( ! function_exists( 'ff_get_default_overlay_options' ) ) :
function ff_get_default_overlay_options() {
	return array(
		'overlay_stripes' => array(
			'url'			  => '%s/images/default/default-overlay-1.png',
			'thumbnail_url' => '%s/images/default/default-overlay-1-thumbnail.png',
			'description'	=> _x( 'Diagonal Stripes Overlay', 'ff_newsletter' )
		),
		'overlay_gradient' => array(
			'url'			  => '%s/images/default/default-overlay-2.png',
			'thumbnail_url' => '%s/images/default/default-overlay-2-thumbnail.png',
			'description'	=> _x( 'Gradient Overlay', 'ff_newsletter' )
		)
	);
}
endif;

/**
 * Set up the WordPress core custom header settings.
 *
 * @since Firefly 1.0
 *
 * @uses ff_header_style()
 * @uses ff_admin_header_style()
 * @uses ff_admin_header_image()
 */
if ( ! function_exists( 'ff_custom_header_setup' ) ) :
function ff_custom_header_setup() {
	/**
	 * Filter Firefly custom-header support arguments.
	 *
	 * @since Firefly 1.0
	 *
	 * @param array $args {
	 *	  An array of custom-header support arguments.
	 *
	 *	  @type bool	$header_text				Whether to display custom header text. Default false.
	 *	  @type int	 $width						Width in pixels of the custom header image. Default 1260.
	 *	  @type int	 $height					  Height in pixels of the custom header image. Default 240.
	 *	  @type bool	$flex_height				Whether to allow flexible-height header images. Default true.
	 *	  @type string $admin_head_callback	 Callback function used to style the image displayed in
	 *														the Appearance > Header screen.
	 *	  @type string $admin_preview_callback Callback function used to create the custom header markup in
	 *														the Appearance > Header screen.
	 * }
	 */

	$default_colors = ff_get_theme_default_css_tokens();

	// register default background options
	$defaults = array(
		'default-color'			 => ff_get_theme_default_color('background_color'),
		'default-image'			 => '',
		'wp-head-callback'		 => '_custom_background_cb',
		'admin-head-callback'	 => '',
		'admin-preview-callback' => ''
	);
	add_theme_support( 'custom-background', $defaults );

	// register default banners
	$overlay_options = ff_get_default_overlay_options();
	register_default_headers( $overlay_options );

	// register default banner options
	$defaults = array(
		//'default-image'			 => get_template_directory_uri() . '/images/default/banner.png',
		'width'						=> 1170,
		'height'					  => 120,
		'flex-height'				=> true,
		'flex-width'				 => true,
		/*'random-default'			=> false,
		'header-text'				=> true,
		'uploads'					 => true*/
		'default-text-color'	  => 'FFFFFF'
	);
	add_theme_support( 'custom-header', $defaults );
}
add_action( 'after_setup_theme', 'ff_custom_header_setup' );
endif;


/**
 * Generates and enqueues front-end CSS for customiser settings.
 * Caches the CSS to the theme temp directory
 */
if ( ! function_exists( 'ff_customiser_css' ) ) :
function ff_customiser_css() {
	global $wp_query;

	// use this flag to determine CSS for email view
	$is_email = isset($wp_query->query_vars['email']);

	// path to custom CSS with color definitions
	$custom_css_path 	= get_stylesheet_directory() . '/css/custom.css';

	// get the current blog ID to namespace cache file
	$blog_id = get_current_blog_id();

	// path to generated cached CSS
	if($is_email) {
		$css_cache_path  = WP_CONTENT_DIR . '/cache/buzz/custom-email-' . $blog_id . '.css';
	} else {
		$css_cache_path  = WP_CONTENT_DIR . '/cache/buzz/custom-' . $blog_id . '.css';
	}

	// default CSS output is nothing
	$css = '';

	// if we already have a cache file then load it and we're done
	if(!is_customize_preview() && (!FF_NO_CACHE_CUSTOMIZER_CSS && is_file($css_cache_path) )) {
		$css = file_get_contents($css_cache_path);
	}

	// if not cached we will generate a new one
	else if(is_file($custom_css_path)) {
		$css = file_get_contents($custom_css_path);

		// strip comments and condense - this also gets rid of tokens we aren't using
		$css = preg_replace('!/\*.*?\*/!s', '', $css);
		$css = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $css);

		// get all the tokens from the CSS file
		$tokens	= ff_get_css_tokens($css);

		// get default colours
		$defaults = ff_get_theme_default_css_tokens();

		// generate a set of colour definitions based on our required tokens and defaults
		$colors	= ff_get_theme_custom_css_values($tokens, $defaults);

		foreach($colors as $key=>$value) {
			$css = str_replace('!!'.$key.'!!', $value, $css);
		}

		// email style inliner does not deal with some pseudo classes
		if($is_email) {
			$css = str_replace(":focus", '.__focus', $css);
			$css = str_replace(":before", '.__before', $css);
			$css = str_replace(":after", '.__after', $css);
		}

		// write cache file
		if( ! FF_NO_CACHE_CUSTOMIZER_CSS) {
			$cache_dir = dirname($css_cache_path);
			if(is_dir($cache_dir) || wp_mkdir_p($cache_dir)) {
				$css = '/*Generated at ' . date('j F Y H:i:s e') . "*/\n" . $css;
				file_put_contents($css_cache_path, $css, LOCK_EX);
			}
		}

	}

	// add css as inline stylesheet
	if( !empty( $css ) ) {
		echo '<style type="text/css" media="screen">' . $css . '</style>';
	}

	// Output Custom CSS field
	$newsletter_custom_css = get_theme_mod( 'ff_custom_css' );
	if( !empty( $newsletter_custom_css ) ) {
		echo '<style type="text/css" media="screen">' . $newsletter_custom_css . '</style>';
	}

}
endif;
add_action( 'wp_head', 'ff_customiser_css', 99 );


/**
 * Handler fires after customizer saves - decache any generated CSS
 */
if ( ! function_exists( 'ff_after_customize_save' ) ) :
function ff_after_customize_save() {
	array_map( 'unlink', glob( WP_CONTENT_DIR . '/cache/buzz/custom-' . get_current_blog_id() . '.css' ) );
	array_map( 'unlink', glob( WP_CONTENT_DIR . '/cache/buzz/custom-email-' . get_current_blog_id() . '.css' ) );
}
endif;
add_action('customize_save_after', 'ff_after_customize_save');

/**
 * Parse CSS string and retrieve tokens
 * Tokens are delimited by double exclaimations - !!token!!
 * @param $css - CSS string
 */
if ( ! function_exists( 'ff_get_css_tokens' ) ) :
function ff_get_css_tokens($css) {
	$num_tokens = preg_match_all("/!!([^!\s]+)!!/", $css, $matches, PREG_SET_ORDER);

	$tokens = array();
	foreach($matches as $match) {
		$tokens[]	= $match[1];
	}

	return array_unique($tokens);
}
endif;

/**
 * Given a set of colour tokens and defaults, retrieve matching colours from the customizer
 * Generate RGBA semi-opaque variants of colours if defined
 */
if ( ! function_exists( 'ff_get_theme_custom_css_values' ) ) :
function ff_get_theme_custom_css_values($tokens, $defaults) {
	$values = array();

	foreach($tokens as $token) {
		// extract params from between brackets  token[params]
		$has_params = preg_match('/(\w+)\[(.+)\]/', $token, $param_match);
		$params 	= '';

		if($has_params) {
			$key		= $param_match[1];
			$params		= $has_params ? $param_match[2] : '';
		} else {
			$key = $token;
		}

		// Check if theme setting contains the "contrast" keyword - these tokens are calculated independently
		$exploded_token = explode( '_', $token );
		$is_contrast_value = array_search( 'contrast', $exploded_token );

		// if the token is a contrast value, calculate the contrast
		if( $is_contrast_value ) {

			// get the value of the token to base contrast from
			$base_token = strstr( $token, '_contrast', true );

			// get the custom setting value of the base token
			$theme_setting = get_theme_mod( $base_token );

			// is this a color?
			if( isset($defaults['color'][$base_token]) ) {

				// if no custom one defined get a default
				if(empty($theme_setting)) {
					$theme_setting = $defaults['color'][$base_token];
				}

				// calculate the contrast value
				$hsl_array = ff_hex_to_hsl( $theme_setting );

				// if lightness of base is less than 0.8, make contrast value light
				// OR is dark blue/purple (dark colours but have high lightness)
				if( $hsl_array['l'] <= 0.8 || ( $hsl_array['h'] >= 0.6 &&  $hsl_array['h'] <= 0.8 ) ) {
					$contrast_value = 'FFFFFF';
				}
				// else if lightness of base is greater than 0.8, make contrast value dark
				else {
					$contrast_value = '111111';
				}

				// add value to stylesheet
				$values[$token] = '#' . str_replace('#', '', $contrast_value);
			}

		}
		// else run normally
		else {

			// get the custom setting value
			$theme_setting = get_theme_mod($key);

			//var_dump( $theme_setting );

			// is this a color?
			if( isset($defaults['color'][$key]) ) {
				$opacity = empty($params) ? 1 : floatval($params);

				// if no custom one defined get a default
				if(empty($theme_setting)) {
					$theme_setting = $defaults['color'][$key];
				}

				// if opacity is defined then push an RGB colour definition
				if($opacity < 1) {
					$rgb_array = ff_hex_to_rgba( $theme_setting, $opacity );
					$values[$token] = sprintf( 'rgba(%s,%s,%s,%s)',
										$rgb_array['r'],
										$rgb_array['g'],
										$rgb_array['b'],
										$rgb_array['a']
									);
				} else {
					$values[$token] = '#' . str_replace('#', '', $theme_setting);
				}
			}

			// is this a boolean?
			elseif( isset($defaults['bool'][$key] ) ) {
				$default = $defaults['bool'][$key];
				if(is_array($default) && count($default) >= 2) {
					if($theme_setting) {
						$values[$token] = $defaults['bool'][$key][0];
					} else {
						$values[$token] = $defaults['bool'][$key][1];
					}
				}
			}

			// is this a string?
			elseif( isset($defaults['string'][$key] ) ) {
				$default = $defaults['string'][$key];
				$values[$token] = empty($theme_setting) ? $default : $theme_setting;
			}

		} // end if

	} // end foreach

	return $values;
}
endif;

/**
 * Utility method - convert hex to RGBA. Optionally specify an alpha (opacity) value
 *
 * @param $hex - string hex colour value - e.g 'FFFFFF' or '#FFFFFF'
 * @param $alpha - numeric opacity between 0 and 1
 * @return array - breakdown of hex into RGBA values
 */
if ( ! function_exists( 'ff_hex_to_rgba' ) ) :
function ff_hex_to_rgba( $hex, $alpha=1 ) {
	$rgb_array = array();

	// convert menu text colour to RGBA
	$color = hexdec($hex);
	$rgb_array['r'] = ($color >> 16) & 255;
	$rgb_array['g'] = ($color >> 8) & 255;
	$rgb_array['b'] = ($color & 255);
	$rgb_array['a'] = $alpha;

	return $rgb_array;
}
endif;

/**
 * Utility method - convert hex to HSL (Hue, Saturation, Lightness) colour definition. Used to determine the lightness of a colour.
 *
 * @param $hex - string hex colour value - e.g 'FFFFFF' or '#FFFFFF'
 * @return array - breakdown of hex into HSL values
 */
if ( ! function_exists( 'ff_hex_to_hsl' ) ) :
function ff_hex_to_hsl( $hex ) {
	// convert HEX to HSL
	$rgb_array = ff_hex_to_rgba( $hex );

	// generate HSL array from RGB
	$hsl_array = array();

	$var_R = ($rgb_array['r'] / 255);
	$var_G = ($rgb_array['g'] / 255);
	$var_B = ($rgb_array['b'] / 255);

	$var_Min = min($var_R, $var_G, $var_B);
	$var_Max = max($var_R, $var_G, $var_B);
	$del_Max = $var_Max - $var_Min;

	$L = $var_Max;

	if ($del_Max == 0)
	{
		$H = 0;
		$S = 0;
	}
	else
	{
		$S = $del_Max / $var_Max;

		$del_R = ( ( ( $var_Max - $var_R ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
		$del_G = ( ( ( $var_Max - $var_G ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
		$del_B = ( ( ( $var_Max - $var_B ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;

		if		($var_R == $var_Max) $H = $del_B - $del_G;
		else if ($var_G == $var_Max) $H = ( 1 / 3 ) + $del_R - $del_B;
		else if ($var_B == $var_Max) $H = ( 2 / 3 ) + $del_G - $del_R;

		if ($H<0) $H++;
		if ($H>1) $H--;
	}

	$hsl_array['h'] = $H;
	$hsl_array['s'] = $S;
	$hsl_array['l'] = $L;

	return $hsl_array;
}
endif;