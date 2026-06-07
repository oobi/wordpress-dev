<?php

class Gecka_Submenu_NavMenuHacks
{

    function __construct()
    {

        if (is_admin()) {

            /* use custom walker */
            add_filter('wp_edit_nav_menu_walker', array($this, 'custom_walker'));

            /* add custom field */
            add_action('wp_nav_menu_item_custom_fields', array($this, 'wp_nav_menu_item_custom_fields'), 10, 4);

            /* save custom field */
            add_action('wp_update_nav_menu_item', array($this, 'wp_update_nav_menu_item'), 10, 3);

            /* load custom javascript and css */

            // register them
            add_action('admin_init', array($this, 'register_scripts'));

            // load them
            add_action("admin_print_scripts-nav-menus.php", array($this, 'admin_print_scripts'));
            add_action("admin_print_styles-nav-menus.php", array($this, 'admin_print_styles'));

            /* ajax: posttype taxonomies select */
            add_action('wp_ajax_gsm_taxonomies_select', array($this, 'gsm_taxonomies_select'));

            /* ajax: taxonomies select options */
            add_action('wp_ajax_gsm_taxonomies_select_tax', array($this, 'gsm_taxonomies_select_tax'));
        }

        add_filter('wp_get_nav_menu_items', array($this, 'wp_get_nav_menu_items'), 10, 3);

        /* set up nav menu item with custom properties */
        add_filter('wp_setup_nav_menu_item', array($this, 'wp_setup_nav_menu_item'));

        /* customize menu display */
        add_filter('walker_nav_menu_start_el', array($this, 'walker_nav_menu_start_el'), 10, 4);
    }
    function wp_get_nav_menu_items($items, $menu, $args)
    {

        //we don't autopopulate in the nav menu edit screen in wp-admin
        if (is_admin() && $GLOBALS['pagenow'] == 'nav-menus.php')
            return $items;

        $order = sizeof($items) + 1;

        foreach ($items as $item) {

            if ($item->autopopulate !== '1')
                continue;

            switch ($item->autopopulate_type) {

                case 'subpages':

                    $pages = get_pages(apply_filters('gecka-submenu-get_pages', array('child_of' => $item->object_id, 'sort_column' => 'menu_order, post_title', 'post_type' => isset($item->object) ? $item->object : 'page')));

                    /**
                     * removes pages not in requested depth
                     * @TODO check performences impact
                     */
                    if ($_depth = (int)$item->autopopulate_depth) {
                        $walker = new Gecka_Submenu_Posts_Depth_Limit();
                        $args = array($pages, $_depth, array());

                        $pages =  call_user_func_array(array($walker, 'walk'), $args);
                    }

                    $this->setup_posts('post', $item, $pages, $items, $order);

                    break;

                case 'posttype':

                    $posttypes = get_post_types();

                    if (!in_array($item->autopopulate_posttype, $posttypes))
                        break;

                    if ($item->autopopulate_posttype_type === 'posts') {

                        $args = array('post_type' => $item->autopopulate_posttype, 'orderby' => $item->autopopulate_posts_orderby, 'order' => $item->autopopulate_posts_order, 'numberposts' => $item->autopopulate_posts_limit);

                        if ($item->autopopulate_posts_taxonomy) {

                            $args['tax_query'] = array(array('taxonomy' => $item->autopopulate_posts_taxonomy, 'field' => 'id', 'terms' => $item->autopopulate_posts_taxonomy_tax));
                        }

                        $posts = get_posts($args);

                        /**
                         * removes pages not in requested depth
                         * @TODO check performences impact
                         */
                        if ($_depth = (int)$item->autopopulate_posts_depth) {
                            $walker = new Gecka_Submenu_Posts_Depth_Limit();
                            $args = array($posts, $_depth, array());

                            $posts =  call_user_func_array(array($walker, 'walk'), $args);
                        }

                        $this->setup_posts('post', $item, $posts, $items, $order);
                    } else if ($item->autopopulate_posttype_type === 'taxonomies') {

                        $taxonomies = $this->post_type_taxonomies_names($item->autopopulate_posttype);

                        if (!in_array($item->autopopulate_taxonomy, $taxonomies))
                            break;

                        $args = array('orderby' => $item->autopopulate_taxonomies_orderby, 'order' => $item->autopopulate_taxonomies_order, 'number' => $item->autopopulate_taxonomies_limit, 'hide_empty' => $item->autopopulate_taxonomies_hideempty);

                        if ($item->autopopulate_taxonomy_child_of) {
                            //echo $item->autopopulate_taxonomy_child_of; exit;


                            $args['child_of'] = $item->autopopulate_taxonomy_child_of;
                        }

                        $tax = get_terms($item->autopopulate_taxonomy, $args);

                        /**
                         * removes pages not in requested depth
                         * @TODO check performences impact
                         */
                        if ($_depth = (int)$item->autopopulate_taxonomies_depth) {
                            $walker = new Gecka_Submenu_Taxonomies_Depth_Limit();
                            $args = array($tax, $_depth, array());

                            $tax =  call_user_func_array(array($walker, 'walk'), $args);
                        }

                        $this->setup_posts('taxonomy', $item, $tax, $items, $order, $item->autopopulate_taxonomy_child_of ? $item->autopopulate_taxonomy_child_of : 0);
                    }

                    break;
            }
        }
        return $items;
    }

