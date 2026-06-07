<?php
namespace Buzz\Blocks;
/**
	$content -> the content of the block
	$attributes -> the attributes of the block
 */
extract($attributes);
$dateSet = isset($dateSet) ? $dateSet : 'NOT SET';

$articleWrapperClassNames = array(
	$dateSet
);

$dates = \Buzz_Addon_Dates_Data::get_dates( [
	'set'			=> $dateSet,
	// 'title' 		=> $instance['title'],
	// 'template'		=> $template,
	// 'format'		=> $instance['format'] ?: '%e %B', // if not blank, default
	// 'show_dates' 	=> $instance['show_dates'],
	'merge_dates' 	=> true,
	// 'show_icons' 	=> $instance['show_icons'],
	// 'link_icon'		=> get_theme_mod( 'buzz_dates_link_icon', '' ),
	// 'class'			=> get_theme_mod( 'buzz_dates_class', '' ),
	// 'column_class'	=> get_theme_mod( 'buzz_dates_column_class', '' ),
]);

$dateWrapperAttributes = sprintf('class="bz-date-items %s"', classnames($articleWrapperClassNames));
$wrapperAttributes = get_block_wrapper_attributes();

$dateContent = '';

foreach( $dates as $dateItem ) {
	$date = date_create($dateItem['date']);
	$date = date_format($date, 'j M');
	$itemContent = '';

	foreach( $dateItem['items'] as $item) {
		$itemContent .= '<p>';
		$itemContent .= <<<EOT
			<div class="bz-date-description">
				{$item['description']}
			</div>
		EOT;

		if( $item['url'] && $item['title']) {
			$itemContent .= sprintf('<a class="bz-date-link" href="%s">%s</a>', $item['url'], $item['title']);
		}
		$itemContent .= '</p>';
	}

	$dateContent .= <<<EOT
		<div class="wp-block-buzz-date">
			<h4 class="bz-date-time">{$date}</h4>
			{$itemContent}
		</div>
	EOT;
}

echo <<<EOT
<div {$wrapperAttributes}>
	<div {$dateWrapperAttributes}>
		{$dateContent}
	</div>
</div>
EOT;
