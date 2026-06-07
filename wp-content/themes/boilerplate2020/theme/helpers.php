<?php 

use Firefly\Setup\Config;

/*
 * This file is strictly for functions that need to be global and don't
 * belong on the FireflyPost object. Consider adding new methods to the 
 * FireflyPost class theme/Core/FireflyPost
*/

if (! function_exists('dd')) :
    function dd()
    {
        echo '<pre>';
        array_map( function( $x ) { var_dump( $x ); }, func_get_args() );
        echo '</pre>';
        die;
    }
endif;

/**
 * Shortcut function for the double underscore localisation function
 */
if (! function_exists('ff__')) :
    function ff__($text) {
		return __($text, Config::get('theme')['text_domain']);
    }
endif;