    function setup_posts($type, &$item, &$posts, &$items, &$order, $child_of = 0)
    {

        $_ids = $this->get_ids($type, $order, $posts);

        if (is_array($posts)) {
            foreach ($posts as $key => $post) {

                $id = isset($post->ID) ? $post->ID : $post->term_id;

                $parent_id = $item->ID;

                if (isset($post->post_parent) && $post->post_parent && $post->post_parent != $item->object_id) {
                    $parent_id = $_ids[$post->post_parent];
                }

                if (isset($post->parent) && $post->parent && $post->parent != $item->object_id && $post->parent != $child_of) {
                    $parent_id = $_ids[$post->parent];
                }

                if (isset($post->term_id)) $items[] = $this->menu_item_from_tax($post, $_ids[$id], $parent_id, $order);
                else $items[] = $this->menu_item_from_post($post, $_ids[$id], $parent_id, $order);

                $order++;
            }
        }

        return $posts;
    }

    function get_ids($type, $order, $items)
    {

        $ids = array();

        if (is_array($items)) {
            foreach ($items as $item) {

                $id = $type == 'post' ? $item->ID : $item->term_id;

                $ids[$id] = -$order;

                $order++;
            }
        }


        return $ids;
    }

    function menu_item_from_post($post, $pseudo_id, $parent_id = 0, $order = 0)
    {
        $post = get_post($post);
        $_item = clone $post;

        $id = $_item->ID;

        $_item->ID = $_item->db_id =  $pseudo_id;
        $_item->post_name = '' . $pseudo_id;
        $_item->object = $post->post_type;
        $_item->post_type = 'nav_menu_item';
        $_item->menu_item_parent = $parent_id;
        $_item->object_id = $id;
        $_item->type = 'post_type';
        $_item->menu_order = $order;
        $_item->description = $_item->post_excerpt;


        $object = get_post_type_object($post->post_type);
        $_item->object         = $object->name;
        $_item->type_label     = $object->labels->singular_name;

        $_item->url = get_permalink($post->ID);
        $_item->title = $post->post_title;
        $_item->target = '';
        $_item->attr_title = '';
        $_item->classes = array(0 => '');
        $_item->xfn = '';

        $_item->db_id =  $pseudo_id;

        return $_item;
    }

