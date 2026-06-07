<?php

namespace Firefly\Gutenberg;

use Firefly\Setup\Config;
use Firefly\Customizer\Customizer;


class Gutenberg
{

	public function __construct()
	{
		add_action('after_setup_theme', [$this, 'afterSetupTheme']);

		add_filter('image_size_names_choose', function ($sizes) {
			$custom_sizes = $this->getImageSizes();
			return array_merge($sizes, $custom_sizes);
		});
	}

	public function afterSetupTheme()
	{
		add_theme_support('disable-custom-colors');
		add_theme_support('editor-color-palette', $this->getThemeColors());
		add_theme_support('editor-font-sizes', $this->getTextSizes());
		add_theme_support('responsive-embeds');

		// align wide can't work with a sidebar
		if( Customizer::get_theme_mod( 'buzz_article_page_sidebar_position' ) != 'none') {
			add_theme_support('align-wide');
		}
	}

	public function getThemeColors()
	{
		// $colors = Config::get('theme')['theme_colors'];
		$colors = Customizer::get_theme_mod( 'buzz_colors_theme_colors' );

		if($colors == null) {
			$colors = [];
		}

		return array_map(function ($color, $value) {
			return [
				'name' => ucwords(str_replace('-', ' ', $color)),
				'slug' => $color,
				'color' => $value,
			];
		}, array_keys($colors), $colors);
	}

	public function getTextSizes()
	{
		$text = Config::get('theme')['text_sizes'];
		return array_map(function ($name, $value) {
			return [
				'name' => ucwords(str_replace('-', ' ', $name)),
				'slug' => $name,
				'size' => $value
			];
		}, array_keys($text), $text);
	}

	public function getImageSizes()
	{
		$sizes = Config::get('theme')['image_sizes'];
		$values = [];
		foreach ($sizes as $size) {
			$values[$size['name']] = ucwords(str_replace('_', ' ', $size['name']));
		}
		return $values;
	}
}
