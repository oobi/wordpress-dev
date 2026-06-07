<?php

namespace FF;

/**
 * Updater Class
**/
class FireflyFacebookMetaTags{

	/**
	 * Class Constructor
	 */
	public function __construct() {
		add_action('wp_head', array( $this, 'add_meta_tags' ));
	}


	public function add_meta_tags() {
		global $post;

		if( is_singular() ) {
			setup_postdata( $post );

			printf('<meta property="og:title" content="%s">',  get_the_title( $post ) );
			printf('<meta property="og:url" content="%s">',  get_permalink( $post ) );
			printf('<meta property="og:type" content="%s">',  'website' );
			printf('<meta property="og:description" content="%s">',  get_the_excerpt( $post ) );


			if( has_post_thumbnail( $post ) ) {
				$attachment_id = get_post_thumbnail_id( $post);
				$attachment = wp_get_attachment_image_src( $attachment_id, 'large'); // returns array (url, width, height)

				if( $attachment && is_array( $attachment )) {
					printf('<meta property="og:image" content="%s">',  		 $attachment[0] );
					printf('<meta property="og:image:width" content="%s">',  $attachment[1] );
					printf('<meta property="og:image:height" content="%s">', $attachment[2] );
				}
			}

			wp_reset_postdata();
		}
	}
}