    function menu_item_from_tax($tax, $pseudo_id, $parent_id = 0, $order = 0)
    {
        $_item = is_object($tax) ? clone $tax : clone get_term($tax);

        $id = $_item->term_id;
        $_item->ID = $_item->db_id =  $pseudo_id;
        $_item->post_name = '' . $pseudo_id;
        $_item->post_title = '';
        $_item->post_excerpt = '';
        $_item->post_content = '';
        $_item->post_parent = $_item->parent;
        $_item->object = $_item->taxonomy;
        $_item->post_type = 'nav_menu_item';
        $_item->menu_item_parent = $parent_id;
        $_item->object_id = $id;
        $_item->type = 'taxonomy';
        $_item->menu_order = $order;
        wp_setup_nav_menu_item($_item);

        $_item->db_id =  $pseudo_id;

        return $_item;
    }


    function walker_nav_menu_start_el($item_output, $item, $depth, $args)
    {

        if (isset($item->autopopulate) && $item->autopopulate == 'subpages') {
            $args = array(
                'depth'        => 0,
                'child_of'     => $item->object_id,
                'echo'         => 0, 'title_li' => ''
            );

            //$item_output = $item_output . '<ul class="sub-menu" >' . wp_list_pages($args) . '</ul>';

        }
        return $item_output;
    }


    /**
     * Setup the nav menu object to have the additionnal properties
     */
    function wp_setup_nav_menu_item($menu_item)
    {
        if (isset($menu_item->post_type)) {

            if ('nav_menu_item' == $menu_item->post_type) {
                $menu_item->autopopulate = empty($menu_item->autopopulate) ? get_post_meta($menu_item->ID, '_menu_item_autopopulate', true) : $menu_item->autopopulate;
                $menu_item->autopopulate_type = empty($menu_item->autopopulate_type) ? get_post_meta($menu_item->ID, '_menu_item_autopopulate_type', true) : $menu_item->autopopulate_type;
                $menu_item->autopopulate_depth = empty($menu_item->autopopulate_depth) ? get_post_meta($menu_item->ID, '_menu_item_autopopulate_depth', true) : $menu_item->autopopulate_depth;

                $menu_item->autopopulate_posttype = empty($menu_item->autopopulate_posttype) ? get_post_meta($menu_item->ID, '_menu_item_autopopulate_posttype', true) : $menu_item->autopopulate_posttype;
                $menu_item->autopopulate_taxonomy = empty($menu_item->autopopulate_taxonomy) ? get_post_meta($menu_item->ID, '_menu_item_autopopulate_taxonomy', true) : $menu_item->autopopulate_taxonomy;
                $menu_item->autopopulate_posttype_type = empty($menu_item->autopopulate_posttype_type) ? get_post_meta($menu_item->ID, '_menu_item_autopopulate_posttype_type', true) : $menu_item->autopopulate_posttype_type;

                $menu_item->autopopulate_depth = empty($menu_item->autopopulate_depth) ? get_post_meta($menu_item->ID, '_menu_item_autopopulate_depth', true) : $menu_item->autopopulate_depth;
                $menu_item->autopopulate_posts_order = empty($menu_item->autopopulate_posts_order) ? get_post_meta($menu_item->ID, '_menu_item_autopopulate_posts_order', true) : $menu_item->autopopulate_posts_order;
                $menu_item->autopopulate_posts_orderby = empty($menu_item->autopopulate_posts_orderby) ? get_post_meta($menu_item->ID, '_menu_item_autopopulate_posts_orderby', true) : $menu_item->autopopulate_posts_orderby;
                $menu_item->autopopulate_posts_limit = empty($menu_item->autopopulate_posts_limit) ? get_post_meta($menu_item->ID, '_menu_item_autopopulate_posts_limit', true) : $menu_item->autopopulate_posts_limit;
                $menu_item->autopopulate_posts_depth = empty($menu_item->autopopulate_posts_depth) ? get_post_meta($menu_item->ID, '_menu_item_autopopulate_posts_depth', true) : $menu_item->autopopulate_posts_depth;
                $menu_item->autopopulate_posts_taxonomy = empty($menu_item->autopopulate_posts_taxonomy) ? get_post_meta($menu_item->ID, '_menu_item_autopopulate_posts_taxonomy', true) : $menu_item->autopopulate_posts_taxonomy;
                $menu_item->autopopulate_posts_taxonomy_tax = empty($menu_item->autopopulate_posts_taxonomy_tax) ? get_post_meta($menu_item->ID, '_menu_item_autopopulate_posts_taxonomy_tax', true) : $menu_item->autopopulate_posts_taxonomy_tax;
                $menu_item->autopopulate_taxonomy_child_of = empty($menu_item->autopopulate_taxonomy_child_of) ? get_post_meta($menu_item->ID, '_menu_item_autopopulate_taxonomy_child_of', true) : $menu_item->autopopulate_taxonomy_child_of;
                $menu_item->autopopulate_taxonomies_order = empty($menu_item->autopopulate_taxonomies_order) ? get_post_meta($menu_item->ID, '_menu_item_autopopulate_taxonomies_order', true) : $menu_item->autopopulate_taxonomies_order;
                $menu_item->autopopulate_taxonomies_orderby = empty($menu_item->autopopulate_taxonomies_orderby) ? get_post_meta($menu_item->ID, '_menu_item_autopopulate_taxonomies_orderby', true) : $menu_item->autopopulate_taxonomies_orderby;
                $menu_item->autopopulate_taxonomies_limit = empty($menu_item->autopopulate_taxonomies_limit) ? get_post_meta($menu_item->ID, '_menu_item_autopopulate_taxonomies_limit', true) : $menu_item->autopopulate_taxonomies_limit;
                $menu_item->autopopulate_taxonomies_depth = empty($menu_item->autopopulate_taxonomies_depth) ? get_post_meta($menu_item->ID, '_menu_item_autopopulate_taxonomies_depth', true) : $menu_item->autopopulate_taxonomies_depth;
                $menu_item->autopopulate_taxonomies_hideempty = empty($menu_item->autopopulate_taxonomies_hideempty) ? get_post_meta($menu_item->ID, '_menu_item_autopopulate_taxonomies_hideempty', true) : $menu_item->autopopulate_taxonomies_hideempty;
            }
        }
        return $menu_item;
    }


