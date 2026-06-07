<?php 

namespace Firefly\Customizer;

use Firefly\Setup\Config;

class Customizer
{
    public $config_id = 'ff_customizer';
	public $panel_id = 'ff_theme_options';
	public static $filename = 'styles.css';
    public static $instance;
	public static $uploadDirectory;
    
    public static function get_instance() {
		if( self::$instance ) {
			return self::$instance;
		} else {
			return new Customizer();
		}
    }
    
    public function __construct()
    {
        self::$uploadDirectory = esc_url_raw(wp_upload_dir()['baseurl'] . '/kirki-css/' );

		if(class_exists('Kirki')) {    
            $this->setupFields();
			add_filter('kirki_dynamic_css_method', [$this, 'outputStyleSheet']);
			add_filter('kirki_config', [$this, 'styleCustomizer']);
            add_filter('mce_css', [$this, 'addKirkiEditorStyles']);
		}

		self::$instance = $this;
    }

    private function setupFields()
    {
        \Kirki::add_config( $this->config_id, [
			'capability'    => 'edit_theme_options',
			'option_type'   => 'theme_mod',
			'styles_priority'=> 999,
        ]);

		\Kirki::add_panel( $this->panel_id, [
			'priority'    => 999,
			'title'       => 'Theme Options',
			'description' => 'Customize the look of your Buzz newsletter',
        ]);
        
        $config_directory = __DIR__ . '/config/';
        foreach ($this->getConfiguartionFiles($config_directory) as $file) {
            include $config_directory . $file;
        }
    }

    protected function getConfiguartionFiles($path)
    {
        return array_values(array_diff(scandir($path), ['.', '..']));
    }

    public function outputStyleSheet()
    {
        return 'file';
    }

    /**
	 * Style the customizer itself
	 */
	public function styleCustomizer($config) {
		return wp_parse_args([
			'disable_loader' => true, // remove Kirki's loader icon
        ], $config );
	}
    
    /**
     * Add customizer styles to Editor
     * Kirki Editor styles are cached easily - clear cache in DEV
     */
    public function addKirkiEditorStyles($mce_css)
    {
        if(! empty($mce_css)) {
            $mce_css .= ',';
        }
        return $mce_css . self::$uploadDirectory . self::$filename;
    }

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
