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

/**
 * Shortcut function for checking if the buzz newsletter plugin is active
 */
if (!function_exists('buzz_active')) :
	function buzz_active() {
		return class_exists('FF_Newsletter');
	}
endif;

/**
 * Get the bootstrap column width value for the number of columns needed
 *
 * @param 	{int}	$num_columns 			The number of columns
 */
if ( ! function_exists( 'ff_get_col_width' ) ) :
	function ff_get_col_width( $num_columns = 4 ) {
		$num_columns = intval( $num_columns ); // make sure num_columns is an int

		// if zero, return
		if( $num_columns === 0 ) {
			return 12;
		}

		// if number of columns is not divisable by 12 or is greater than 12, return
		if( ( 12 % $num_columns ) != 0 || $num_columns > 12 ) {
			return 6;
		}

		// return column width value
		return 12 / $num_columns;
	}
endif;

/**
 * Is this the email view?
 */
if ( ! function_exists( 'is_email_view' ) ) :
	function is_email_view( ) {
		$has_email_view = class_exists( 'Buzz_Addon_Email_View' );
		$is_email_view = $has_email_view && \Buzz_Addon_Email_View::is_email_view();
		return $is_email_view;
	}
endif;

/**
 * Is this the print view?
 */
if ( ! function_exists( 'is_print_view' ) ) :
	function is_print_view( ) {
		$has_print_view = class_exists( 'Buzz_Addon_Print_View' );
		$is_print_view = $has_print_view && \Buzz_Addon_Print_View::is_print_view();
		return $is_print_view;
	}
endif;

/**
 * Get print classes
 */
if ( ! function_exists( 'get_print_classes' ) ) :
	function get_print_classes( $id ) {
		if ( is_print_view() ) {
			return \Buzz_Addon_Print_View::get_print_classes($id);
		}
		return '';
	}
endif;

/**
 * Return gutenberg blocks via id.
 */
if ( ! function_exists( 'collect_blocks_by_name' ) ) :
function collect_blocks_by_name($jsonArray, $blockName) {
    $result = [];

    foreach ($jsonArray as $block) {
        if ($block['blockName'] === $blockName) {
            $result[] = $block;
        }

        if (!empty($block['innerBlocks'])) {
            // Recursively search for the blockName in innerBlocks
            $innerResults = collect_blocks_by_name($block['innerBlocks'], $blockName);
            // Merge the inner results with the current result
            $result = array_merge($result, $innerResults);
        }
    }

    return $result;
}
endif;