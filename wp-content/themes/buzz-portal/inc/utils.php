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
 * NAVIGATION UTILS
 **********************************************************************************************/

/**
 * get the submenu pages of a given page
 */
if ( ! function_exists( 'ff_get_page_sub_menu' ) ) :
function ff_get_page_sub_menu($page=null, $args=array()){
	global $post;
	if(!$page) {
		if(is_home() || is_single()) {
			$page = ff_get_posts_page();
		} else {
			$page = $post;
		}
	}

	$defaults = array(
		'title_li'		=> NULL,
		'echo'			=> FALSE,
	);

	$args = array_merge($defaults, $args);

	if(!$page->post_parent){
		// will display the subpages of this top level page
		$args['child_of'] = $page->ID;
		$children = wp_list_pages($args);
	}else{
		// diplays only the subpages of parent level
		//$children = wp_list_pages("title_li=&child_of=".$post->post_parent."&echo=0");

		if($page->ancestors)
		{
			// now you can get the the top ID of this page
			// wp is putting the ids DESC, thats why the top level ID is the last one
			$ancestors = get_post_ancestors($page);
			$last_ancestor = end( $ancestors );
			$args['child_of'] = $last_ancestor;
			$children = wp_list_pages($args);
			// you will always get the whole subpages list
		}
	}

	return $children;
}
endif;

/**
 * display the submenu pages of a given page
 */
if ( ! function_exists( 'ff_the_page_sub_menu' ) ) :
function ff_the_page_sub_menu($page=null, $args=array()) {
	global $post;
	if(!$page) {
		if(is_home() || is_single()) {
			$page = ff_get_posts_page();
		} else if(!is_page()){
			return "";
		} else {
			$page = $post;
		}
	}

	$children = ff_get_page_sub_menu($page, $args);
	echo "<ul class='menu'>";
	echo $children;
	echo "</ul>";
}
endif;

/**
 * get the section of a given page
 */
if ( ! function_exists( 'ff_get_current_section' ) ) :
function ff_get_current_section($page=null) {
	global $post;
	if(!$page) {
		if(is_home() || is_single()) {
			$page = ff_get_posts_page();
		}  else if(is_search()) {
			return 'Search';
		} else if($post != NULL){
			$page = $post;
		} else {
			return '';
		}
	}
	$p = ff_get_current_section_post($page);
	if($p)  return $p -> post_name;
	else 	return '';
}
endif;

/**
 * display the section of a given page
 */
if ( ! function_exists( 'ff_the_current_section' ) ) :
function ff_the_current_section($page=null) {
	echo ff_get_current_section($page);
}
endif;

/**
 * display the section of a given post
 */
if ( ! function_exists( 'ff_get_current_section_post' ) ) :
function ff_get_current_section_post($page=null) {
	global $post;
	if(!$page) {
		if(is_home() || is_single()) {
			$page = ff_get_posts_page();
		} else if($post != NULL){
			$page = $post;
		} else {
			return NULL;
		}
	}
	$result = $page;

	// get ancestors array
	if(!isset($page -> ancestors)) return null;

	if($page -> ancestors) {
		$ancestors = get_post_ancestors($page);
		$first_ancestor = end( $ancestors );
		$result    = get_page($first_ancestor);
	}

	// complete
	return $result;
}
endif;

/**
 * display the section of a given post
 */
if ( ! function_exists( 'ff_the_current_section_post' ) ) :
function ff_the_current_section_post($page=null) {
	echo ff_get_current_section_post($page);
}
endif;

/**
 * Get the permalink of a page by its title
 */
if ( ! function_exists( 'ff_get_page_link_by_title' ) ) :
function ff_get_page_link_by_title($page_slug) {
	$page = get_page_by_title($page_slug);
	if ($page) :
		return get_permalink( $page->ID );
	else :
		return "#";
	endif;
}
endif;

/**
 * Display the permalink of a page by its title
 */
if ( ! function_exists( 'ff_the_page_link_by_title' ) ) :
function ff_the_page_link_by_title($page_slug) {
	echo ff_get_page_link_by_title($page_slug);
}
endif;

/**
 * Get the ID of a page by its title
 */
if ( ! function_exists( 'ff_get_page_id_by_title' ) ) :
function ff_get_page_id_by_title($page_slug) {
	$page = get_page_by_title($page_slug);
	if ($page) :
		return $page->ID;
	else :
		return "";
	endif;
}
endif;

