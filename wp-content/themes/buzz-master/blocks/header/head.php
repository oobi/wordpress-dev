<?php 
/*********************************************************************
 * Contents of <head>
 *********************************************************************/ ?>

<meta charset="<?php bloginfo('charset'); ?>">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php
	/*
		* Print the <title> tag based on what is being viewed.
		*/
	global $page, $paged;

	wp_title('|', true, 'right');

	// Add the blog name.
	bloginfo('name');

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo('description', 'display');
	if($site_description &&(is_home() || is_front_page())) {
		echo " | $site_description";
	}

	// Append a page number if necessary:
	if($paged >= 2 || $page >= 2) {
		echo ' | ' . sprintf(__('Page %s', 'firefly'), max($paged, $page));
	}
?></title>

<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

<?php
/* We add some JavaScript to pages with the comment form
	* to support sites with threaded comments (when in use). */
if(is_singular() && get_option('thread_comments')) {
	wp_enqueue_script('comment-reply');
}

/* IMPORTANT
	* Always call wp_head() just before the closing head
	* tag of your theme. Otherwise you will break many plugins
	* which may use this hook to add elements to head such
	* as styles, scripts, and meta tags. */
wp_head(); ?>