    /**
     * Saves the new fields
     */
    function wp_update_nav_menu_item($menu_id, $menu_item_db_id, $args)
    {

        $args['menu-item-autopopulate'] = isset($_POST['menu-item-autopopulate'][$menu_item_db_id]) ? $_POST['menu-item-autopopulate'][$menu_item_db_id] : '0';

        if (empty($args['menu-item-autopopulate'])) {
            $args['menu-item-autopopulate'] = '0';
        }

        update_post_meta($menu_item_db_id, '_menu_item_autopopulate', $args['menu-item-autopopulate']);

        $args['menu-item-autopopulate_type'] = isset($_POST['menu-item-autopopulate_type'][$menu_item_db_id]) ? $_POST['menu-item-autopopulate_type'][$menu_item_db_id] : '';

        if (empty($args['menu-item-autopopulate_type'])) {
            $args['menu-item-autopopulate_type'] = 'subpages';
        }

        $args['menu-item-autopopulate_depth'] = isset($_POST['menu-item-autopopulate_depth'][$menu_item_db_id]) ? $_POST['menu-item-autopopulate_depth'][$menu_item_db_id] : '';

        if (empty($args['menu-item-autopopulate_depth'])) {
            $args['menu-item-autopopulate_depth'] = null;
        }

        update_post_meta($menu_item_db_id, '_menu_item_autopopulate_depth', $args['menu-item-autopopulate_depth']);

        update_post_meta($menu_item_db_id, '_menu_item_autopopulate_type', $args['menu-item-autopopulate_type']);

        $args['menu-item-autopopulate_posttype'] = isset($_POST['menu-item-autopopulate_posttype'][$menu_item_db_id]) ? $_POST['menu-item-autopopulate_posttype'][$menu_item_db_id] : '';

        if (empty($args['menu-item-autopopulate_posttype'])) {
            $args['menu-item-autopopulate_posttype'] = null;
        }

        update_post_meta($menu_item_db_id, '_menu_item_autopopulate_posttype', $args['menu-item-autopopulate_posttype']);

        $args['menu-item-autopopulate_posttype_type'] = isset($_POST['menu-item-autopopulate_posttype_type'][$menu_item_db_id]) ? $_POST['menu-item-autopopulate_posttype_type'][$menu_item_db_id] : '';

        if (empty($args['menu-item-autopopulate_posttype_type'])) {
            $args['menu-item-autopopulate_posttype_type'] = 'posts';
        }

        update_post_meta($menu_item_db_id, '_menu_item_autopopulate_posttype_type', $args['menu-item-autopopulate_posttype_type']);

        $args['menu-item-autopopulate_posts_order'] = isset($_POST['menu-item-autopopulate_posts_order'][$menu_item_db_id]) ? $_POST['menu-item-autopopulate_posts_order'][$menu_item_db_id] : '';

        if (empty($args['menu-item-autopopulate_posts_order'])) {
            $args['menu-item-autopopulate_posts_order'] = 'modified';
        }

        update_post_meta($menu_item_db_id, '_menu_item_autopopulate_posts_order', $args['menu-item-autopopulate_posts_order']);

        $args['menu-item-autopopulate_posts_orderby'] = isset($_POST['menu-item-autopopulate_posts_orderby'][$menu_item_db_id]) ? $_POST['menu-item-autopopulate_posts_orderby'][$menu_item_db_id] : '';

        if (empty($args['menu-item-autopopulate_posts_orderby'])) {
            $args['menu-item-autopopulate_posts_orderby'] = 'ASC';
        }

        update_post_meta($menu_item_db_id, '_menu_item_autopopulate_posts_orderby', $args['menu-item-autopopulate_posts_orderby']);

        $args['menu-item-autopopulate_posts_limit'] = isset($_POST['menu-item-autopopulate_posts_limit'][$menu_item_db_id]) ? (int)$_POST['menu-item-autopopulate_posts_limit'][$menu_item_db_id] : '10';
        update_post_meta($menu_item_db_id, '_menu_item_autopopulate_posts_limit', $args['menu-item-autopopulate_posts_limit']);

        $args['menu-item-autopopulate_posts_depth'] = isset($_POST['menu-item-autopopulate_posts_depth'][$menu_item_db_id]) ? (int)$_POST['menu-item-autopopulate_posts_depth'][$menu_item_db_id] : '10';
        update_post_meta($menu_item_db_id, '_menu_item_autopopulate_posts_depth', $args['menu-item-autopopulate_posts_depth']);

        $args['menu-item-autopopulate_posts_taxonomy'] = isset($_POST['menu-item-autopopulate_posts_taxonomy'][$menu_item_db_id]) ? $_POST['menu-item-autopopulate_posts_taxonomy'][$menu_item_db_id] : '0';
        update_post_meta($menu_item_db_id, '_menu_item_autopopulate_posts_taxonomy', $args['menu-item-autopopulate_posts_taxonomy']);

        $args['menu-item-autopopulate_posts_taxonomy_tax'] = isset($_POST['menu-item-autopopulate_posts_taxonomy_tax'][$menu_item_db_id]) ? (int)$_POST['menu-item-autopopulate_posts_taxonomy_tax'][$menu_item_db_id] : '0';
        update_post_meta($menu_item_db_id, '_menu_item_autopopulate_posts_taxonomy_tax', $args['menu-item-autopopulate_posts_taxonomy_tax']);

        $args['menu-item-autopopulate_taxonomy'] = isset($_POST['menu-item-autopopulate_taxonomy'][$menu_item_db_id]) ? $_POST['menu-item-autopopulate_taxonomy'][$menu_item_db_id] : '';
        if (empty($args['menu-item-autopopulate_taxonomy'])) {
            $args['menu-item-autopopulate_taxonomy'] = null;
        }

        update_post_meta($menu_item_db_id, '_menu_item_autopopulate_taxonomy', $args['menu-item-autopopulate_taxonomy']);

        $args['menu-item-autopopulate_taxonomy_child_of'] = isset($_POST['menu-item-autopopulate_taxonomy_child_of'][$menu_item_db_id]) ? (int)$_POST['menu-item-autopopulate_taxonomy_child_of'][$menu_item_db_id] : '0';
        update_post_meta($menu_item_db_id, '_menu_item_autopopulate_taxonomy_child_of', $args['menu-item-autopopulate_taxonomy_child_of']);


        $args['menu-item-autopopulate_taxonomies_order'] = isset($_POST['menu-item-autopopulate_taxonomies_order'][$menu_item_db_id]) ? $_POST['menu-item-autopopulate_taxonomies_order'][$menu_item_db_id] : '';

        if (empty($args['menu-item-autopopulate_taxonomies_order'])) {
            $args['menu-item-autopopulate_taxonomies_order'] = 'modified';
        }

        update_post_meta($menu_item_db_id, '_menu_item_autopopulate_taxonomies_order', $args['menu-item-autopopulate_taxonomies_order']);

        $args['menu-item-autopopulate_taxonomies_orderby'] = isset($_POST['menu-item-autopopulate_taxonomies_orderby'][$menu_item_db_id]) ? $_POST['menu-item-autopopulate_taxonomies_orderby'][$menu_item_db_id] : '';

        if (empty($args['menu-item-autopopulate_taxonomies_orderby'])) {
            $args['menu-item-autopopulate_taxonomies_orderby'] = 'ASC';
        }

        update_post_meta($menu_item_db_id, '_menu_item_autopopulate_taxonomies_orderby', $args['menu-item-autopopulate_taxonomies_orderby']);

        $args['menu-item-autopopulate_taxonomies_limit'] = isset($_POST['menu-item-autopopulate_taxonomies_limit'][$menu_item_db_id]) ? (int)$_POST['menu-item-autopopulate_taxonomies_limit'][$menu_item_db_id] : '10';
        update_post_meta($menu_item_db_id, '_menu_item_autopopulate_taxonomies_limit', $args['menu-item-autopopulate_taxonomies_limit']);

        $args['menu-item-autopopulate_taxonomies_depth'] = isset($_POST['menu-item-autopopulate_taxonomies_depth'][$menu_item_db_id]) ? (int)$_POST['menu-item-autopopulate_taxonomies_depth'][$menu_item_db_id] : '10';
        update_post_meta($menu_item_db_id, '_menu_item_autopopulate_taxonomies_depth', $args['menu-item-autopopulate_taxonomies_depth']);

        $args['menu-item-autopopulate_taxonomies_hideempty'] = empty($_POST['menu-item-autopopulate_taxonomies_hideempty'][$menu_item_db_id]) ? '0' : '1';
        update_post_meta($menu_item_db_id, '_menu_item_autopopulate_taxonomies_hideempty', $args['menu-item-autopopulate_taxonomies_hideempty']);
    }

