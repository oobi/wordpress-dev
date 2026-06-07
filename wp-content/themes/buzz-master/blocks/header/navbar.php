<?php
/*********************************************************************
 * The default navbar
 *********************************************************************/ ?>

<!-- main navigation -->
<nav id="nav" class="navbar">

		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#ff-navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		</div>

		<div id="ff-navbar-collapse" class="collapse navbar-collapse">
			<ul class="nav navbar-nav">

				<!-- handset only gadgets -->
				<div class="visible-xs handset-nav-top">
					<?php ff_share_links( ); ?>
					<?php get_search_form(); ?>
				</div>

				<!-- index link -->
				<li class="nav-index"><a href="<?php echo get_home_url(); ?>">Index</a></li>

				<!-- newsletter archive -->
				<li class="nav-archive"><a data-type="archive" href="<?php echo get_post_type_archive_link( 'newsletter' ); ?>" title="View past newsletter issues">Archive</a></li>

				<?php // optionally show navbar menu if set 
				if( has_nav_menu( 'navbar-menu' ) ) { 
					wp_nav_menu( array( 'theme_location' 	=> 'navbar-menu',
										'container'			=> false,
										'items_wrap' 		=> '%3$s' // remove wrapping UL
								) ); 
				} ?>

			</ul>

			<div class="hidden-xs">
				<?php get_search_form(); ?>
			</div>
		</div><!-- /.navbar-collapse -->


</nav><!--/#navbar -->