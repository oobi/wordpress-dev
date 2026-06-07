<?php
namespace Buzz\Blocks;
/**
	$content -> the content of the block
	$attributes -> the attributes of the block
 */
?>

<?php
// see block.json for attribute names
extract($attributes);
$wrapperAttributes = get_block_wrapper_attributes();

// description
$description = empty($date_description) ? '' : '<div class="bz-date-description">' . $date_description . '</div>';

// link
$linkHtml = isset($link_url) ? "<a class='bz-date-link' href='$link_url'>$link_label</a>" : '';

echo <<<EOT
<div {$wrapperAttributes}>
	{$content}
	{$description}
	{$linkHtml}
</div>
EOT;
?>
