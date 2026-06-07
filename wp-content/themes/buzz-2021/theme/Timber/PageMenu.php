<?php

namespace Firefly\Timber;

class PageMenu
{
    public static function render( $post )
    {
        // if a page, get the parent ID
        if( is_page() ) {
            if ( $post->post_parent > 0 ) {
                $post_ancestors = get_post_ancestors($post->ID);
                $parent_id = array_pop( $post_ancestors );
            }
            else {
                $parent_id = $post->ID;
            }
        }
        // if a single post, set the parent to Blog
        elseif( get_post_type( $post->ID ) == 'post' ) {
            $parent_id = get_option( 'page_for_posts' );
        }

        $page_menu_args = array(
            'child_of'    	=> $parent_id,
			'echo'         	=> false,
            'title_li'		=> false, 		// display no heading
            'link_before'   => '<span class="fal fa-angle-right mr-1"></span>'
        );

        return wp_list_pages( $page_menu_args );
    }
}