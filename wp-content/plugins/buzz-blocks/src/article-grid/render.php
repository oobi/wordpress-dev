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
extract($attributes);
$mediaPosition = $attributes['mediaPosition'] ?? '';

// set the wrapper class names
$wrapperClassNames = array(
	'bz-grid-' . $uniqueId => true,
	'has-media-on-left' => ($mediaPosition === 'left'),
	'has-media-on-right' => ($mediaPosition === 'right'),
	'is-collapsible' => $collapsible,
	'is-featured' => $attributes['includeFeatured']
);

if (isset($columns)) {
	$wrapperClassNames["has-cols-$columns"] = true;
}

if (isset($verticalAlignment)) {
	$wrapperClassNames["is-vertically-aligned-$verticalAlignment"] = true;
}

$articleArgs = [
	'post_type' => 'article',
	'post_status' => 'publish',
	'posts_per_page' => -1
];

// Category filter
if ($categoryId && $categoryId != $FEATURED_CATEGORY_PLACEHOLDER_ID) {
	$articleArgs['tax_query'][] = [
		'taxonomy' => 'article_category',
		'field' => 'id',
		'terms' => $categoryId,
	];

	// get the category name by its ID
	$category = get_term($categoryId, 'article_category');
	$categoryName = $category->name;
}

// Featured filter - metadata query
if ($categoryId == $FEATURED_CATEGORY_PLACEHOLDER_ID) {
	$articleArgs['meta_query'][] = [
		'key' => 'ff_featured_article',
		'compare' => 'EXISTS',
	];

	$categoryName = "Featured Articles";
}
// filter featured articles if not included
else {
	if (!$includeFeatured) {
		$articleArgs['meta_query'][] = [
			'key' => 'ff_featured_article',
			'compare' => 'NOT EXISTS',
		];
	}
}

if ($newsletterId) {
	// if the newsletter id is set to 'current post' then set the correct ID
	if ($newsletterId == $CURRENT_POST_PLACEHOLDER_ID) {
		$newsletterId = get_the_ID();
	}

	$articleArgs['meta_query'][] = [
		[
			'key' => 'ff_parent_id',
			'value' => $newsletterId,
		]
	];
}

$articles = get_posts($articleArgs);

// Output the block
if (count($articles)) {
	// Title
	$titleHtml = $displayTitle ? <<<EOT
		<input type="checkbox" checked class="bz-grid-checkbox" id="bz-grid-checkbox-{$uniqueId}">
		<label class="bz-grid-title" for="bz-grid-checkbox-{$uniqueId}">
			$categoryName
		</label>
	EOT : '';


	// Articles
	$articlesHtml = '';
	foreach ($articles as $article) {
		$thumbnail_url = get_the_post_thumbnail_url($article->ID, $thumbnailSize);
		$excerpt = excerpt($article, $excerptLength);
		$link = get_permalink($article->ID);

		$terms = get_the_terms($article->ID, 'article_tag');

		$tags = false;

		if ($terms != false) {
			$tags = $terms;
		}

		// thumbnail
		$thumbnailHtml = $thumbnail_url && $displayThumbnail ? <<<EOT
			<a href="{$link}" class="bz-grid-article-thumbnail">
				<img class="bz-grid-article-thumbnail-image" src="{$thumbnail_url}" alt="{$article->post_title}">
			</a>
		EOT : '';

		// excerpt
		$excerptHtml = $displayExcerpt ? <<<EOT
			<p class="bz-grid-article-excerpt">{$excerpt}</p>
		EOT : '';

		// tags
		$tagsHtml = '';

		if ($tags && $displayTags) {
			foreach ($tags as $tag) {
				$termLink = get_term_link($tag);
				$tagsHtml .= <<<EOT
					<a class="bz-grid-article-tag" href="{$termLink}">{$tag->name}</a>
				EOT;
			}
		}

		$thumbnailClass = $thumbnail_url ? '' : 'no-featured-image';

		// append article to output
		$articlesHtml .= <<<EOT
			<div class="bz-grid-article bz-grid-article-{$article->ID} {$thumbnailClass}">
				{$thumbnailHtml}
				<div class="bz-grid-article-content">
					<div class="bz-grid-article-tags">
						{$tagsHtml}
					</div>
					<h3 class="bz-grid-article-title">
					<a href="{$link}">{$article->post_title}</a>
					</h3>
					{$excerptHtml}
				</div>
			</div>
		EOT;
	}

	// set wrapper attributes
	$wrapperAttributes = get_block_wrapper_attributes([
		"class" => classnames($wrapperClassNames)
	]);

	// set custom styles
	$customStyles = <<<EOT
		.bz-grid-{$uniqueId}.has-media-on-left .bz-grid-article-thumbnail,
		.bz-grid-{$uniqueId}.has-media-on-right .bz-grid-article-thumbnail {
			@media all and (min-width: 768px) {
				width:{$mediaWidth}%;
			}
		}
	EOT;

	// Final render
	echo <<<EOT
		<div {$wrapperAttributes}>
			{$titleHtml}
			<div class="bz-grid-articles">
				{$articlesHtml}
			</div>

			<style>
				{$customStyles}
			</style>
		</div>
	EOT;
}
?>
