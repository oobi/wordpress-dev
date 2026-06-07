<?php

namespace Firefly\Buzz\Setup;

use Firefly\Buzz\Core\Config;

class Editor
{

    function __construct()
    {
        add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );
        add_filter('tiny_mce_before_init', array( $this, 'tinymce_settings' ) );
    }

    public function after_setup_theme()
    {
        add_editor_style( Config::get('config')['editor'] );
    }

    public function tinymce_settings( $init ) {

        $init['preview_styles'] = 'font-family font-size font-weight font-style text-decoration text-transform color background background-color';

        //theme_advanced_blockformats seems deprecated - instead the hook from Helgas post did the trick
        $init['block_formats'] = "Paragraph=p; Heading 2=h2; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6";

		// prevent all object resizing (ie. tables, images)
		$init['object_resizing'] = false;

        //$init['style_formats']  doesn't work - instead you have to use tinymce style selectors
        $style_formats = array(
            /* TEXT STYLES */
            array(
                'title' => 'Highlight',
                'block' => 'div',
                'classes' => 'highlight',
                'wrapper' => true
            ),
            array(
                'title' => 'Pull Quote',
                'block' => 'blockquote',
                'classes' => 'pullquote',
                'wrapper' => true
            ),
            array(
                'title' => 'Table Standard',
                'selector' => 'table',
                'classes' => 'table table-standard',
                'wrapper' => true
            ),
            array(
                'title' => 'Table Banded',
                'selector' => 'table',
                'classes' => 'table table-banded',
                'wrapper' => true
            ),
            array(
                'title' => 'Button',
                'selector' => 'a',
                'classes' => 'btn btn-primary'
            )
        );

        // Merge old & new styles
        $settings['style_formats_merge'] = true;

        $init['style_formats'] = json_encode( $style_formats );
        //$init['statusbar'] = false;
        return $init;
    }

}