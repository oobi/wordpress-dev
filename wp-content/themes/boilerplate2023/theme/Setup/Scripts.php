<?php

namespace Firefly\Setup;

use Firefly\Setup\Config;

class Scripts
{

	private $version;

	public function __construct()
	{
		$this->version = Config::get('theme')['version'];
		add_action('wp_enqueue_scripts', [$this, 'enqueue_style']);
		add_action('wp_enqueue_scripts', [$this, 'enqueue_script']);
		add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_script']);
	}

	public function enqueue_script()
	{
		foreach (Config::get('theme')['scripts'] as $script) {
			wp_enqueue_script($script['name'], $this->set_path($script['src']), $script['deps'], $this->version, $script['in_footer']);
		}
	}

	public function enqueue_style()
	{
		// merge customizer fonts
		$customFonts = get_theme_mod('ff_fonts_urls', []);
		$styles = Config::get('theme')['styles'];

		// add 'version' to each key in customFonts array
		foreach ($customFonts as $key => $value) {
			$customFonts[$key]['version'] = null;
		}

		// merge custom and styles array, overwriting any duplicate entries with 'name' key
		if (!empty($customFonts)) {
			$styles = array_merge(array_column($styles, null, 'name'), array_column($customFonts, null, 'name'));
		}

		foreach ($styles as $style) {
			$style = array_merge([
				'version' => $this->version,
				'deps'	=> null,
				'name'	=> '',
				'src'	=> ''
			], $style);

			if (!empty($style['name']) && !empty($style['src'])) {
				wp_enqueue_style($style['name'], $this->set_path($style['src']), $style['deps'], $style['version']);
			}
		}
	}

	/**
	 * Add custom scripts and styles to admin Dashboard
	 */
	public function admin_enqueue_script()
	{
		$admin = Config::get('theme')['admin'];

		foreach ($admin['styles'] as $style) {
			wp_enqueue_style($style['name'], $this->set_path($style['src']), $style['deps'], $this->version);
		}

		foreach ($admin['scripts'] as $script) {
			wp_enqueue_script($script['name'], $this->set_path($script['src']), $script['deps'], $this->version, $script['in_footer']);
		}
	}

	private function set_path($path)
	{
		if (strpos($path, 'http') === 0) {
			return $path;
		}

		return get_theme_file_uri($path);
	}
}
