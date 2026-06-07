<?php

namespace Firefly\Setup;

/** ------------------------------------------------------
 *	 BLOCK STYLES
 *
 * 	 Create block styles https://developer.wordpress.org/reference/functions/register_block_style/
 *	------------------------------------------------------ */

class BlockStyles
{
	public function __construct()
	{
		register_block_style('core/button', [
				'name'         => 'button-link',
				'label'        => __( 'Link Style', 'textdomain' ),
				'is_default'   => false,
			]
		);
	}
}