<?php
namespace LOOS_Inc\CBP;

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', __NAMESPACE__ . '\add_parts_tax' );


/**
 * 投稿タイプの登録
 */
function add_parts_tax() {
	$tax = __( 'Pattern Category', 'loos-cbp' );
	register_taxonomy(
		LOOS_CBP_TAX_SLUG,
		[ LOOS_CBP_PT_SLUG ],
		[
			'public'             => false,
			'hierarchical'       => true,
			'labels'             => [
				'name'                => $tax,
				'singular_name'       => $tax,
				'menu_name'           => $tax,
			],
			'show_ui'            => true,
			'capabilities'       => [
				// 'manage_terms' => false,
				'edit_terms'   => 'manage_options',
				'delete_terms' => 'manage_options',
				// 'assign_terms' => false, // 投稿画面で設定できる権限
			],
			'show_admin_column'  => true,
			'query_var'          => true,
			'show_in_rest'       => true,
			// 'rewrite'            => [ 'slug' => LOOS_CBP_TAX_SLUG ],
		]
	);
}



/**
 * ブログパーツに絞り込みのセレクトボックスを追加
 */
add_filter( 'restrict_manage_posts', __NAMESPACE__ . '\add_search_by_tax' );
function add_search_by_tax( $post_type ) {
	if ( LOOS_CBP_PT_SLUG !== $post_type ) return;

	$options = '<option value="">' . esc_html__( 'Pattern Category', 'loos-cbp' ) . '</option>';

	// 全タームを取得
	wp_dropdown_categories( [
		'show_option_all' => __( 'Pattern Category', 'loos-cbp' ),
		// 'orderby'         => 'name',
		'hide_empty'      => false,
		'selected'        => get_query_var( LOOS_CBP_TAX_SLUG ),
		'name'            => LOOS_CBP_TAX_SLUG,
		'taxonomy'        => LOOS_CBP_TAX_SLUG,
		'value_field'     => 'slug',
	] );

}
