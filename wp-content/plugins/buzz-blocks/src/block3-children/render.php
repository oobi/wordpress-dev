<?php
namespace Buzz\Blocks;
/**
	$content -> the content of the block
	$attributes -> the attributes of the block

	<?php // echo json_encode($attributes); ?>
*/
?>

<div <?php echo get_block_wrapper_attributes(); ?>>
	<?php echo $content; ?>
</div>
