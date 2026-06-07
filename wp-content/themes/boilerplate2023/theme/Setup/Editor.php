<?php

namespace Firefly\Setup;

use Firefly\Setup\Config;

class Editor
{

    function __construct()
    {
        // enqueue styles for legacy editor
        add_action( 'after_setup_theme', [$this, 'after_setup_theme']);
        add_filter('tiny_mce_before_init', [$this, 'tinymce_settings']);

        // Add backend styles for Gutenberg.
        add_action( 'enqueue_block_editor_assets', [$this, 'enqueue_gutenberg_styles'] );
    }

    public function after_setup_theme()
    {
        add_editor_style( Config::get('theme')['editor'] );
    }

    public function enqueue_gutenberg_styles()
    {
        foreach( Config::get('theme')['editor'] as $key=>$style ) {
            wp_enqueue_style( 'ff_editor_style' . $key, $style);
        }
    }

    public function tinymce_settings( $init ) {

        $init['preview_styles'] = 'font-family font-size font-weight font-style text-decoration text-transform color background background-color';

        $init['block_formats'] = "Paragraph=p; Heading 2=h2; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6";

        $style_formats = [
            /* TEXT STYLES */
            [
                'title' => 'Highlight',
                'block' => 'div',
                'classes' => 'highlight',
                'wrapper' => true
            ],
            [
                'title' => 'Pull Quote',
                'block' => 'blockquote',
                'classes' => 'pullquote',
                'wrapper' => true
            ],
            [
                'title' => 'Table Standard',
                'selector' => 'table',
                'classes' => 'table',
                'wrapper' => true
            ],
            [
                'title' => 'Table Banded',
                'selector' => 'table',
                'classes' => 'table table-striped',
                'wrapper' => true
            ],
            [
                'title' => 'Button',
                'selector' => 'a',
                'classes' => 'btn btn-primary'
            ]
        ];

        // Merge old & new styles
        $settings['style_formats_merge'] = true;

		$init['style_formats'] = json_encode( $style_formats );

		// prevent image / table resize
		$init['object_resizing'] = false;


        //$init['statusbar'] = false;
        return $init;
    }

}