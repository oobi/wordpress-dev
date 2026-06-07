<?php

namespace FF\LogicalDoc;


/**
 * The class to add metabox to the post edit screen
 *
 * @package    ff-logicaldoc
 * @subpackage ff-logicaldoc/admin
 * @author     Firefly Interactive
 */
class Tag_Sync {

	/**
	 * Constructor - set some values
	 */
	public function __construct() {

	}

	/**
	 * Add management page
	 */
	public function add_management_page() {
		add_management_page( 'Sync LogicalDOC Tags', 'Logical Tag Sync', 'manage_options', 'logical-tag-sync', array( $this, 'sync_page_output') );
	}


	/**
	 * Output content for sync page
	 */
	public function sync_page_output() {

		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		echo '<div class="wrap">';
		echo '<h2>Sync LogicalDOC Tags</h2>';
		echo '<p>The sync tool will look at the current list of tags available in LogicalDOC and create any missing terms in WordPress.</p>';
		echo '<p>You can optionally delete any terms <strong>extra</strong> terms which are present in WordPress but not in Logical</p>';

		?>

		<form name="form1" method="post" action="">
			<input type="hidden" name="ff-check-sync" value="1">

			<label for="ff-delete-extra">
				<input type="checkbox" value="1" name="ff-delete-extra" id="ff-delete-extra">
				Delete local terms which are not present in LogicalDOC
			</label>

			<p class="submit">
				<input type="submit" name="Sync" class="button-primary" value="<?php esc_attr_e('Sync') ?>" />
			</p>

		</form>

		<?php

		if( isset($_REQUEST['ff-check-sync']) && $_REQUEST['ff-check-sync'] ) {
			$delete = isset($_REQUEST['ff-delete-extra']) && $_REQUEST['ff-delete-extra'];
			$result = $this->sync_terms($delete);
			echo "<div class=\"notice notice-success\"><p>Sync Complete. {$result}</p></div>";
		}
		$this->show_matching_terms();
		echo '</div>';
	}

	/**
	 * Sync terms from remote to local
	 * Optionally delete local items which are not present in remote store
	 */
	protected function sync_terms($delete) {
		$matches = $this->get_matching_terms();
		$tax = 'logical-tag';

		$inserted = 0;
		$deleted = 0;

		foreach( $matches as $item ) {
			$local = $item['local'];
			$remote = $item['remote'];
			$term = empty($local) ? false : get_term_by('name', $local, $tax);

			// if terms don't match, do something about it
			if( $local != $remote ) {
				// if delete flag is set and the remote item is empty then delete the local term
				if( $term && $delete && empty($remote) ) {
					wp_delete_term($term->term_id, $tax);
					$deleted++;
				}

				// create missing term
				else if( !empty( $remote ) ) {
					wp_insert_term( $remote, $tax);
					$inserted++;
				}
			}
		}

		return "Created: {$inserted}, Deleted: {$deleted}";
	}

	/**
	 * Return an array of matches
	 */
	protected function get_matching_terms() {
		$local 	= $this->get_local_terms();
		$remote = $this->get_remote_terms();

		$matches = [];

		// find matching terms
		$terms = array_intersect($local, $remote);
		foreach( $terms as $term ) {
			$matches[] = array('local' => $term, 'remote' => $term);
		}

		// find differences
		$terms  = array_diff($local, $remote);
		foreach( $terms as $term ) {
			$matches[] = array('local' => $term, 'remote' => null);
		}

		$terms  = array_diff($remote, $local);
		foreach( $terms as $term ) {
			$matches[] = array('local' => null, 'remote' => $term);
		}

		return $matches;
	}

	/**
	 * Generate a table showing any differences between local and remote tags
	 */
	protected function show_matching_terms() {
		$matches = $this->get_matching_terms();
		?>

		<table class="widefat">
			<thead>
				<tr>
					<th  width="50%" class="row-title">Local Terms</th>
					<th>LogicalDOC Terms</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach( $matches as $item ) :
					$local = $item['local'];
					$remote = $item['remote'];
					$error = '<span style="color:red" class="dashicons dashicons-no"></span>';
					?>
					<tr>
						<td>
							<?php echo empty($local) ? $error : $local; ?>
						</td>
						<td>
							<?php echo empty($remote) ? $error : $remote; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<?php
	}

	/**
	 * Retrieve an array of local taxonomy terms
	 */
	protected function get_local_terms() {
		$terms = get_terms(array(
			'taxonomy' => 'logical-tag',
			'hide_empty' => false
		));

		$tags = [];

		if( $terms && !is_wp_error( $terms )) {
			foreach( $terms as $tag ) {
				$tags[] = $tag->name;
			}
		}

		return $tags;
	}


	/**
	 * Retrieve an array of remote taxonomy terms
	 */
	protected function get_remote_terms() {
		$logical = new Logical();
		$sid = $logical->login();
		$result = $logical->getAllTags();
		$logical->logout($sid);

		$tags = [];

		if( $result && isset( $result->tag ) ) {
			foreach( $result->tag as $tag ) {
				// sanitize_term_field ensures that term name is in same format as what's stored locally for comparison
				// for example & characters are transformed to &amp;
				$tags[] = sanitize_term_field( 'name', $tag, 0, 'logical-tag', 'db');
			}
		}

		return $tags;
	}
}