    function register_scripts()
    {
        $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '.dev' : '';
        wp_register_script('gecka-submenu-nav-menu-edit', GKSM_URL . "/javascripts/admin-nav-menu-edit$suffix.js", array('jquery'), GKSM_VERSION);
        wp_register_style('gecka-submenu-nav-menu-edit', GKSM_URL . "/css/admin-nav-menu-edit$suffix.css", array(), GKSM_VERSION);
    }

    function admin_print_styles()
    {
        wp_enqueue_style('gecka-submenu-nav-menu-edit');
    }

    function admin_print_scripts()
    {
        wp_enqueue_script('gecka-submenu-nav-menu-edit');
    }

    /**
     * Adds a custom fields
     */
    function custom_walker($a)
    {
        include 'Nav-Menu-Edit-Walker.php';
        return 'Gecka_Walker_Nav_Menu_Edit';
    }

    function wp_nav_menu_item_custom_fields($item_id, $item, $depth, $args)
    {
        include GKSM_PATH . '/views/Nav-Menu-Fields.php';
    }

    /**
     * Gets all taxonmies of a specific post type
     */

    function post_type_taxonomies_select($post_type, $name, $id, $selected = '', $class = '', $show_all = false, $action = '')
    {

        $select_options = $this->post_type_taxonomies_select_options($post_type, $selected, $show_all);

        $o = '';

        $o .= '<select name="' . $name . '" id="' . $id . '" class="' . $class . '" onchange="' . $action . '">';

        $o .= $select_options ? $select_options : '';

        $o .= '</select>';
        return $o;
    }