/**
 * Display the ID of a page by its title
 */
if ( ! function_exists( 'ff_the_page_id_by_title' ) ) :
function ff_the_page_id_by_title($page_slug) {
	echo ff_get_page_id_by_title($page_slug);
}
endif;


/***********************************************************************************************
 * POSTS PAGE UTILS
 **********************************************************************************************/

/**
 * Retrieves the posts page
 */
if ( ! function_exists( 'ff_get_posts_page' ) ) :
function ff_get_posts_page() {
	$isOption = (get_option( 'show_on_front') == 'page');
	if($isOption) {
		$posts_page_id = get_option('page_for_posts');
		$result    	   = get_page( $posts_page_id );
	} else {
		$result = NULL;
	}
	return $result;
}
endif;

/**
 * Retrieves the full path of the posts page
 */
if ( ! function_exists( 'ff_get_posts_page_url' ) ) :
function ff_get_posts_page_url() {
	$posts_page = ff_get_posts_page();
	if($posts_page) {
		$result	= site_url( get_page_uri( $posts_page -> ID ) );
	} else {
		$result = site_url();
	}
	return $result;
}
endif;

/**
 * Displays the full path of the posts page
 */
if ( ! function_exists( 'ff_the_posts_page_url' ) ) :
function ff_the_posts_page_url() {
	echo ff_get_posts_page_url();
}
endif;

/**
 * Retrieves the title of the posts page
 */
if ( ! function_exists( 'ff_get_posts_page_title' ) ) :
function ff_get_posts_page_title() {
	$posts_page = ff_get_posts_page();
	if ($posts_page) {
		$title	= $posts_page->post_title;
	} else {
		$title	= 'News';
	}
	$type = ff_get_posts_archive_type();
	if (empty($type)) $result = $title;
	else 			  $result = $title . ': ' . $type . ' Archives';
	return	apply_filters('posts_page_title', $result);
}
endif;

/**
 * Displays the title of the posts page
 */
if ( ! function_exists( 'ff_the_posts_page_title' ) ) :
function ff_the_posts_page_title() {
	echo ff_get_posts_page_title();
}
endif;

/**
 * Retrieves the type of the posts page
 */
if ( ! function_exists( 'ff_get_posts_archive_type' ) ) :
function ff_get_posts_archive_type() {
	$type = get_post_type();
	if(is_home()){
		$result = '';
	} else if (is_author()){
		$result = 'author';
	} else if (is_category()) {
		$result = 'category';
	} else if (is_tag()) {
		$result = 'tag';
	} else if (is_day()) {
		$result = 'day';
	} else if (is_month()) {
		$result = 'month';
	} else if (is_year()) {
		$result = 'year';
	} else {
		$result = '';
	}
	return	apply_filters('posts_archive_type', $result);
}
endif;

/**
 * Displays the type of the posts page
 */
if ( ! function_exists( 'ff_the_posts_archive_type' ) ) :
function ff_the_posts_archive_type() {
	echo ff_get_posts_archive_type();
}
endif;


/***********************************************************************************************
 * CONTENT
 **********************************************************************************************/

/**
 * Displays the Edit Post link
 */
if ( ! function_exists( 'ff_edit_post_link' ) ) :
function ff_edit_post_link() {
	edit_post_link( __( 'Edit', 'firefly' ), '<span class="edit-link">', '</span>' );
}
endif;

/**
 * Output a top link
 */
function ff_toplink() {
	echo '<a href="#" class="toplink" onclick="window.scrollTo(0,0);return false;">Back to Top</a>';
}

/***********************************************************************************************
 * POST/PAGE TEMPLATE UTILS
 **********************************************************************************************/

/**
 * Display navigation to next/previous pages when applicable
 */
if ( ! function_exists( 'ff_content_nav' ) ) :
function ff_content_nav( $nav_id ) {
	global $wp_query;
	if ( $wp_query->max_num_pages > 1 ) : ?>
		<nav id="<?php echo $nav_id; ?>">
			<h3 class="assistive-text"><?php _e( 'Post navigation', 'firefly' ); ?></h3>
			<div class="nav-previous"><?php next_posts_link( '<span class="meta-nav">&larr;</span> Older posts' ); ?></div>
			<div class="nav-next"><?php previous_posts_link( 'Newer posts <span class="meta-nav">&rarr;</span>' ); ?></div>
		</nav><!-- #nav-above -->
	<?php endif;
}
endif;

