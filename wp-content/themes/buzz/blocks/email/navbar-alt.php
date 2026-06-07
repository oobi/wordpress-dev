<?php 
/*********************************************************************
 * Email View Nav Bar alternate
 *
 * This file must be called from within an email template
 *********************************************************************/ ?>

<tr><td id="nav">
	<?php 
	$current_newsletter = ff_get_newsletter($post->ID);	
	// Check issue title setting, display if on
	$show_issue_title = get_theme_mod( 'ff_issue_title_display' ); ?>

	<table border="0" cellpadding="0" cellspacing="15" width="640">
		<tr>
			<td width="320" class="date"><!-- contact -->
				<p class="navbar-text navbar-right hidden-xs">
					<?php if( $show_issue_title ) : ?>
						<span class="current-issue"><?php echo ff_get_newsletter_title( $current_newsletter ); ?></span>
						<span class="mdash">&mdash;</span>
					<?php endif; ?>
					<span class="issue-date"><?php echo ff_get_newsletter_date( 'd M Y', $current_newsletter ); ?></span>
				</p>
			</td>
			<td width="320" class="align-right view-full"><!-- nav links -->
				<a href="<?php echo ff_get_newsletter_url( $current_newsletter ); ?>">View Full Newsletter</a>
			</td>
		</tr>
	</table>

</td></tr>