    function post_type_taxonomies_select_options($post_type, $selected = '', $show_all = false)
    {

        $taxonomies = $this->post_type_taxonomies($post_type);
        if (!sizeof($taxonomies)) return false;

        $o = '';

        if ($show_all) $o = "\t<option value='0'>" . __('All') . "</option>\n";

        foreach ($taxonomies as $tax) {
            $_selected = selected($selected, $tax->name, false);
            $o .= '<option value="' . $tax->name . '"' . $_selected . '>' . $tax->label . '</option>';
        }

        return $o;
    }

    function post_type_taxonomies_tax_select($taxonomy, $name, $id, $selected = '', $class = '', $action = '', $show_all = false)
    {

        $select_options = $this->post_type_taxonomies_select_tax_options($taxonomy, $selected, $show_all);

        $o = '';

        $o .= '<select name="' . $name . '" id="' . $id . '"  class="' . $class . '" onchange="' . $action . '"  >';

        $o .= $select_options ? $select_options : '';

        $o .= '</select>';
        return $o;
    }

    function post_type_taxonomies_select_tax_options($taxonomy, $selected = '', $show_all = false)
    {


        if (!$taxonomy) return;
        $o = '';

        if ($show_all)
            $o .= "\t<option value='0'>" . $show_all . "</option>\n";

        $args = array(
            'show_count' => 0,
            'selected' => $selected,
            'show_last_update' => 0,
        );

        $taxonomies = get_terms($taxonomy, 'hide_empty=0');

        if (!sizeof($taxonomies)) return $o;

        $o .= walk_category_dropdown_tree($taxonomies, 0, $args);

        return $o;
    }

