<?php

/**
 * Load a customization script.
 *
 * First try in the child theme, then fall back to Firefly theme
 * path if file cannot be found. If extendsParent is set then load
 * both.
 */
function ff_load_custom_include( $path, $extendsParent, $once=true ) {
	$localFile  = get_stylesheet_directory() . $path;
	$parentFile = get_template_directory()   . $path;
	$isChild    = is_readable($localFile);
	$isParent   = is_readable($parentFile) && (!$isChild || $extendsParent);
	$isError	= !$isChild && !$isParent;
	if ($isError) {
		trigger_error("Unable to load custom include from " . $localFile . ' or ' . $parentFile, E_USER_ERROR);
	} else if($once){
		if ($isChild)  require_once($localFile);
		if ($isParent) require_once($parentFile);
	} else {
		if ($isChild)  include($localFile);
		if ($isParent) include($parentFile);
	}
}