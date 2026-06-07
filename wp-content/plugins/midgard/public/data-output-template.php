<?php

use FF\Midgard\Midgard_Common;

// check if in preview mode
$preview = array_key_exists('preview', $_GET) && $_GET['preview'];


if($preview) {
	echo '<pre>';
	the_content();
	echo '</pre>';
} else {
	header('Content-Type:application/json');
	the_content();
}