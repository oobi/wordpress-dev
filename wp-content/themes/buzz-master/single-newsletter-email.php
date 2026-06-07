<?php
/**
 * Newsletter article EMAIL view
 */

// set to false to just echo without inlining (debug)
$inline = true;



// if unsubscribe link is not set, show error
if( !Buzz_Addon_Email_View::is_unsubscribe_set() ) {

	$message = Buzz_Addon_Email_View::unsubscribe_not_set_message();
	wp_die( $message );

}

// write to output buffer so we can inline this at the bottom
ob_start();

get_template_part('content-newsletter-email');

// capture output buffer and inline the result
$html = ob_get_clean();


if($inline) {
	// inline it
	$html = FF_Newsletter_Inline_CSS::inline_html($html);
}

// inject custom stylesheet for font
global $wp_styles;

// inject unsubscribe link
//$unsubscribe = Buzz_Addon_Email_View::get_unsubscribe_markup();
//$html = str_replace('%%BUZZ_UNSUBSCRIBE%%', $unsubscribe, $html);

if(array_key_exists('custom_font_css', $wp_styles->registered)) {
	// this is a text variable which will be built up of HTML content to inject into the BODY tag
	$injected = '';

	// create font CSS <link>
	if(array_key_exists('custom_font_css', $wp_styles->registered)) {
		$font_css_url  = $wp_styles->registered['custom_font_css']->src;
		$injected  .= '<link type="text/css" rel="stylesheet" href="' . $font_css_url . '">';
	}

	// create email <style> tag
	if(array_key_exists('newsletter_email_css', $wp_styles->registered)) {
		$email_css_url  = $wp_styles->registered['newsletter_email_css']->src;
		$injected  .= "\n" . '<style>' . file_get_contents($email_css_url) . '</style>';
	}

	// create theme-specific email <style> tag
	if(array_key_exists('theme_specific_email_css', $wp_styles->registered)) {
		$email_theme_css_url  = $wp_styles->registered['theme_specific_email_css']->src;
		$injected  .= "\n" . '<style>' . file_get_contents($email_theme_css_url) . '</style>';
	}

	// inject into email body
	if(!empty($injected)) {
		$html = preg_replace('@<body[^>]*>@', '$0' . $injected, $html);
	}
}

// output result
echo $html;