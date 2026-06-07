<?php

namespace Buzz\Blocks;

require_once(BUZZ_BLOCKS_DIR . '/inc/utils.php');

/**
	$content -> the content of the block
	$attributes -> the attributes of the block
 */

// see block.json for attribute names
extract($attributes);

// article grouping
if( function_exists( 'get_articles_from_newsletter' ) ) {
	$groups = get_articles_from_newsletter($showCategoryHeadings);
} else {
	$groups = [];
}

// if post type article

// block wrapper attributes
$wrapperAttributes = get_block_wrapper_attributes();

// render each article group
$articleGroupHtml = '';

foreach ($groups as $group) {
	if ($group['name']) {
		$articleGroupHtml .= '<div class="bz-article-sidebar-category">' . $group['name'] . '</div>';
	}

	$articleGroupHtml .= '<ul class="bz-article-sidebar-list">';

	foreach ($group['posts'] as $article) {
		$status = $article->post_status != 'publish' ? ' <small class="post-status">(' . $article->post_status . ')</small>' : '';
		$link = get_permalink($article->ID);
		$title = $article->post_title;

		$articleGroupHtml .= <<<EOT
			<li class="bz-article-sidebar-list-item">
				<a href="{$link}">{$title}{$status}</a>
			</li>
		EOT;
	}

	$articleGroupHtml .= '</ul>';
}


echo <<<EOT
<div {$wrapperAttributes}>
	{$content}
	{$articleGroupHtml}
</div>
EOT;
