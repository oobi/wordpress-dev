<?php

/**
 * Inline CSS for email templates
 *
 * @package	ff_newsletter
 * @subpackage ff_newsletter/includes
 * @author	 Firefly Interactive
 */
class FF_Newsletter_Inline_CSS
{

	/**
	 * Inlines styles for an external php file and echos it. This can be used as an analog to the built in
	 * WordPress function 'inline_template('slug', 'name');
	 *
	 * Usage : FF_Newsletter_Inline_CSS::inline_template_part('my-template-slug', 'my-template-name', array('my-css-file.css'));
	 *
	 * Any unrecognised options are considered to be wildcard replacements. Each given
	 * key will be found within the stylesheet and substituted with its corresponding
	 * value before the css is applied to the template.
	 *
	 * IMPORTANT:
	 *  - Selectors are processed lower case. Use hyphenated lower case in place of camel case.
	 *  - Do *not* overload selectors of the same precedence. Use specific selectors in each case.
	 *  - Inheritance appears to be more generous than recent browsers. Use specific selectors.
	 *
	 * @since	3.0.0
	 * @access  public
	 */
	public static function inline_template_part($slug, $name = null, $css = array())
	{

		// output template into a buffer
		ob_start();
		get_template_part($slug, $name);
		$html = ob_get_clean();

		$result = self::inline_html($html, $css);

		echo $result;
	}

	/**
	 * Inlines styles from string
	 * Usage: FF_Newsletter_Inline_CSS::inline_html($html_string, array('my-css-file.css'));
	 * @see inline_template_part
	 */
	public static function inline_html($html, $css = array())
	{
		require_once('inline-style/process_html.php');

		// Ensure UTF-8 is handled
		libxml_use_internal_errors(true);
		$doc = new DOMDocument('1.0', 'UTF-8');

		// Directly load the HTML (without injecting XML declaration)
		$doc->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

		// This will output HTML with characters converted to named/numeric entities
		$ascii = $doc->saveHTML();

		// Pass to inliner
		$result = process_html_string($ascii, NULL, FALSE, $css);

		return $result;
	}
}
