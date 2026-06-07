<?php

namespace Firefly\Setup;

use Firefly\Setup\Config;
use Firefly\Customizer\Customizer;

class Scripts
{

	private $version;

	public function __construct()
	{
		$this->version = Config::get('theme')['version'];
		add_action('wp_enqueue_scripts', [$this, 'enqueue_style'], 5);
		add_action('wp_enqueue_scripts', [$this, 'enqueue_script']);
		add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_script']);
	}

	public function enqueue_script()
	{
		if (!is_email_view()) {
			foreach (Config::get('theme')['scripts'] as $script) {
				wp_enqueue_script($script['name'], $this->set_path($script['src']), $script['deps'], $this->version, $script['in_footer']);
			}
		}
	}

	public function enqueue_style()
	{


		// if in the Email View, enqueue email styles
		if (is_email_view()) {
			foreach (Config::get('theme')['email_styles'] as $style) {
				$version = array_key_exists('version', $style) ? $style['version'] : $this->version;
				if (class_exists('Buzz_Addon_Email_View')) {
					\Buzz_Addon_Email_View::register_style($style['name'], $this->set_path($style['src']), $style['deps'], $version);
				}
			}

			// \Buzz_Addon_Email_View::enqueue_style( 'buzz-email-view-custom', Customizer::$upload_dir . Customizer::$filename , NULL, $this->version );
			$this->add_custom_css(get_theme_mod('buzz_custom_css_email_code'));
		}

		// add customizer custom CSS website code IF NOT the email view
		else {
			foreach (Config::get('theme')['styles'] as $style) {
				$version = array_key_exists('version', $style) ? $style['version'] : $this->version;
				wp_enqueue_style($style['name'], $this->set_path($style['src']), $style['deps'], $version);
			}
			$this->add_custom_css(get_theme_mod('buzz_custom_css_website_code'));

			// if print view, enqueue the print styles
			if( is_print_view() ) {
				$this->add_custom_css(get_theme_mod('buzz_custom_css_print_code'));
			}
		}
	}

	/**
	 * Add custom scripts and styles to admin Dashboard
	 */
	public function admin_enqueue_script()
	{
		$admin = Config::get('theme')['admin'];

		$styles = $admin['styles'] ?? [];
		$scripts = $admin['scripts'] ?? [];

		foreach ($styles as $style) {
			wp_enqueue_style($style['name'], $this->set_path($style['src']), $style['deps'], $this->version);
		}

		foreach ($scripts as $script) {
			wp_enqueue_script($script['name'], $this->set_path($script['src']), $script['deps'], $this->version, $script['in_footer']);
		}
	}

	protected function set_path($path)
	{
		if (strpos($path, 'http') === 0) {
			return $path;
		}

		return get_theme_file_uri($path);
	}

	/**
	 * Add the custom CSS blocks from customizer to wp_head
	 */
	protected function add_custom_css($styles)
	{
		if (!empty($styles)) {
			add_action('wp_head', function () use ($styles) {
				printf('<style type="text/css">%s</style>', $styles);
			}, 9999);
		}
	}
}