    /**
     * Gets all taxonmies of a specific post type
     */

    function post_type_taxonomies($post_type)
    {

        global $wp_taxonomies;

        $tax = array();

        foreach ($wp_taxonomies as $taxonomy) {
            if (in_array($post_type, $taxonomy->object_type)) $tax[] = $taxonomy;
        }

        return $tax;
    }

    function post_type_taxonomies_names($post_type)
    {

        global $wp_taxonomies;

        $tax = array();

        foreach ($wp_taxonomies as $taxonomy) {
            if (in_array($post_type, $taxonomy->object_type)) $tax[] = $taxonomy->name;
        }

        return $tax;
    }

    function gsm_taxonomies_select()
    {

        $post_type = $_REQUEST['post_type'];

        if (isset($_REQUEST['show_all']) && $_REQUEST['show_all']) echo "<option value='0'>" . __('All') . "</option>";

        echo $this->post_type_taxonomies_select_options($post_type);

        die();
    }

    function gsm_taxonomies_select_tax()
    {

        $taxonomy = $_REQUEST['tax'];

        //if(isset($_REQUEST['show_all']) && $_REQUEST['show_all']) echo "<option value='0'>".__('All')."</option>";

        $Show_all = false;
        if (isset($_REQUEST['Root'])) {
            $Show_all = __('Root');
        }

        echo $this->post_type_taxonomies_select_tax_options($taxonomy, '', $Show_all);

        die();
    }
}

class Gecka_Submenu_Posts_Depth_Limit extends Walker
{
    var $db_fields = array('parent' => 'post_parent', 'id' => 'ID');

    function start_el(&$output, $page, $depth = 0, $args = array(), $id = 0)
    {
        if (!is_array($output)) $output = array();

        $output[] = $page;
    }
}

class Gecka_Submenu_Taxonomies_Depth_Limit extends Gecka_Submenu_Posts_Depth_Limit
{
    var $db_fields = array('parent' => 'parent', 'id' => 'term_id');
}
