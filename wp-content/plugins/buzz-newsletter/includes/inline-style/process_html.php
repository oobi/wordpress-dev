<?php
require_once('url_to_absolute.php');
require_once('simple_html_dom.php');
require_once('net_url2/URL2.php');

// autoload requirements for InlineStyle
require_once('vendor/autoload.php');
use \InlineStyle\InlineStyle;

/*
Process HTML from string input
- convert all URLs to absolute
- inline CSS
*/
function process_html_string($htmlString, $baseURI="", $skipProcessing=FALSE, $css=array()) {
	// strip all script tags from htmlString
	$htmlString = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $htmlString);

	// Create DOM from HTML string
	$html = str_get_html($htmlString);

	// if URI blank use self
	if(empty($baseURI)) {
		$baseURI = dirname($_SERVER['SERVER_PROTOCOL']) . '://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
	}

	// skip inliner means we just return raw HTML
	if($skipProcessing) {
		return $html;
	}
	// otherwise process the HTML
	else {
		return process_html($html, $baseURI, $css);
	}
}


/*
Process HTML downloaded from URL input to
- convert all URLs to absolute
- inline CSS
*/
function process_html_url($uri, $baseURI="", $skipProcessing=FALSE) {
	// Create DOM from HTML string
	$html = file_get_html($uri);

	// optionally specify different base URI
	if(empty($baseURI)) {
		$baseURI = $uri;
	}

	// skip inliner means we just return raw HTML
	if($skipProcessing) {
		return $html;
	}
	// otherwise process the HTML
	else {
		return process_html($html, $baseURI);
	}
}

/*
Process HTML DOM object
- convert all URLs to absolute
- inline CSS
*/
function process_html($html, $uri, $css=array()) {
	// don't freak out about non-standard tags
	libxml_use_internal_errors(true);

	// URI of the resource for resolving
	$uri = new Net_URL2($uri);
	$baseURI = $uri;

	// remove script
	foreach($html ->find('script') as $elem) {
    	$elem->outertext = '';
    }

	// if there's a base href then use that as baseURI
	foreach ($html->find('base[href]') as $elem) {
	    $baseURI = $uri->resolve($elem->href);
	}

	// convert src attribs
	foreach ($html->find('*[src]') as $elem) {
	    $elem->src = htmlentities($baseURI->resolve($elem->src)->__toString());
	}

	// convert HREF attribs
	foreach ($html->find('*[href]') as $elem) {
		// ignore BASE tag
		// ingore any URLs that look like placeholder strings
		// if we DO find one, then store its original URL because the inliner will munge it
	    if (   strtoupper($elem->tag) === 'BASE'
			|| preg_match('/%%.*%%/', $elem->href)       // href="%....%"
			|| preg_match('/^[^\w\/]/', $elem->href) )   // starts with something other than a word char or slash
		{
				$elem->setAttribute('buzz-original-href', $elem->getAttribute('href'));
				continue;
		}
	   $elem->href = htmlentities($baseURI->resolve($elem->href)->__toString());
	}

	// convert FORM actions
	foreach ($html->find('form[action]') as $elem) {
	    $elem->action = htmlentities($baseURI->resolve($elem->action)->__toString());
	}

	// convert background URLs in style block
	// TODO: convert URLs found in <style>...</style> and <node style="...">

	// invoke style inliner
	$inlineHTML = new InlineStyle($html->save());

	$all_styles = array();
	$defined_styles = array();

	///////////////////////////////////////////////////////////////////////
	// stylesheets can be defined two ways - either as an input array or
	// or the inliner will extract contents of <link> tags

	// apply any defined stylesheets
	if(!empty($css)) {
		foreach ($css as $cssitem) {
			$defined_styles[] = strtolower(file_get_contents($cssitem));
		}
		$all_styles = $defined_styles;
		$inlineHTML->applyStylesheet($defined_styles);
	}

	$linked_styles = $inlineHTML->extractStylesheets();
	$all_styles = array_merge($defined_styles, $linked_styles);

	// replace inline styles
	$inlineHTML->applyStylesheet($linked_styles);

	$all_styles_string = "";
	foreach($all_styles as $style) {
		$all_styles_string .= $style . PHP_EOL . PHP_EOL;
	}

	$result = $inlineHTML->getHTML();

	// COPY CONTENTS of stylesheets into an empty style tag if one is defined
	$result	= preg_replace('/\<style\>\w*\<\/style\>/', '<style>' . $all_styles_string . '</style>', $result);

	// strip !important from inline styles
	// e.g. style="color:white !important"
	// becomes style="color:white"
	// !important is irrelevant in inlined styles and confuses outlook
	$result = preg_replace('/style=[\'\"]([^\"]*)!important([^\"]*)[\'\"]/', 'style="$1$2"', $result);

	$html = str_get_html($result);

	///////////////////////////////////////////////////////////////////////
	// put any stored placeholder href attributes back in place
	foreach ($html->find('*[buzz-original-href]') as $elem) {
		$elem->href = $elem->getAttribute('buzz-original-href');
		$elem->removeAttribute('buzz-original-href');
	}

	// return result
	return $html->save();
}

function removeDomNodes($html, $xpathString) {
    $dom = new DOMDocument;
    $dom->loadHtml($html);
    $xpath = new DOMXPath($dom);
    while ($node = $xpath->query($xpathString)->item(0)) {
        $node->parentNode->removeChild($node);
    }
    return $dom->saveHTML();
}