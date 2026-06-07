<?php
/**
 * Convenience methods for CMS building
 *
 * @package WordPress
 * @subpackage Firefly
 * @since Firefly 1.0
 */


/**
 * Echos name of the current template
 */
if ( ! function_exists( 'ff_the_template_name' ) ) :
function ff_the_template_name() {
	echo get_template_name();
}
endif;

/**
 * Retrieves the name of the current template
 */
if ( ! function_exists( 'ff_get_template_name' ) ) :
function ff_get_template_name() {
    return $GLOBALS['current_theme_template'];
}
endif;

/**
 * Echos name of the theme based on its subdirectory
 */
if ( ! function_exists( 'ff_the_theme_name' ) ) :
function ff_the_theme_name() {
	echo get_theme_name();
}
endif;

/**
 * Returns name of the theme based on its subdirectory
 */
if ( ! function_exists( 'ff_get_theme_name' ) ) :
function ff_get_theme_name() {
	$url  = strtolower(preg_replace("/\W+/", "_", ff_get_theme_directory()));
	$name = preg_replace("/.*_/", "", $url);
	return $name;
}
endif;

/**
 * Echos base directory of the theme
 */
if ( ! function_exists( 'ff_the_theme_directory' ) ) :
function ff_the_theme_directory() {
	echo ff_get_theme_directory();
}
endif;

/**
 * Returns the base directory of the theme
 */
if ( ! function_exists( 'ff_get_theme_directory' ) ) :
function ff_get_theme_directory() {
	return dirname( get_bloginfo('stylesheet_url') );
}
endif;

/***********************************************************************************************
 * CONTENT
 **********************************************************************************************/

/**
 * Output a top link
 */
if ( ! function_exists( 'ff_toplink' ) ) :
function ff_toplink() {
	echo '<a href="#" class="toplink" onclick="window.scrollTo(0,0);return false;">Back to Top</a>';
}
endif;

// retrieves the attachment ID from the file URL
if ( ! function_exists( 'ff_get_image_id' ) ) :
function ff_get_image_id($image_url) {
	global $wpdb;
	$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ));
	return $attachment[0];
}
endif;

/***********************************************************************************************
 * POST/PAGE TEMPLATE UTILS
 **********************************************************************************************/

/**
 * Display navigation to next/previous set of posts when applicable.
 * 	@param $middle_size {integer} The number of links included between the active number and the start/end/ellipses
 * 			(eg. $middle_size=2: Previous 1 ... 2 3 [4] 5 6 ... 7 Next)
 */
if ( ! function_exists( 'ff_paging_nav' ) ) :
function ff_paging_nav( $middle_size=4 ) {
	// Don't print empty markup if there's only one page.
	if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
		return;
	}

	$paged        = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
	$pagenum_link = html_entity_decode( get_pagenum_link() );
	$query_args   = array();
	$url_parts    = explode( '?', $pagenum_link );

	if ( isset( $url_parts[1] ) ) {
		wp_parse_str( $url_parts[1], $query_args );
	}

	$pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
	$pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

	$format  = $GLOBALS['wp_rewrite']->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
	$format .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit( 'page/%#%', 'paged' ) : '?paged=%#%';

	// Set up paginated links.
	$links = paginate_links( array(
		'base'     => $pagenum_link,
		'format'   => $format,
		'total'    => $GLOBALS['wp_query']->max_num_pages,
		'current'  => $paged,
		'mid_size' => $middle_size,
		'add_args' => array_map( 'urlencode', $query_args ),
		'prev_text' => __( 'Previous', 'firefly' ),
		'next_text' => __( 'Next', 'firefly' ),
	) );

	// display if links to show
	if ( $links ) :
	?>
		<div class="navigation paging-navigation" role="navigation">
			<div class="pagination loop-pagination">
				<?php echo $links; ?>
			</div><!-- .pagination -->
		</div><!-- .navigation -->
	<?php
	endif;
}
endif;

/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
if ( ! function_exists( 'ff_posted_on' ) ) :
function ff_posted_on($useAuthor=false) {
	$authorURL = esc_url(get_author_posts_url(get_the_author_meta('ID')));
	echo 'Posted on ' . esc_attr(get_the_date());
	if ($useAuthor) {
		echo '<span class="author"> by <a href="' . $authorURL . '">' . get_the_author() . '</a></span>';
	}
}
endif;

/**
 * Display the excerpt
 */
if ( ! function_exists( 'ff_the_excerpt' ) ) :
function ff_the_excerpt($length = -1, $id=false, $options=array()) {
	echo ff_get_excerpt($length, $id, $options);
}
endif;

/**
 * An except generate with optional length
 */
if ( ! function_exists( 'ff_get_excerpt' ) ) :
function ff_get_excerpt($excerpt_length = -1, $id = false, $options=array()) {
	$text 			= '';
	$permalink 		= '';

	$defaults 		= array(
		"ellipsis"		=> '...',
		"more_link"		=> ' <a href="%s" class="more"><span>Read More</span></a>'
	);
	$args = array_merge($defaults,$options);

	// if length is negative (default) just return the default WP exerpt
	if( $excerpt_length < 0) {
		return get_the_excerpt();
	}

	// otherwise we can get the excerpt text for the post ID
	if($id) {
		$the_post 	= get_post( $my_id = $id );
		$text 		= ($the_post->post_excerpt) ? $the_post->post_excerpt : $the_post->post_content;
		$permalink 	= get_permalink($the_post -> ID);
	}
	// or if ID is not specified, get excerpt text for current post
	else {
		global $post;
		$text = ($post->post_excerpt) ? $post->post_excerpt : get_the_content('');
		$permalink 	= get_permalink($post -> ID);
	}

	// strip shortcodes etc from excerpt
	$text = strip_shortcodes( $text );
	$text = apply_filters('the_content', $text);
	$text = str_replace(']]>', ']]&gt;', $text);
	$text = strip_tags($text);

	// truncate into the requisite number of words
	$words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
	if ( count($words) > $excerpt_length ) {
		array_pop($words);
		$text = implode(' ', $words);
		$text .= $args['ellipsis'];
	} else {
		$text = implode(' ', $words);
	}

	$text .= sprintf($args['more_link'], $permalink);

	return $text;
}
endif;

