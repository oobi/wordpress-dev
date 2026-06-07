<?php

/**
 * Render the required output
 * Attributes will be available in the $attributes collection
 */

// get the page/post URL
$url = urlencode(get_permalink());

// share links for various services:
$shareLinks = [
	'twitter' => 'https://twitter.com/intent/tweet?url=' . $url,
	'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . $url,
	'linkedin' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $url,
	'instagram' => 'https://www.instagram.com/create/post/?url=' . $url,
	'email' => 'mailto:?subject=Take+a+look+at+this!&body=' . $url
];

$activeServices = [];

// if the service is enabled, add it to the services array
foreach ($shareLinks as $service => $url) {
	if ($attributes[$service]) {
		$activeServices[] = $service;
	}
}

// render the output
if (count($activeServices) > 0) {

	// add extra attributes to the wrapper
	$extra = [];

	if (isset($attributes['textAlign'])) {
		$text_align = $attributes['textAlign'];
		$extra['class'] = "has-text-align-{$text_align}";
	}

	$iconStyle = sprintf("width: %spx; height: %spx; margin-left: %spx;",
		$attributes['iconSize'],
		$attributes['iconSize'],
		$attributes['iconSpacing']);

	$iconClass = 'bz-social-icon bz-social-icon--' . $service;

	$iconAttr = sprintf('style="%s" class="%s"', $iconStyle, $iconClass);
?>
	<div <?php echo get_block_wrapper_attributes($extra); ?>>
		<span class="bz-social-label">
			<?php echo $attributes['content']; ?>
		</span>
		<div class="bz-social-icons">
			<?php foreach ($activeServices as $service) { ?>
				<a class="bz-social-link" href="<?php echo $shareLinks[$service]; ?>" target="_blank" rel="noopener noreferrer">
					<?php
					$icon = file_get_contents(BUZZ_BLOCKS_DIR . 'images/' . $service . '.svg');
					echo str_replace('<svg', '<svg  '. $iconAttr . ' ', $icon);
					?>
				</a>
			<?php } ?>
		</div>
	</div>
<?php
}
