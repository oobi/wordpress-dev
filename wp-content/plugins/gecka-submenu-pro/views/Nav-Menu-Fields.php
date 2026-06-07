                <br style="clear: both" />

                <p class="edit-menu-item-autopopulate">
                	<label for="edit-menu-item-autopopulate-<?php echo $item_id; ?>">
                		<input type="checkbox" id="edit-menu-item-autopopulate-<?php echo $item_id; ?>" class="edit-menu-item-autopopulate" name="menu-item-autopopulate[<?php echo $item_id; ?>]" value="1" <?php echo checked($item->autopopulate, '1'); ?> onclick="gk_toggle_autopopulate_options(<?php echo $item_id; ?>, this.checked);" />
                		<?php _e('Automaticaly populate child items ', Gecka_Submenu::Domain); ?>
                	</label>
                </p>

                <div class="edit-menu-item-autopopulate-options edit-menu-item-autopopulate-options-<?php echo $item_id; ?>" style="<?php echo $item->autopopulate == '1' ? '' : 'display: none'; ?>">


                	<p class="description">
                		<?php if ($item->type === 'post_type') : ?>
                			<label for="edit-menu-item-autopopulate_type-<?php echo $item_id; ?>-subpages">
                				<input type="radio" id="edit-menu-item-autopopulate_type-<?php echo $item_id; ?>-subpages" class="edit-menu-item-autopopulate_type" name="menu-item-autopopulate_type[<?php echo $item_id; ?>]" value="subpages" <?php echo checked($item->autopopulate_type, 'subpages'); ?> onclick="gk_toggle_autopopulate_posttype_options(<?php echo $item_id; ?>);" />
                				<?php _e('Automatically populate with child posts', Gecka_Submenu::Domain); ?>
                			</label>
                	</p>

                	<p class="edit-menu-item-autopopulate-child-options-<?php echo $item_id; ?> link-to-original gsm-autopopulate-options gsm-autopopulate-child" style="<?php echo $item->autopopulate_type == 'subpages' ? '' : 'display: none'; ?>">

                		<span class="description description-thin">
                			<label for="edit-menu-item-autopopulate_depth-<?php echo $item_id; ?>">
                				<?php _e('Depth', Gecka_Submenu::Domain); ?>:
                			</label>
                			<input type="text" id="edit-menu-item-autopopulate_depth-<?php echo $item_id; ?>" class="widefat edit-menu-item-autopopulate_depth" name="menu-item-autopopulate_depth[<?php echo $item_id; ?>]" value="<?php echo intval($item->autopopulate_depth) ?>" />
                		</span>
                		<br style="clear: both">

                	</p>

                	<p class="description">
                	<?php endif; ?>
                	<label for="edit-menu-item-autopopulate_type-<?php echo $item_id; ?>-posttype">
                		<input type="radio" id="edit-menu-item-autopopulate_type-<?php echo $item_id; ?>-posttype" class="edit-menu-item-autopopulate_type" name="menu-item-autopopulate_type[<?php echo $item_id; ?>]" value="posttype" <?php echo checked($item->autopopulate_type, 'posttype'); ?> onclick="gk_toggle_autopopulate_posttype_options(<?php echo $item_id; ?>);" />
                		<?php _e('Automatically populate with a custom post type', Gecka_Submenu::Domain); ?>
                	</label>
                	</p>




                	<p class="edit-menu-item-autopopulate-posttype-options-<?php echo $item_id; ?> link-to-original gsm-autopopulate-options gsm-autopopulate-posttype" style="<?php echo $item->autopopulate_type == 'posttype' ? '' : 'display: none'; ?>">

                		<strong class="gsm-title"><?php _e('Parameters', Gecka_Submenu::Domain) ?></strong><br />

                		<label for="edit-menu-item-autopopulate_posttype-<?php echo $item_id; ?>">
                			<?php _e('Post type', Gecka_Submenu::Domain); ?>:
                		</label>

                		<select id="edit-menu-item-autopopulate_posttype-<?php echo $item_id; ?>" class="widefat edit-menu-item-autopopulate_posttype" name="menu-item-autopopulate_posttype[<?php echo $item_id; ?>]" onchange="gk_autopopulate_posttype_taxonomies(<?php echo $item_id; ?>, this)">
                			<?php
							foreach (get_post_types(array(), 'objects') as $post_type) {

								if (
									$post_type->name == 'revision'
									|| $post_type->name == 'nav_menu_item'
								) continue;

								$selected = selected($post_type->name, $item->autopopulate_posttype);
								echo '<option value="' . $post_type->name . '"' . $selected . '>' . $post_type->labels->name . '</option>';
							} ?>
                		</select>

                		<br style="clear: both" />

                		<label for="edit-menu-item-autopopulate_posttype_type-<?php echo $item_id; ?>-posts">
                			<input type="radio" id="edit-menu-item-autopopulate_posttype_type-<?php echo $item_id; ?>-posts" class="edit-menu-item-autopopulate_posttype_type-posts" name="menu-item-autopopulate_posttype_type[<?php echo $item_id; ?>]" value="posts" <?php if ($item->autopopulate_posttype_type == 'posts') echo 'checked="checked"'; ?> onclick="gk_toggle_autopopulate_posttax_options(<?php echo $item_id; ?>, this);" />
                			<?php _e('Populate using posts', Gecka_Submenu::Domain); ?>
                		</label>

                		<span class="gsm_posts_options gsm_posts_options-<?php echo $item_id; ?>" <?php if ($item->autopopulate_posttype_type == 'posts') echo 'style="display:block"';
																									else echo 'style="display:none"'; ?>>

                			<span class="description description-thin">
                				<label for="edit-menu-item-autopopulate_posts_orderby-<?php echo $item_id; ?>">
                					<?php _e('Sort by', Gecka_Submenu::Domain); ?>:
                				</label>

                				<select id="edit-menu-item-autopopulate_posts_orderby-<?php echo $item_id; ?>" class="widefat edit-menu-item-autopopulate_posts_orderby" name="menu-item-autopopulate_posts_orderby[<?php echo $item_id; ?>]">
                					<option value="date" <?php selected($item->autopopulate_posts_orderby, 'date'); ?>><?php _e('Date created', Gecka_Submenu::Domain); ?></option>
                					<option value="modified" <?php selected($item->autopopulate_posts_orderby, 'modified'); ?>><?php _e('Date modifed', Gecka_Submenu::Domain); ?></option>
                					<option value="title" <?php selected($item->autopopulate_posts_orderby, 'title'); ?>><?php _e('Title'); ?></option>
                					<option value="menu_order" <?php selected($item->autopopulate_posts_orderby, 'menu_order'); ?>><?php _e('Menu order'); ?></option>
                				</select>
                			</span>

                			<span class="description description-thin">
                				<label for="edit-menu-item-autopopulate_posts_order-<?php echo $item_id; ?>">
                					<?php _e('Sort order', Gecka_Submenu::Domain); ?>:
                				</label>

                				<select id="edit-menu-item-autopopulate_posts_order-<?php echo $item_id; ?>" class="widefat edit-menu-item-autopopulate_posts_order" name="menu-item-autopopulate_posts_order[<?php echo $item_id; ?>]">
                					<option value="ASC" <?php selected($item->autopopulate_posts_order, 'ASC'); ?>><?php _e('Ascendent', Gecka_Submenu::Domain); ?></option>
                					<option value="DESC" <?php selected($item->autopopulate_posts_order, 'DESC'); ?>><?php _e('Descendent', Gecka_Submenu::Domain); ?></option>
                				</select>
                			</span>

                			<span class="description description-thin">
                				<label for="edit-menu-item-autopopulate_posts_limit-<?php echo $item_id; ?>">
                					<?php _e('Number of posts', Gecka_Submenu::Domain); ?>:
                				</label>

                				<input type="text" id="edit-menu-item-autopopulate_posts_limit-<?php echo $item_id; ?>" class="widefat edit-menu-item-autopopulate_posts_limit" name="menu-item-autopopulate_posts_limit[<?php echo $item_id; ?>]" value="<?php echo (int)$item->autopopulate_posts_limit;  ?>" />

                			</span>
                			<span class="description description-thin">
                				<label for="edit-menu-item-autopopulate_posts_depth-<?php echo $item_id; ?>">
                					<?php _e('Depth', Gecka_Submenu::Domain); ?>:
                				</label>

                				<input type="text" id="edit-menu-item-autopopulate_posts_depth-<?php echo $item_id; ?>" class="widefat edit-menu-item-autopopulate_posts_depth" name="menu-item-autopopulate_posts_depth[<?php echo $item_id; ?>]" value="<?php echo intval($item->autopopulate_posts_depth)  ?>" />
                			</span>
                			<br style="clear: both" />
                			<span class="description description-thin">
                				<label for="edit-menu-item-autopopulate_posts_taxonomy-<?php echo $item_id; ?>">
                					<?php _e('Specific taxonomy', Gecka_Submenu::Domain); ?>:
                				</label>

                				<?php
								echo $this->post_type_taxonomies_select($item->autopopulate_posttype, 'menu-item-autopopulate_posts_taxonomy[' . $item_id . ']', 'edit-menu-item-autopopulate_posts_taxonomy-' . $item_id, $item->autopopulate_posts_taxonomy, 'widefat edit-menu-item-autopopulate_posts_taxonomy', true, "gk_autopopulate_post_taxonomies($item_id, this)");
								?>

                			</span>
                			<span class="description description-thin">
                				<label for="edit-menu-item-autopopulate_posts_taxonomy_tax-<?php echo $item_id; ?>">
                					&nbsp;</label>

                				<?php
								echo $this->post_type_taxonomies_tax_select($item->autopopulate_posts_taxonomy, 'menu-item-autopopulate_posts_taxonomy_tax[' . $item_id . ']', 'edit-menu-item-autopopulate_posts_taxonomy_tax-' . $item_id, $item->autopopulate_posts_taxonomy_tax, 'widefat edit-menu-item-autopopulate_posts_taxonomy_tax');
								?>

                			</span>
                		</span>
                		<br style="clear: both" />
                		<br />


                		<label for="edit-menu-item-autopopulate_posttype_type-<?php echo $item_id; ?>-taxonomies">
                			<input type="radio" id="edit-menu-item-autopopulate_posttype_type-<?php echo $item_id; ?>-taxonomies" class="edit-menu-item-autopopulate_posttype_type-taxonomies" name="menu-item-autopopulate_posttype_type[<?php echo $item_id; ?>]" value="taxonomies" <?php if ($item->autopopulate_posttype_type == 'taxonomies') echo 'checked="checked"'; ?> <?php if (!$this->post_type_taxonomies($item->autopopulate_posttype)) echo 'disabled="disabled"'; ?> onclick="gk_toggle_autopopulate_posttax_options(<?php echo $item_id; ?>, this);" />
                			<?php _e('Populate using a taxonomy', Gecka_Submenu::Domain); ?>
                		</label>

                		<span class="gsm_taxonomies_options gsm_taxonomies_options-<?php echo $item_id; ?>" <?php if ($item->autopopulate_posttype_type == 'taxonomies') echo 'style="display:block"';
																											else echo 'style="display:none"'; ?>>

                			<?php

							echo $this->post_type_taxonomies_select($item->autopopulate_posttype, 'menu-item-autopopulate_taxonomy[' . $item_id . ']', 'edit-menu-item-autopopulate_taxonomy-' . $item_id, $item->autopopulate_taxonomy, 'widefat edit-menu-item-autopopulate_taxonomy', false, "gk_toggle_autopopulate_tax_child_of_options($item_id, this)");
							?>
                			<br />
                			<label for="edit-menu-item-autopopulate_taxonomies_child_of-<?php echo $item_id; ?>">
                				<?php _e('Start from', Gecka_Submenu::Domain); ?>:
                			</label>
                			<?php
							echo $this->post_type_taxonomies_tax_select($item->autopopulate_taxonomy, 'menu-item-autopopulate_taxonomy_child_of[' . $item_id . ']', 'edit-menu-item-autopopulate_taxonomy_child_of-' . $item_id, $item->autopopulate_taxonomy_child_of, 'widefat edit-menu-item-autopopulate_taxonomy_child_of', '', __('Root', Gecka_Submenu::Domain));
							?>
                			<br />
                			<span class="description description-thin">
                				<label for="edit-menu-item-autopopulate_taxonomies_orderby-<?php echo $item_id; ?>">
                					<?php _e('Sort by', Gecka_Submenu::Domain); ?>:
                				</label>

                				<select id="edit-menu-item-autopopulate_taxonomies_orderby-<?php echo $item_id; ?>" class="widefat edit-menu-item-autopopulate_taxonomies_orderby" name="menu-item-autopopulate_taxonomies_orderby[<?php echo $item_id; ?>]">
                					<option value="none" <?php selected($item->autopopulate_taxonomies_orderby, 'none'); ?>><?php _e('None'); ?></option>
                					<option value="name" <?php selected($item->autopopulate_taxonomies_orderby, 'name'); ?>><?php _e('Name'); ?></option>
                					<option value="count" <?php selected($item->autopopulate_taxonomies_orderby, 'count'); ?>><?php _e('Count', Gecka_Submenu::Domain); ?></option>
                				</select>
                			</span>

                			<span class="description description-thin">
                				<label for="edit-menu-item-autopopulate_taxonomies_order-<?php echo $item_id; ?>">
                					<?php _e('Sort order', Gecka_Submenu::Domain); ?>:
                				</label>

                				<select id="edit-menu-item-autopopulate_taxonomies_order-<?php echo $item_id; ?>" class="widefat edit-menu-item-autopopulate_taxonomies_order" name="menu-item-autopopulate_taxonomies_order[<?php echo $item_id; ?>]">
                					<option value="ASC" <?php selected($item->autopopulate_taxonomies_order, 'ASC'); ?>><?php _e('Ascendent', Gecka_Submenu::Domain); ?></option>
                					<option value="DESC" <?php selected($item->autopopulate_taxonomies_order, 'DESC'); ?>><?php _e('Descendent', Gecka_Submenu::Domain); ?></option>
                				</select>
                			</span>

                			<span class="description description-thin">
                				<label for="edit-menu-item-autopopulate_taxonomies_limit-<?php echo $item_id; ?>">
                					<?php _e('Number of terms', Gecka_Submenu::Domain); ?>:
                				</label>

                				<input type="text" id="edit-menu-item-autopopulate_taxonomies_limit-<?php echo $item_id; ?>" class="widefat edit-menu-item-autopopulate_taxonomies_limit" name="menu-item-autopopulate_taxonomies_limit[<?php echo $item_id; ?>]" value="<?php echo (int)$item->autopopulate_taxonomies_limit;  ?>" />

                			</span>

                			<span class="description description-thin">
                				<label for="edit-menu-item-autopopulate_taxonomies_depth-<?php echo $item_id; ?>">
                					<?php _e('Depth', Gecka_Submenu::Domain); ?>:
                				</label>

                				<input type="text" id="edit-menu-item-autopopulate_taxonomies_depth-<?php echo $item_id; ?>" class="widefat edit-menu-item-autopopulate_taxonomies_depth" name="menu-item-autopopulate_taxonomies_depth[<?php echo $item_id; ?>]" value="<?php echo intval($item->autopopulate_taxonomies_depth) ?>" />
                			</span>

                			<span class="description description-thin">
                				<br /><label for="edit-menu-item-autopopulate_taxonomies_hideempty-<?php echo $item_id; ?>">
                					<input type="checkbox" id="edit-menu-item-autopopulate_taxonomies_hideempty-<?php echo $item_id; ?>" class="widefat edit-menu-item-autopopulate_taxonomies_hideempty" name="menu-item-autopopulate_taxonomies_hideempty[<?php echo $item_id; ?>]" value="1" <?php checked('1', $item->autopopulate_taxonomies_hideempty);  ?> />

                					<?php _e('Hide empty', Gecka_Submenu::Domain); ?>
                				</label>


                			</span>
                		</span>
                		<br style="clear: both" />

                	</p>

                </div>