/*****************************************************************************
 * SOCIAL MEDIA
 *****************************************************************************/

if(! function_exists('ff_share_links')) :
function ff_share_links( $include_print=FALSE, $p=NULL ) {
	global $post;

	if(!$p) $p = $post;
	if(!$p) return '';

	// if $include_print is true, check if Print View plugin is active
	// if not active, do not set $include_print to false
	if( $include_print ) {
		$include_print = defined( 'BUZZ_ADDON_PRINT_VIEW' );
	}

	// check social sharing settings and create share link array
	$share_links = array();
	get_theme_mod( 'ff_social_sharing_print' ) && $include_print 	? array_push( $share_links, 'print' ) 		: '';
	get_theme_mod( 'ff_social_sharing_facebook' ) 					? array_push( $share_links, 'facebook' ) 	: '';
	get_theme_mod( 'ff_social_sharing_twitter' ) 					? array_push( $share_links, 'twitter' ) 	: '';
	get_theme_mod( 'ff_social_sharing_linkedin' ) 					? array_push( $share_links, 'linkedin' ) 	: '';

	// decide whether to use brand colours (theme option)
	$brand = get_theme_mod('ff_social_sharing_brandcolors') ? 'class="brand"' : '';

	// display share links
	echo ff_get_share_links(
		$share_links,
		array(
			'before'		=> sprintf('<div id="social" %s>', $brand),
			'after'			=> '</div>',
			// override icons
			'icon_css'		=> array(
				'print'		=> "fa fa-print",
				'twitter'	=> "fa fa-twitter",
				'facebook'	=> "fa fa-facebook",
				'linkedin'	=> "fa fa-linkedin"
			)
		),
		$p
	);

}
endif;

// get social media share links
// specify array of social media services in the desired order and receive the share link HTML
// e.g. ff_get_share_links(array('twitter','facebook','linkedin'));
if(! function_exists('ff_get_share_links')) :
function ff_get_share_links($services=array(), $opt=array(), $p=NULL) {
	global $post;
	$links = array();

	if(!$p) $p = $post;
	if(!$p) return $links;

	// default options
	$defaults = array(
		// wrapper
		'before'		=> '',
		'after'			=> '',
		// text
		'show_text'		=> TRUE,
		'before_text'	=> '<span class="share-text">',
		'after_text'	=> '</span>',
		// icon
		'icon_template'	=> '<i class="%1$s"></i>',
		// icon css
		'icon_css'		=> array(
			'print'		=> 'fa fa-print',
			'twitter'	=> 'fa fa-twitter',
			'facebook'	=> 'fa fa-facebook-official',
			'linkedin'	=> 'fa fa-linkedin-square',
		),
		'link_template' => '<a href="%1$s" class="social-link social-%2$s" %5$s>%3$s%4$s</a>'
	);

	// merge user options with defaults
	$o = array_merge($defaults, $opt);

	// get URLs
	$urls = ff_get_share_urls($p);

	// output
	$output = $o['before'];

	foreach($services as $servicename) {
		$key 	 = strtolower($servicename);
		$service = $urls[$key];

		// get link url, text and icon
		$url 	 = $service['link'];
		$text	 = $o['show_text'] ? $o['before_text'] . $service['share_text'] . $o['after_text'] : '';
		$icon_css = array_key_exists($key, $o['icon_css']) ? $o['icon_css'][$key] : '';
		$icon 	 = sprintf($o['icon_template'], $icon_css);
		$target	 = $key == 'print' ? '' : 'target="_blank"';

		// render link
		$link 	 = sprintf($o['link_template'], $url, $key, $icon, $text, $target);
		$output .= $link;

	}

	$output .= $o['after'];

	return $output;

}
endif;

// get social media share link URLs
if(! function_exists('ff_get_share_urls')) :
function ff_get_share_urls($p=NULL) {
	global $post;
	$urls = array();

	if(!$p) $p = $post;
	if(!$p) return $urls;

	// stuff to share
	$share_text = urlencode($p->post_title);
	$share_url  = urlencode(get_permalink($p->ID));

	// share URLs
	$urls = array(
		'twitter'  	=> array(
			'name' 			=> 'Twitter',
			'share_text'	=> 'Share',
			'link' 			=> sprintf("https://twitter.com/intent/tweet?text=%s&url=%s", $share_text, $share_url),
		),
		'linkedin' 	=> array(
			'name' 			=> 'LinkedIn',
			'share_text'	=> 'Share',
			'link' 			=> sprintf("http://www.linkedin.com/shareArticle?mini=true&url=%s&title=%s&ro=false&summary=&source=", $share_url, $share_text),
		),
		'facebook' 	=> array(
			'name' 			=> 'Facebook',
			'share_text'	=> 'Share',
			'link' 			=> sprintf("https://www.facebook.com/sharer/sharer.php?u=%s", $share_url)
		),
	);

	// check if Print View plugin is active
	$is_print_active = defined( 'BUZZ_ADDON_PRINT_VIEW' );

	// if Print View plugin is active, add print to $urls array
	if( $is_print_active ) {
		$urls['print'] = array(
			'name'			=> 'Print',
			'share_text'	=> 'Print',
			'link'			=> 'javascript:window.print()'//ff_get_print_url( $p->ID )
		);
	}

	return $urls;
}
endif;
