<?php

namespace Firefly\Setup;

use Firefly\Setup\Config;

class Images
{

    function __construct()
    {
        add_action( 'after_setup_theme', [$this, 'after_setup_theme']);
        if( Config::get('theme')['allow_svg'] ) {
            $this->allowSVG();
        }
    }

    public function after_setup_theme()
    {
        foreach ( Config::get('theme')['image_sizes'] as $image ) {
            add_image_size( $image['name'],
                            $image['width'],
                            $image['height'],
                            $image['crop'] );
        }

        // Set the default content width.
        $GLOBALS['content_width'] = Config::get('theme')['content_width'];
    }

	public function allowSVG() {
		add_filter( 'wp_check_filetype_and_ext', function($checked, $file, $filename, $mimes) {

			if ( ! $checked['type'] ) {

				$check_filetype		= wp_check_filetype( $filename, $mimes );
				$ext				= $check_filetype['ext'];
				$type				= $check_filetype['type'];
				$proper_filename	= $filename;

				if ( $type && 0 === strpos( $type, 'image/' ) && $ext !== 'svg' ) {
					$ext = $type = false;
				}

				$checked = compact( 'ext','type','proper_filename' );
			}

			return $checked;

		}, 10, 4 );

		/**
		 * Mime Check fix for WP 4.7.1 / 4.7.2
		 *
		 * Fixes uploads for these 2 version of WordPress.
		 * Issue was fixed in 4.7.3 core.
		 */
		add_filter( 'wp_check_filetype_and_ext', function ( $data, $file, $filename, $mimes ) {

			global $wp_version;
			if ( $wp_version !== '4.7.1' || $wp_version !== '4.7.2' ) {
				return $data;
			}

			$filetype = wp_check_filetype( $filename, $mimes );

			return [
				'ext'				=> $filetype['ext'],
				'type'				=> $filetype['type'],
				'proper_filename'	=> $data['proper_filename']
			];

		}, 10, 4 );


		add_filter( 'upload_mimes', function($mimes) {
			if ( current_user_can( 'administrator' ) ) {
				// allow SVG file upload
				$mimes['svg'] = 'image/svg+xml';
				$mimes['svgz'] = 'image/svg+xml';
			}
			return $mimes;
		});
	}
}