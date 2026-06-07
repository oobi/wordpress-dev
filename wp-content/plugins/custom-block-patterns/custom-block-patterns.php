<?php
/**
 * Plugin Name: Custom Block Patterns
 * Plugin URI: https://github.com/ddryo/Custom-Block-Patterns
 * Description: You can easily create your own block patterns and register them.
 * Version: 1.4.0
 * Author: LOOS, Inc.
 * Author URI: https://loos.co.jp/
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: loos-cbp
 */

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! function_exists( 'register_block_pattern' ) ) return;

define( 'LOOS_CBP_PT_SLUG', 'loos-cbp' );
define( 'LOOS_CBP_TAX_SLUG', 'loos-cbp-category' );

add_action( 'plugins_loaded', function() {
	$cbp_path = plugin_dir_path( __FILE__ );

	// 翻訳ファイルの読み込み
	if ( 'ja' === determine_locale() ) {
		load_textdomain( 'loos-cbp', $cbp_path . 'languages/loos-cbp-ja.mo' );
	} else {
		load_plugin_textdomain( 'loos-cbp' );
	}

	require $cbp_path . 'inc/gutenberg.php';
	require $cbp_path . 'inc/post_type.php';
	require $cbp_path . 'inc/taxonomy.php';
} );
