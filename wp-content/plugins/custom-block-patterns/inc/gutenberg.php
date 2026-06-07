<?php
namespace LOOS_Inc\CBP;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ブロックパターンの登録
 */
add_action( 'init', __NAMESPACE__ . '\register_block_patterns', 20 );
function register_block_patterns() {

	// パターンカテゴリーのセット
	register_block_pattern_category(LOOS_CBP_PT_SLUG, [
		'label' => '[CBP]',
	] );
	$all_terms = get_terms( LOOS_CBP_TAX_SLUG );
	foreach ( $all_terms as $term ) {
		register_block_pattern_category(
			LOOS_CBP_PT_SLUG . $term->term_id,
			[ 'label' => '[CBP] ' . $term->name ]
		);
	}

	// カスタムパターン全取得
	$the_query = new \WP_Query( [
		'post_type'              => LOOS_CBP_PT_SLUG,
		'posts_per_page'         => -1,
		'no_found_rows'          => true,
	] );
	wp_reset_postdata();

	// デフォルトのビューポートサイズ
	$viewport = apply_filters( 'loos_cbp_default_viewport_width', 1200 );

	// パターン登録
	foreach ( $the_query->posts as $parts ) {
		$pid = $parts->ID;

		// カテゴリーで振り分け
		$categories = [];
		$the_terms  = get_the_terms( $pid, LOOS_CBP_TAX_SLUG );

		if ( empty( $the_terms ) ) {
			$categories = [ LOOS_CBP_PT_SLUG ];
		} else {
			foreach ( $the_terms as $term ) {
				$categories[] = LOOS_CBP_PT_SLUG . $term->term_id;
			}
		}

		// パターン登録データ
		$options = [
			'title'         => $parts->post_title,
			'content'       => $parts->post_content,
			'categories'    => $categories,
			'viewportWidth' => $viewport,
		];

		// その他、カスタムフィールドで調整可能なもの
		// $viewport = apply_filters( 'loos_cbp_viewport_width', 1200, $pid );
		// $block_types = apply_filters( 'loos_cbp_block_types', [], $pid );
		// if ( $viewport ) $options['viewportWidth'] = $viewport;
		// if ( ! empty( $block_types ) ) $options['blockTypes'] = $block_types;

		register_block_pattern( "loos-cbp/pattern-$pid", $options );
	}
}
