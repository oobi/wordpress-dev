<?php
/**
 * Newsletter article PRINT view (all articles in one page)
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
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
    ?> | Print</title>

    <?php
    /* IMPORTANT
     * Always call wp_head() just before the closing head
     * tag of your theme. Otherwise you will break many plugins
     * which may use this hook to add elements to head such
     * as styles, scripts, and meta tags. */
    wp_head();
    ?>
</head>

<body>
    <div id="wrapper"><div class="inner">

    <div id="banner">

        <?php
        // if a print header is specified, show it in place of text
        $print_header_image = get_theme_mod('ff_print_header_image');
        if( $print_header_image ) : ?>
            <img class="custom-header" src="<?php echo esc_url( $print_header_image ); ?>" alt="<?php bloginfo('title'); ?>">

        <?php
        // if header text is available, display it
        else : ?>

            <div class="header-content">
                <!-- logo -->
                <?php if ( get_theme_mod( 'ff_newsletter_logo' ) ) : ?>
                    <div class='site-logo'>
                        <img src='<?php echo esc_url( get_theme_mod( 'ff_newsletter_logo' ) ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'>
                    </div>
                <?php endif; ?>

                <!-- title and tagline -->
                <div class="site-text">
                    <h1 class="site-title"><?php bloginfo( 'name' ); ?></h1>
                    <?php
                    $description = get_bloginfo( 'description', 'display' );
                    if ( $description || is_customize_preview() ) : ?>
                        <p class="site-description"><?php echo $description; ?></p>
                    <?php endif; ?>
                </div>

            </div>


        <?php endif; ?>
    </div>

    <div class="issue-date">
		<?php // Check issue title setting, display if on
		$show_issue_title = get_theme_mod( 'ff_issue_title_display' ); ?>
		<?php if( $show_issue_title ) {
			the_title();
			echo " - "; 
		}
		echo ff_get_newsletter_date('d M Y', $post); ?>
	</div>

    <?php while ( have_posts() ) : the_post(); // start loop ?>

    	<div id="main" class="print">
    	<?php
    		// get articles
    		$args = array(
    			'parent-id'			=> get_the_ID()
    		);
    		$articles = ff_get_article_query($args);

    		// display articles
    		while ( $articles->have_posts() ) : $articles->the_post(); ?>

    			<div class="article-print-single">
    			    <?php if(has_post_thumbnail()) : ?>
        				<div class="article-thumb">
                            <?php the_post_thumbnail('banner'); ?>
        				</div><!-- /.article-thumb -->
    				<?php endif; ?>
    				<div class="article-text">
    					<h1><?php the_title(); ?></h1>
    					<?php the_content(); ?>
    				</div><!-- /.article-text -->
    			</div><!-- /.article-print-single -->

    		<?php endwhile; ?>
    	</div><!-- #index-articles -->

    <?php endwhile; // end loop ?>

    </div></div><!-- /#wrapper -->

    <script>window.print();</script>

</body>
</html>