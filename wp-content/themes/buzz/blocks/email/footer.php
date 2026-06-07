<?php 
/*********************************************************************
 * Email View Footer
 *
 * This file must be called from within an email template
 *********************************************************************/ ?>

<tr><td id="footer">

	<table border="0" cellpadding="0" cellspacing="15" width="640">
		<tr>
			<td width="320" valign="top"><!-- copyright -->
				&copy;
				<?php echo get_theme_mod( 'ff_copyright_text' ); ?>
				<?php echo strftime( '%Y' ); ?>
			</td>
			<td width="320" valign="top" class="align-right"><!-- website credit -->
				<a href="http://www.thebuzz.net.au" target="_blank" title="Powered by The Buzz">Powered by The Buzz</a>
			</td>
		</tr>
	</table>

</td></tr>