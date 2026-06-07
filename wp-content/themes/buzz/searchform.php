<?php
/**
 * The Search form region.
 *
 * @package WordPress
 * @subpackage Firefly
 * @since Firefly Base 2.0
 */
?>

<form method="get" id="searchform" class="navbar-form navbar-right" role="search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<div class="form-group">
		<input type="text" class="form-control" name="s" id="s" placeholder="<?php esc_attr_e( 'Search...', 'firefly' ); ?>">
		<button type="submit" name="submit" id="searchsubmit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span></button>
	</div>
</form>