<?php
namespace Buzz\Blocks;
/**
	$content -> the content of the block
	$attributes -> the attributes of the block
 */
extract($attributes);
$numColumns = isset($numColumns) ? $numColumns : 3;

$articleWrapperClassNames = array(
	// "bz-date-items-cols-" . $numColumns => $numColumns
);

$dateWrapperAttributes = sprintf('class="bz-date-items %s"', classnames($articleWrapperClassNames));
$wrapperAttributes = get_block_wrapper_attributes();


echo <<<EOT
<div {$wrapperAttributes}>
	<div {$dateWrapperAttributes}>
		{$content}
	</div>
</div>
EOT;
