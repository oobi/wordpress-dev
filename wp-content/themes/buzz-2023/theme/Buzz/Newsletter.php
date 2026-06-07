<?php
namespace Firefly\Buzz;

use Timber\Timber;
use Firefly\Timber\FireflyPost;

class Newsletter {

	protected $newsletter;

    public function __construct() {
		// if Buzz plugin is active, replace the homepage
		if( class_exists( 'FF_Newsletter' ) ) {
			add_action( 'pre_get_posts', [$this, 'replace_home'], 99 );
		}
	}

    /**
     * When we hit the root of the site, we want to show the latest issue
     *
     * @param [type] $template
     * @return void
     */
    public function replace_home( $query ) {


		if ( is_home() && $query->is_main_query() ) {
			// set the global post to the latest newsletter if available
			$latest = $this::get();

			if( $latest !== false ) {
				$post = $latest; // must be assigned to $post for setup_postdata to work properly
				setup_postdata( $post );
				$query->set( 'post_type', 'newsletter' );
				$query->set( 'p', $latest->ID );
				$query->set( 'is_single', true );
				$query->set( 'is_singular', true );

				// set template with block editor template single-newsletter
				// re-parse the query to correctly set the is_home / is_singular flag
				$query->parse_query();
			}
		}
	}

    /**
     * Get the newsletter by ID. If no post ID passed, returns latest newsletter
	 *
	 * @param 	{int}		$id 				The newsletter post ID
	 * @param 	{boolean}	$include_drafts 	Flag to get draft, pending and auto-draft newsletters alongside published
     *
     * @return FireflyPost
     */
    public static function get( $id=false, $include_drafts=false ) {

		$args = array(
			'post_type'		=> 'newsletter',
			'p'				=> $id,
			// include drafts as well? Needed to get articles attached to a draft newsletter
			'post_status'	=> $include_drafts ? ['publish', 'pending', 'draft', 'auto-draft', 'private'] : ['publish'],
		);
		$newsletter = Timber::get_post( $args, 'Firefly\Timber\FireflyPost' );

		// Given we successfully retrieved a newsletter, return the first object
        if( ! empty( $newsletter ) ) {
            return $newsletter;
		}

		// if no newsletter is returned and current post is a preview,
		if( is_preview() ) {
			global $post;

			// check if current post has same ID as the expected newsletter and return
			if( $id == $post->ID ) {
				return new FireflyPost($post);
			}

		}

        return false;
    }

}
