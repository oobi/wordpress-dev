<?php

namespace Firefly\Buzz\Setup;

use Firefly\Buzz\Core\Config;
use Firefly\Buzz\Customizer\Customizer;

class Scripts
{

    private $version;

    public function __construct()
    {
        $this->version = Config::get('config')['version'];
        add_action('wp_enqueue_scripts', array($this, 'enqueue_style'));
		add_action('wp_enqueue_scripts', array($this, 'enqueue_script'));
		add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_script'));
    }

    public function enqueue_script()
    {
        foreach ( Config::get('config')['scripts'] as $script ) {
            wp_enqueue_script( $script['name'], $this->set_path($script['src']), $script['deps'], $this->version, $script['in_footer'] );
        }
    }

    public function enqueue_style()
    {
		// if in the Email View, enqueue email styles
		if( class_exists( 'Buzz_Addon_Email_View' ) ) {
			\Buzz_Addon_Email_View::enqueue_style( 'buzz-email-view', $this->set_path( 'assets/css/email.css') , NULL, $this->version );
			// file doesn't exist and throws error on S3
			// \Buzz_Addon_Email_View::enqueue_style( 'buzz-email-view-custom', Customizer::$upload_dir . Customizer::$filename , NULL, $this->version ); 
		}

        // foreach ( Config::get('config')['styles'] as $style ) {
        //     wp_enqueue_style( $style['name'], $this->set_path( $style['src'] ), $style['deps'], $this->version  );
		// }

		// TODO: is this the best way to add these?
		// add customizer custom CSS website code IF NOT the email view
		if( ! method_exists( 'Buzz_Addon_Email_View', 'is_email_view' ) || ! \Buzz_Addon_Email_View::is_email_view() ) {
			$this->add_custom_css( get_theme_mod( 'buzz_custom_css_website_code' ) );
		}

		// add customizer custom CSS website code ONLY in the email view
		if( method_exists( 'Buzz_Addon_Email_View', 'is_email_view' ) && \Buzz_Addon_Email_View::is_email_view() ) {
			$this->add_custom_css( get_theme_mod( 'buzz_custom_css_email_code' ) );
		}

		// GOOGLE FONTS

		// merge customizer fonts
		$customFonts = get_theme_mod('buzz_font_urls', []);
		
		$styles = Config::get('config')['styles'];

		// add 'version' to each key in customFonts array
		foreach ($customFonts as $key => $font) {
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
    public function admin_enqueue_script() {
		$admin = Config::get('config')['admin'];

		foreach( $admin['styles'] as $style ) {
            wp_enqueue_style( $style['name'], $this->set_path( $style['src'] ), $style['deps'], $this->version  );
		}

		foreach( $admin['scripts'] as $script ) {
            wp_enqueue_script( $script['name'], $this->set_path($script['src']), $script['deps'], $this->version, $script['in_footer'] );
		}
	}

    private function set_path( $path ) {
        if( strpos( $path, 'http' ) === 0 ) {
            return $path;
        }

        return get_theme_file_uri( $path );
	}

	/**
	 * Add the custom CSS blocks from customizer to wp_head
	 */
	static function add_custom_css( $styles ) {
		if( !empty( $styles ) ) {
			add_action( 'wp_head', function() use ($styles) {
				printf( '<style type="text/css">%s</style>', $styles );
			}, 9999);
		}
	}
}
