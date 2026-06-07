<?php

namespace Buzz\Blocks;

require_once(BUZZ_BLOCKS_DIR . '/inc/utils.php');

/**
	$content -> the content of the block
	$attributes -> the attributes of the block
 */

// see block.json for attribute names
extract($attributes);

if (is_singular('article')) {

	// retrieve adjacent articles
	$adjacent = get_adjacent_articles();

	$wrapperClassNames = array(
		'bz-article-nav-' . $uniqueId => true
	);

	if( isset($verticalAlignment)) {
		$wrapperClassNames["is-vertically-aligned-$verticalAlignment"] = true;
	}


	// set wrapper attributes
	$wrapperAttributes = get_block_wrapper_attributes([
		"class" => classnames($wrapperClassNames)
	]);


	// generate link HTML
	$itemHtml = '';

	foreach (['previous', 'next'] as $key) {
		$article = $adjacent[$key] ?? false;

		// set the item attributes if the article exists
		if ($article) {
			$title = $article->post_title;
			$link = get_permalink($article);
			$thumbnail = get_the_post_thumbnail_url($article, $thumbnailSize);
			$label = $key === 'next' ? 'Next' : 'Previous';

			$thumbmailHtml = $showThumbnail && $thumbnail ? <<<EOT
				<img class="bz-article-nav-item-thumbnail" src="{$thumbnail}" alt="{$title}">
			EOT : '';

			$labelHtml = $showLabel ? <<<EOT
				<div class="bz-article-nav-item-label">{$label}</div>
			EOT : '';

			$itemHtml .= <<<EOT
				<a class="bz-article-nav-item bz-article-nav-item-{$key}" href="{$link}">
					{$thumbmailHtml}
					<div class="bz-article-nav-item-text">
						{$labelHtml}
						<div class="bz-article-nav-item-title">{$title}</div>
					</div>
				</a>
			EOT;
		}
		// empty slot if no article
		else {
			$itemHtml = "<div class=\"bz-article-nav-item bz-article-nav-item-{$key}\"></div>";
		}
	}

	// Output block HTML
	echo <<<EOT
		<div {$wrapperAttributes}>
				{$itemHtml}
		</div>
		<style>
			.bz-article-nav-{$uniqueId} .bz-article-nav-item-thumbnail {
				width: {$thumbnailWidth}px;
				object-fit: cover;
				aspect-ratio: {$thumbnailAspectRatio};
			}
		</style>
	EOT;
}
