<?php
/**
 * The common template region for the Header.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Custom
 * @since Custom 1.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--meta name="viewport" content="width=device-width, initial-scale=1"-->
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
		wp_head();
	?>
</head>

<body <?php body_class(); ?>>
	<div id="wrapper" class="container">

		<!-- #header -->
		<div id="header" class="row">
			<div class="col-xs-12">

				<a class="logo"
				   href="<?php echo get_home_url(); ?>"
				   title="<?php bloginfo('name');?>"
				   >

				   	<h1 class="logo-text"><?php bloginfo('name'); ?></h1>
				</a>

				<div id="nav" class="navbar" role="navigation">

					<div class="navbar-header">
						<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="btn-label">Menu</span>
						</button>
					</div>

					<div class="collapse navbar-collapse">
						<div class="links">
							<span class="social-links">
								<a class="homelink" href="<?php echo get_home_url();?>"><i class="fa fa-home"></i> Home</a>
								<?php ff_social_links(); ?>
							</span>

							<?php
								// LOGIN/OUT LINK
								$loggedin = is_user_logged_in();
								$link_url = $loggedin ? wp_logout_url() : wp_login_url();
								$link_text = $loggedin ? "Sign Out" : "Sign In";
							?>
							<a class="login-link" href="<?php echo $link_url; ?>"><i class="fa fa-pencil"></i> <?php echo $link_text; ?></a>

						</div>

						<?php echo get_search_form(); ?>

						<div id="main-menu-hs" class="visible-xs">
							<?php /*wp_nav_menu(array('theme_location' 	=> 'primary',
													'container'         => NULL,
													'link_after'		=> '<span class="expand"></span>'));*/ ?>
							<?php
								if(is_home() || is_singular()) {

									if(is_home()) {
										$current_section = ff_get_posts_page();
									} else {
										$current_section = ff_get_current_section_post();
									}

									echo '<ul class="menu">';
									printf('<li><a class="back-link" href="%s"><i class="fa fa-chevron-left"></i> Back to my portal</a>', ff_get_my_portal_link());
									printf('<li class="menu-item-has-children open"><a href="%s">%s <span class="expand"></expand></a>',
												get_post_permalink($current_section->ID),
												$current_section->post_title);
									echo 		'<ul>';
									echo 			ff_get_page_sub_menu( NULL, array('link_after'		=> '<span class="expand"></span>') );
									echo 		'</ul>';
									echo 	'</li>';
									echo '</ul>';
								}
							?>
						</div>

					</div><!-- /.collapse -->

				</div><!-- /#nav -->
			</div><!-- /.col -->
		</div><!-- /#header -->




		<?php
		 /****************************
		  * begin main content area
		  ****************************/
		?>
		<div id="main" class="row">
