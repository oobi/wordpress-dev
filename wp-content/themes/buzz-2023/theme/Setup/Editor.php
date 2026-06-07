<?php

namespace Firefly\Setup;

use Firefly\Setup\Config;

class Editor
{

    function __construct()
    {
        // Add backend styles for Gutenberg.
        add_action( 'enqueue_block_editor_assets', [$this, 'enqueue_gutenberg_styles'] );
    }

    public function enqueue_gutenberg_styles()
    {
        foreach( Config::get('theme')['editor'] as $key=>$style ) {
            wp_enqueue_style( 'ff_editor_style' . $key, $style);
        }
    }
}