<?php
namespace LOOS_Inc\CBP;

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', __NAMESPACE__ . '\cbp_register_post_type', 11 );
add_action( 'admin_init', __NAMESPACE__ . '\cbp_admin_init' );


/**
 * ブロックパターン登録用のカスタム投稿タイプを追加
 */
function cbp_register_post_type() {
	$parts_name = __( 'Block Patterns', 'loos-cbp' );
	register_post_type(
		LOOS_CBP_PT_SLUG,
		[
			'labels'        => [
				'name'          => $parts_name,
				'singular_name' => $parts_name,
			],
			'public'        => false,
			// 'menu_position' => 6,
			'show_ui'       => true,
			'show_in_menu'  => true,
			'capabilities'  => [ 'create_posts' => 'create_loos_cbp' ],
			'map_meta_cap'  => true, // capabilities を使用するために必要
			'has_archive'   => false,
			'menu_icon'     => 'dashicons-screenoptions',
			'show_in_rest'  => true,  // ブロックエディターに対応させる
			'supports'      => [ 'title', 'editor' ],
		]
	);
}


/**
 * 独自権限を各権限グループに付与する
 * add_cap() は remove_cap() するまで永続的に権限が付与されることに注意。
 */
function cbp_admin_init() {
	global $wp_roles;
	$wp_roles->add_cap( 'administrator', 'create_loos_cbp' );
	$wp_roles->add_cap( 'editor', 'create_loos_cbp' );
	$wp_roles->add_cap( 'author', 'create_loos_cbp' );
}