/**
 * Display navigation to next/previous set of posts when applicable.
 * 	@param $middle_size {integer} The number of links included between the active number and the start/end/ellipses
 * 			(eg. $middle_size=2: Previous 1 ... 2 3 [4] 5 6 ... 7 Next)
 */
if ( ! function_exists( 'ff_paging_nav' ) ) :
function ff_paging_nav( $middle_size=1 ) {
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
 * Template for comments and pingbacks.
 */
if ( ! function_exists( 'ff_comment_template' ) ) :
function ff_comment_template( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'firefly' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( 'Edit', 'firefly' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<?php
			$avatar_size = ( '0' != $comment->comment_parent )? 40 : 80;
			echo get_avatar( $comment, $avatar_size );
		?><div class="comment-main">
			<p><?php echo firefly_posted_on(); ?></p>
			<?php if ( $comment->comment_approved == '0' ) : ?>
				<?php _e( 'Your comment is awaiting moderation.', 'firefly' ); ?>
				<br />
			<?php endif; ?>
			<?php comment_text(); ?>
			<p><?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'firefly' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?></p>
		</div>
		<?php edit_comment_link( __( 'Edit', 'firefly' ), '', '' ); ?>
	<?php
			break;
	endswitch;
}
endif;

/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
if ( ! function_exists( 'ff_posted_on' ) ) :
function ff_posted_on($useAuthor=false) {
	$authorURL = esc_url(get_author_posts_url(get_the_author_meta('ID')));
	echo 'Posted on ' . esc_attr(get_the_date('l jS F Y'));
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
		$text = $text . $args['ellipsis'];
		$text = $text . sprintf($args['more_link'], $permalink);
	} else {
		$text = implode(' ', $words);
	}

	return $text;
}
endif;

/**
 * Display the caption for the hero image.
 */
if ( ! function_exists( 'ff_the_hero_image_caption' ) ) :
function ff_the_hero_image_caption($postID=false) {
	echo firefly_get_hero_image_caption($postID);
}
endif;

/**
 * Get the caption for the hero image.
 */
if ( ! function_exists( 'ff_get_hero_image_caption' ) ) :
function ff_get_hero_image_caption($postID=false) {
  global $post;
  $thumbnail_id    = get_post_thumbnail_id(($postID)? $postID : $post->ID);
  $thumbnail_image = get_posts(array('p' => $thumbnail_id, 'post_type' => 'attachment'));
  if ($thumbnail_image && isset($thumbnail_image[0])) {
    return	$thumbnail_image[0]->post_excerpt;
  }
}
endif;

/**
 * Echo the bread crumb routine
 */
if ( ! function_exists( 'ff_the_breadcrumbs' ) ) :
function ff_the_breadcrumbs($options=array()){
	echo ff_get_breadcrumbs($options);
}
endif;

/**
 * Single bread crumb routine for all pages
 */
if ( ! function_exists( 'ff_get_breadcrumbs' ) ) :
function ff_get_breadcrumbs($options=array()) {
    global $post;
 	global $wp_query;

  	// parameters
	$defaults = array(
		'wrapper_before'=> '<div id="breadcrumb">',
		'wrapper_after'	=> '</div>',
		'delimiter'		=> ' &gt; ',
		'home'			=> 'Home',						// text for the 'Home' link
		'before'		=> '<span class="current">',	// tag before the current crumb
		'after'			=> '</span>',					// tag after the current crumb
	);
	$args = array_merge($defaults,$options);

  	// locals
  	$wrapper_before = $args['wrapper_before'];
	$wrapper_after 	= $args['wrapper_after'];
	$delimiter 		= $args['delimiter'];
	$home 			= $args['home'];
	$before 		= $args['before'];
 	$after 			= $args['after'];
    $homeLink 		= get_bloginfo('url');
	$isFront 		= is_front_page() || is_404();
	$postsPage		= ff_get_posts_page_title();
	$output 		= "";

 	// the root is always home
    $output .= $wrapper_before;
	$output .=  '<a href="' . $homeLink . '">' . $home . '</a>';

	// sometimes there is more
    if ( !$isFront ) $output .=  $delimiter;

 	// home page
 	if ( $isFront ) {
		/* nothing further */

 	// single posts
    } elseif ( is_single() && !is_attachment() ) {

		// custom post types
		if ( get_post_type() != 'post' ) {
			$post_type = get_post_type_object(get_post_type());
			$slug	   = $post_type->rewrite;
			$output .=  '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->name . '</a>' . $delimiter;
			$output .=  $before . get_the_title() . $after;

		// standard posts
		} else {
			$output .=  '<a href="' . ff_get_posts_page_url() . '">' . ff_get_posts_page_title() . '</a>';
			$output .=  $delimiter;
			$output .=  $before . get_the_title() . $after;
		}

	// attachment
    } elseif ( is_attachment() ) {
		$parent = get_post($post->post_parent);
		$cat 	  = get_the_category($parent->ID);
		if (!empty($cat)) {
			$output .=  get_category_parents($cat[0], TRUE, $delimiter);
		}
		$output .=  '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a>';
		$output .=  $delimiter;
		$output .=  $before . get_the_title() . $after;

	// top level pages
    } elseif ( is_page() && !$post->post_parent ) {
		$output .=  $before . get_the_title() . $after;

	// child pages
	} elseif ( is_page() && $post->post_parent ) {
		$parent_id   = $post->post_parent;
		$breadcrumbs = array();
		while ($parent_id) {
			$page 		   = get_page($parent_id);
			$breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
			$parent_id     = $page->post_parent;
		}
		$breadcrumbs = array_reverse($breadcrumbs);
		foreach ($breadcrumbs as $crumb) $output .=  $crumb . $delimiter;
		$output .=  $before . get_the_title() . $after;

 	// just the search page
    } elseif ( is_search() ) {
		$output .=  $before . 'Search results for "' . get_search_query() . '"' . $after;

	// posts page
    } elseif ( is_home() ) {
    	$output .=  $before . ff_get_posts_page_title() . $after;
	}

 	// pagination
 	$pages = $wp_query->max_num_pages;
    if ( $pages > 1 ) {
    	$page = max(get_query_var('paged'), 1);
      	$output .=  '<span class="page-num"> (page ' . $page . ' of ' . $pages . ')</span>';
    }

    $output .=  $wrapper_after;

	return $output;
}
endif;


/***********************************************************************************************
 * WP POST QUERIES
 **********************************************************************************************/

/**
 * Retrieve posts per WP_Query using common firefly default values.
 *
 * Specific custom fields may be specified in a 'meta' argument as field-value pairs.
 * Each is added to the meta_query object as a direct equals comparison. Keys must be
 * per 'types' plugin as wpcf is prepended.
 */
if ( ! function_exists( 'ff_query_posts' ) ) :
function ff_query_posts($options=array()) {

	$isOldWordPress = version_compare( $GLOBALS['wp_version'], '3.7', '<' );

	$defaults = array(
		'post_type'		=> 'post',
		'post_status'	=> 'publish',
		'paged'			=> 0,
		'meta_query'	=> array(),
		'orderby' 		=> $isOldWordPress ? 'post_date' : "date",
		'order'	  		=> apply_filters('posts_order', 'DESC')
	);
	$args = array_merge($defaults,$options);

	// custom query fields
	if (isset($options['meta'])) {
		$element = array();
		$prefix  = apply_filters('meta_key_prefix', 'wpcf-');
		foreach($options['meta'] as $key => $value) {
			$element = array(
				'key' 		=> $prefix . $key,
				'value' 	=> $value,
				'compare'	=> '='
			);
			array_push($args['meta_query'], $element);
		}
	}

	// return query
    return new WP_Query($args);
}
endif;

/**
 * Obtains an array of posts per firefly_query_posts.
 */
if ( ! function_exists( 'ff_get_posts' ) ) :
function ff_get_posts($options=array()) {
	$q = firefly_query_posts($options);
	return $q -> posts;
}
endif;

/**
 * Call 'get_post_meta' with key prefixes for 'types' plugin.
 */
if ( ! function_exists( 'ff_get_meta' ) ) :
function ff_get_meta($postid, $name, $single=true) {
	$prefix = apply_filters('meta_key_prefix', 'wpcf-');
	$key    = $prefix . $name;
	return get_post_meta($postid, $key, $single);
}
endif;
