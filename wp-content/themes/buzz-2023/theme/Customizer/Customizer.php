<?php
namespace Firefly\Customizer;
use Firefly\Setup\Config;

class Customizer {

	public $config_id;
	public $panel_id;
	public static $upload_dir;
	public static $filename;
	public static $instance;

	public static function get_instance() {
		if( self::$instance ) {
			return self::$instance;
		} else {
			return new Customizer();
		}
	}

	public function __construct() {

		// If buzz_customizer config option doesn't exist, turn off customizer
		if( ! isset( Config::get('theme')['buzz_customizer'] ) || empty( Config::get('theme')['buzz_customizer'] ) ) {
			return;
		}

		$this->config_id 	= 'buzz_customizer';
		$this->panel_id 	= 'buzz_theme_options';

		// Upload directory and filename must match those specified by Kirki. If these stop working, check Kirki.
		$up_dir 			= wp_upload_dir();
		self::$upload_dir 	= esc_url_raw( $up_dir['baseurl'] . '/kirki-css/' );
		self::$filename 	= 'styles.css';

		// set up kirki
		// require 'kirki/kirki.php';
		if( class_exists( 'Kirki' ) ) {
			$this->setup_fields();

			// Force Kirki to output styles to a file (rather than inline)
			add_filter( 'kirki_dynamic_css_method', function() { return 'file'; });

			// Embed customizer google fonts to CSS file (necessary to import to Editor)
			// TODO: Embedding google fonts doesn't work for now - waiting for v3.1
			// add_filter( "kirki_googlefonts_load_method", function() { return 'embed'; });

			// Init customizer controls
			add_filter( 'kirki_config', array( $this, 'style_customizer' ) );

			// Add customizer styles to Editor
			// NOTE: The Kirki editor styles gets stuck in browser cache very easily! Make sure to clear cache regularly while in DEV
			add_filter( 'mce_css', function( $mce_css ) {
				if ( ! empty( $mce_css ) ) {
					$mce_css .= ',';
				}
				return $mce_css . self::$upload_dir . self::$filename;
			});

		}

		self::$instance = $this;
	}

	private function setup_fields( ) {
		// setup Kirki
		\Kirki::add_config( $this->config_id, array(
			'capability'    => 'edit_theme_options',
			'option_type'   => 'theme_mod',
			// 'disable_output'=> true, // do not automatically output CSS, we will enqueue it manually for greater control
			'styles_priority'=> 999,
		) );

		\Kirki::add_panel( $this->panel_id, array(
			'priority'    => 999, // last in list
			'title'       => ff__( 'Theme Options' ),
			'description' => ff__( 'Customize the look of your Buzz newsletter' ),
		) );

		// setup fields (if enabled in config)
		$config = Config::get('theme')['buzz_customizer'];
		if( $config['colors'] ) 				include 'controls/colors.php';
		if( $config['fonts'] ) 					include 'controls/fonts.php';
		if( $config['branding'] ) 				include 'controls/branding.php';
		if( $config['navbar'] ) 				include 'controls/navbar.php';
		if( $config['featured-articles'] ) 		include 'controls/featured-articles.php';
		if( $config['footer'] ) 				include 'controls/footer.php';
		if( $config['social-media'] ) 			include 'controls/social.php';

		// separate templates
		if( $config['index-page'] ) 			include 'controls/index-page.php';
		if( $config['article-page'] ) 			include 'controls/article-page.php';

		// custom css
		// Only show if Email View addon plugin is enabled
		if( $config['add-ons'] && class_exists('Buzz_Addon_Email_View') ) {
			include 'controls/custom-css-email.php';
		}

		// Only show if Print View addon plugin is enabled
		if( $config['add-ons'] && class_exists('Buzz_Addon_Print_View') ) {
			include 'controls/custom-css-print.php';
		}

		if( $config['custom-css'] ) 			include 'controls/custom-css-website.php';

		// Hook for add-ons to add their own customizer options
		do_action( 'buzz_customizer_addons_options', 10 );

		// add on specific fields

		// Only show if Email View addon plugin is enabled
		if( $config['add-ons'] && class_exists('Buzz_Addon_Email_View') ) {
			include 'controls/addons/email-view.php';
		}

		// Only show if Print View addon plugin is enabled
		if( $config['add-ons'] && class_exists('Buzz_Addon_Print_View') ) {
			include 'controls/addons/print-view.php';
		}

		if( $config['add-ons'] && class_exists('Buzz_Addon_Taxonomies') ) {
			include 'controls/addons/taxonomies.php'; // Only show if Taxonomies addon plugin is enabled
		}
	}

	/**
	 * Style the customizer itself
	 */
	public function style_customizer( $config ) {
		return wp_parse_args( array(
			'disable_loader'	=> true // remove Kirki's loader icon
		), $config );
	}

	// TODO: Figure out a way of getting to the email view from within the Customizer (the below doesn't work)
	// /**
	//  * Add a view switch
	// 	* 	add_filter( 'customize_preview_init', array( $this, 'add_view_switch' ) );
	//  */
	// public function add_view_switch( $config ) {
	// 	printf('<div class="view-switch"><a class="btn" href="http://demo.thebuzz.net.au/buzzv4/newsletter/issue-26/email/">Switch to Email View</a></div>');
	// }

	public function sanitize_basic_html($text) {
		$class_and_style = array('class' => array(), 'style' => array() );

		return wp_kses( $text, array(
			'br' => $class_and_style,
			'em' => $class_and_style,
			'strong' => $class_and_style,
			'span' => $class_and_style,
			'b' => $class_and_style,
			'i' => $class_and_style,
			'sup' => $class_and_style,
			'sub' => $class_and_style,
		) );
	}

	/**
	 * get kirki custom field value
	 */
	public static function default( $fieldname ) {
		$value = null;
		if( class_exists( 'Kirki' ) ) {
			$value = \Kirki::$fields[$fieldname]['default'];
		}
		return $value;
	}

	/**
	 * Get a theme mod using Kirki default
	 * @see https://codex.wordpress.org/Function_Reference/get_theme_mod
	 */
	public static function get_theme_mod( $fieldname, $default=false ) {
		$custom_default = null;


		if( class_exists( 'Kirki' ) ) {
			$custom_default = isset( \Kirki::$fields[$fieldname]['default'] ) ? \Kirki::$fields[$fieldname]['default'] : null;
		}

		// use the kirki custom value by preference, otherwise the default sent in here
		$default_value = $custom_default ?? $default;

		// check for presence of single percentage symbol (%)
		// get_theme_mod uses sprintf - if default_value contains one things get messy. Need to double them up. eg. "10%" becomes "10%%"
		if( is_string( $default_value ) ) {
			$default_value = preg_replace( '/(?<!%)%(?!%)/', '%%', $default_value );
		}

		return get_theme_mod( $fieldname, $default_value );
	}


	/**
	 * Executes in_array on get_theme_mod
	 * Protected against non-array values
	 */
	public static function get_theme_mod_contains( $value, $fieldname, $default=false ) {
		$mod = self::get_theme_mod( $fieldname, $default );

		if( $mod && is_array($mod) ) {
			return in_array($value, $mod );
		}

		return false;
	}

}
