<?php

namespace Firefly\Buzz\Setup;

use Firefly\Buzz\Config;

class Structure
{

    private $types = [
        'index', '404', 'archive', 'author',
        'category', 'tag', 'taxonomy', 'date',
        'embed', 'home', 'frontpage', 'page',
        'paged', 'search', 'single', 'singular',
        'attachment', 'sidebar',
    ];

	function __construct()
	{
       foreach($this->types as $type) {
           add_filter( "{$type}_template_hierarchy",  array($this, 'filter_template_hierarchy') );
       }
	}

    function filter_template_hierarchy( $hierarchy ) {
        $new = array();
        foreach($hierarchy as $item) {

            if( strpos( $item, 'controllers/' ) !== false ) {
                $new[] = $item;
            } else {
                $new[] = 'controllers/' . $item;
            }
        }

        $combined = array_merge($new, $hierarchy);
        return $combined;
    }
}