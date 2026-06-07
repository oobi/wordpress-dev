<?php
namespace Firefly\Buzz\Setup;

use Timber\Timber;
use Firefly\Timber\FireflyPost;

class Newsletter {

	protected $newsletter;

    public function __construct() {
		// if Buzz plugin is active, replace the homepage
		if( class_exists( 'FF_Newsletter' ) ) {
			add_filter( 'template_include', array($this, 'replace_home'), 99 );
		}
	}

    /**
     * When we hit the root of the site, we want to use the single-newsletter.php controller and post
     *
     * @param [type] $template
     * @return void
     */
    public function replace_home( $template ) {
        global $wp_query, $post;

        if ( is_home() ) {
			// set the global post to the latest newsletter if available
			$latest = $this::get();
			if( $latest !== false ) {
				$post = $latest; // must be assigned to $post for setup_postdata to work properly
				setup_postdata( $post );
			}

			// set the template to single-newsletter
            $new_template = locate_template( array( 'controllers/single-newsletter.php' ) );
            if ( $new_template != '' ) {
                return $new_template;
            }
		}

        return $template;
	}

    /**
     * Get the newsletter by ID. If no post ID passed, returns latest newsletter
	 *
	 * @param 	{int}	$id 		The newsletter post ID
     *
     * @return FireflyPost
     */
    public static function get( $id=false ) {
		$args = array(
			'post_type'		=> 'newsletter',
			'p'				=> $id,
		);
		$newsletter = Timber::get_post( $args );

		// Given we successfully retrieved a newsletter, return the first object
        if( ! empty( $newsletter ) ) {
            return $newsletter;
        }

        return false;
    }

}
