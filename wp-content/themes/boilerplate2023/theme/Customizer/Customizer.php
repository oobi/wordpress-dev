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
	public static $separatorCount = 0;

	public static function get_instance()
	{
		if (self::$instance) {
			return self::$instance;
		} else {
			return new Customizer();
		}
	}

	public function __construct()
	{
		self::$uploadDirectory = esc_url_raw(wp_upload_dir()['baseurl'] . '/kirki-css/');

		if (class_exists('Kirki')) {
			$this->kirkiConfig();

			add_filter('kirki/dynamic_css/method', function () {
				return 'file';
			});

			// if (!is_customize_preview() ) {
			// 	add_filter( 'kirki_output_inline_styles', '__return_false' );
			// }

			add_filter('kirki/config', function ($config = array()) {
				return wp_parse_args([
					'styles_priority' => 10
				], $config);
			});

			// add_filter('kirki_config', [$this, 'styleCustomizer']);
			// add_filter('mce_css', [$this, 'addKirkiEditorStyles']);

			// send back a list of colours for the colour picker swatches
			// based on the user defined palette
			add_filter('kirki_color_swatches', function ($swatches) {
				return self::kirkiSwatches();
			});
		}

		self::$instance = $this;
	}

	/**
	 * Apply Kirki configuration
	 *
	 * @return void
	 */
	private function kirkiConfig()
	{
		add_action( 'init', function() {
			new \Kirki\Panel($this->panel_id, [
				'priority'    => 999,
				'title'       => 'Theme Options',
				'description' => 'Customize the look of your theme',
			]);

			// load in additional config sections
			$config_directory = __DIR__ . '/config/';
			$config_files = array_values(array_diff(scandir($config_directory), ['.', '..']));

			foreach ($config_files as $file) {
				include $config_directory . $file;
			}
		});
	}

	/**
	 * Style the customizer itself
	 */
	public function styleCustomizer($config)
	{
		return wp_parse_args([
			'disable_loader' => true, // remove Kirki's loader icon
		], $config);
	}

	/**
	 * Add customizer styles to Editor
	 * Kirki Editor styles are cached easily - clear cache in DEV
	 */
	public function addKirkiEditorStyles($mce_css)
	{
		if (!empty($mce_css)) {
			$mce_css .= ',';
		}
		return $mce_css . self::$uploadDirectory . self::$filename;
	}

	/**
	 * get kirki custom field value
	 */
	public static function default($fieldname)
	{
		$value = null;
		if (class_exists('Kirki')) {
			$value = \Kirki::$fields[$fieldname]['default'];
		}
		return $value;
	}

	/**
	 * Get a theme mod using Kirki default
	 * @see https://codex.wordpress.org/Function_Reference/get_theme_mod
	 */
	public static function get_theme_mod($fieldname, $default = false)
	{
		$custom_default = null;


		if (class_exists('Kirki')) {
			$custom_default = isset(\Kirki::$fields[$fieldname]['default']) ? \Kirki::$fields[$fieldname]['default'] : null;
		}

		// use the kirki custom value by preference, otherwise the default sent in here
		$default_value = $custom_default ?? $default;

		// check for presence of single percentage symbol (%)
		// get_theme_mod uses sprintf - if default_value contains one things get messy. Need to double them up. eg. "10%" becomes "10%%"
		if (is_string($default_value)) {
			$default_value = preg_replace('/(?<!%)%(?!%)/', '%%', $default_value);
		}

		return get_theme_mod($fieldname, $default_value);
	}


	/**
	 * Executes in_array on get_theme_mod
	 * Protected against non-array values
	 */
	public static function get_theme_mod_contains($value, $fieldname, $default = false)
	{
		$mod = self::get_theme_mod($fieldname, $default);

		if ($mod && is_array($mod)) {
			return in_array($value, $mod);
		}

		return false;
	}

	/**
	 * return a list of colours for the colour picker swatches
	 *
	 * @param [type] $colors
	 * @return void
	 */
	public static function kirkiSwatches(): array
	{
		// default theme palette
		$defaults = self::kirkiColorDefaults();
		// retrieve Kirki palette
		$colors = get_theme_mod('ff_colors_palette', []);
		// merge together and flatten
		return array_values(array_merge($defaults, $colors));
	}

	public static function kirkiColorDefaults(): array
	{
		$colors = Config::get('theme')['theme_colors'];
		return $colors;
	}

	public static function separator($section_id, $title)
	{
		return new \Kirki\Field\Generic(
			[
				'settings'    => $section_id . '_separator' . self::$separatorCount++,
				'section'     => $section_id,
				'choices'     => [
					'element'  => 'hr',
					'style'    => 'margin: 2rem 0',
				],
			]
		);
	}

	public static function title($section_id, $title)
	{
		return new \Kirki\Field\Generic(
			[
				'settings'    => $section_id . '_separator' . self::$separatorCount++,
				'section'     => $section_id,
				'default'	=> $title,
				'choices'     => [
					'element'  => 'h2',
					'style'    => 'color: #666; margin: 1rem 0; padding: 1rem; border-bottom: 1px solid #CCC; border-top: 1px solid #CCC; font-size: 1.125rem; text-transform: uppercase; background: #FFF; margin-left:-12px; margin-right:-12px;',
				]
			]
		);
	}
}
