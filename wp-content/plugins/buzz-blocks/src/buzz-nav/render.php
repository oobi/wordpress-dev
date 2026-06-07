<?php

namespace Buzz\Blocks;
require_once(BUZZ_BLOCKS_DIR . '/inc/utils.php');

/**
	$content -> the content of the block
	$attributes -> the attributes of the block
 */

// echo json_encode($attributes);
?>

<?php
// see block.json for attribute names

$wrapperClassNames = array(
	// 'bz-article-nav-' . $uniqueId => true
);

// set wrapper attributes
$wrapperAttributes = get_block_wrapper_attributes([
	"class" => classnames($wrapperClassNames)
]);

$archiveURL = get_post_type_archive_link('newsletter');
$archiveHTML = $attributes['displayArchive'] ? <<<EOT
	<li class="bz-nav-item">
		<a href="$archiveURL" class="bz-nav-link">Archive</a>
	</li>
EOT : '';

$nid = get_post_type(get_post()) == 'newsletter' ? get_the_ID() : false;

if (is_singular('article')) {
	$post = get_post();
	$nid = get_post_meta($post->ID, 'ff_parent_id', true);
}

$printURL = function_exists('ff_get_print_url') ? ff_get_print_url($nid) : '';
$printHTML = $attributes['displayPrint'] ? <<<EOT
	<li class="bz-nav-item">
		<a href="$printURL" target="_blank" class="bz-nav-link">Print</a>
	</li>
EOT : '';
// issue HTML
$navHTML = '';

// generate HTML
$itemHtml = <<<EOT
	<ul class="bz-nav-list">
		{$archiveHTML}		
		{$printHTML}		
	</ul>
EOT;

// Output block HTML
echo <<<EOT
<div {$wrapperAttributes}>
	{$itemHtml}
</div>
<style>
	
</style>
EOT;