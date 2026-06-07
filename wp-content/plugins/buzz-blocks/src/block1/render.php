<?php
namespace Buzz\Blocks;
/*
	$content -> the content of the block
	$attributes -> the attributes of the block
*/
?>

<p <?php echo get_block_wrapper_attributes(); ?>>
	<?php esc_html_e( 'Block1 – hello from a dynamic block!', 'block1' ); ?>
</p>
