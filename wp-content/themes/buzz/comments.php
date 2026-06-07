<?php
/**
 * The comments form region.
 *
 * The area of the page that contains both current comments
 * and the comment form. The actual display of comments is
 * handled by a callback to firefly_comment() which is
 * located in the functions.php file.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
?>
	<?php
		$isSupported = post_type_supports(get_post_type(), 'comments');
		$isSome		 = have_comments();
		$isOpen		 = comments_open();
		$classes = array(
			$isSupported ? 'supported' : 'unsupported',
			$isSome ? 'some' : 'none',
			$isOpen ? 'open' : 'closed'
		);
	?>
	<div id="comments" class="<?php echo implode(' ', $classes) ?>">
	
	<?php if ( post_password_required() ) : ?>
		<p class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any comments.', 'firefly' ); ?></p>
	</div><!-- #comments -->
	<?php
			/* Stop the rest of comments.php from being processed,
			 * but don't kill the script entirely -- we still have
			 * to fully load the template.
			 */
			return;
		endif;
	?>

	<?php // You can start editing here -- including this comment! ?>
	
	<h2>Feedback</h2>
	<?php
	/* Always show what comments are present. */
	 if ( have_comments() ) : ?>
		
		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // comment page nav ?>
		<nav class="comment-nav above">
			<div class="previous"><?php previous_comments_link('Previous'); ?></div>
			<div class="next"><?php next_comments_link('Next'); ?></div>
		</nav>
		<?php endif; // end comment page nav ?>

		<ol class="commentlist">
			<?php
				/* Loop through and list the comments. Tell wp_list_comments()
				 * to use firefly_comment() to format the comments.
				 * If you want to overload this in a child theme then you can
				 * define firefly_comment() and that will be used instead.
				 * See firefly_comment() in firefly/functions.php for more.
				 */
				wp_list_comments( array( 'callback' => 'firefly_comment' ) );
			?>
		</ol>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // comment page nav  ?>
			<nav class="comment-nav below">
				<div class="previous"><?php previous_comments_link('Previous'); ?></div>
				<div class="next"><?php next_comments_link('Next'); ?></div>
			</nav>
		<?php endif; // end comment page nav
		
	?><?php
	/* If there are no comments and comments are closed then note it.
	 * But not on pages or post types that do not support comments. */
	elseif ( $isSupported && !$isOpen && !is_page()) :
	?>
		<p><?php _e( 'Comments are closed.', 'firefly' ); ?></p>
	<?php endif; ?>
	
	<?php
	/* Show the comment form only where comments are open */
		if ($isOpen) {
			comment_form(array(
				'title_reply'          => __( 'Post your own Feedback' ),
				'title_reply_to'       => __( 'Reply to %s' ),
				'cancel_reply_link'    => __( 'Cancel' ),
				'label_submit'         => __( 'Submit' ),
			));
		}
	?>

</div><!-- #comments -->
