<p>Select which Version 1 newsletters you want to migrate. The following operations will be performed for each selected newsletter:</p>
<ol>
	<li>Each selected newsletter <strong>taxonomy</strong> will be converted to a newsletter <strong>post</strong> with the same title.</li>
	<li>Articles associated with the old taxonomy will be have this new post assigned as a parent.</li>
	<li>Publish date for the new post will be set (if possible) to the taxonomy "publish_date" meta value</li>
	<li>Featured image will be set (if possible) to the taxonomy "thumbnail" meta value</li>
	<li><em>If a post already exists with a matching SLUG it will be ignored</em></li>
</ol>

<form method="post" id="migration-tool-form">
	<input type="hidden" name="action" value="convert_newsletters">

	<button class="button select-all">all</button>
	<button class="button select-none">none</button>

	<?php
		$newsletters = get_categories(array(
			'orderby'	=> 'name',
			'order'		=> 'desc',
			'taxonomy'	=> 'newsletter',
		));
	?>

	<div id="newsletter-select">
		<div class="t-header">
			<table>
				<thead>
					<td class="c1">&nbsp;</td>
					<td class="c2">Title</td>
					<td class="c3">Publish Date</td>
					<td class="c4">Articles</td>
				</thead>
			</table>
		</div>

		<div class="t-body">
			<table>
				<tbody>

					<?php
						foreach ($newsletters as $newsletter) {
							$title  		= $newsletter->name;
							$id     		= $newsletter->term_id;
							$count  		= $newsletter->count;
							$publish_date	= Buzz_V1_Migration_Options_Page::get_tax_meta($id, 'publish_date');

							printf('<tr>');
							printf('<td class="c1"><input type="checkbox" name="newsletter[]" value="%d"></td>', $id);
							printf('<td class="c2">%s</td>', $title);
							printf('<td class="c3">%s</td>', $publish_date);
							printf('<td class="c4">%d</td>', $count);
							printf('</tr>');
						}

					?>

				</tbody>
			</table>
		</div>
	</div>

	<?php submit_button('convert selected'); ?>

</form>

<style>
	#newsletter-select 				{ width:50%; min-width:320px; }
	#migration-tool-form .c1 		{ width:30px; }
	#migration-tool-form .c2 		{ width:150px; }
	#migration-tool-form .c3 		{ }
	#migration-tool-form .c4 		{ width:100px;  }
	#migration-tool-form table 		{ width:100%}
	#migration-tool-form td 		{ padding:0 5px; }
	#migration-tool-form .t-body	{ border:1px solid #CCC; height:300px; overflow-y:scroll; }
	#migration-tool-form .t-header 	{ margin-top:20px; font-weight:bold; padding-right:20px; }

</style>

<script>
	(function($){

		$frm = $("#migration-tool-form");
		$(".select-all", $frm).click(function(e){
			e.preventDefault();
			$("[type='checkbox']", $frm).prop('checked',true);
		});
		$(".select-none", $frm).click(function(e){
			e.preventDefault();
			$("[type='checkbox']", $frm).prop('checked',false);
		});

	})(jQuery);
</script>
