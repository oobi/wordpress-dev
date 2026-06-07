<?php
/*
  Plugin Name: Gravity Forms PayWayApi API Add-On
  Plugin URI: http://www.gravityforms.com
  Description: Integrates Gravity Forms with PayWayApi, enabling end users to purchase goods and services through Gravity Forms.
  Version: 1.2
  Author: Sydney Ecommerce
  Author URI: http://www.sydneyecommerce.com.au

  ------------------------------------------------------------------------
 */

add_action('wp', array('GFPayWayApi', 'maybe_thankyou_page'), 5);
add_action('init', array('GFPayWayApi', 'init'));

register_activation_hook(__FILE__, array("GFPayWayApi", "add_permissions"));

add_action("gform_enable_credit_card_field", "enable_creditcard");

function enable_creditcard($is_enabled) {
    return true;
}

class GFPayWayApi {

    private static $path = "gravityformspaywayapi/paywayapi.php";
    private static $url = "http://www.gravityforms.com";
    private static $slug = "gravityformspaywayapi";
    private static $version = "1.0";
    private static $min_gravityforms_version = "1.5";

    //Plugin starting point. Will load appropriate files
    public static function init() {

        if (RG_CURRENT_PAGE == "plugins.php") {
            //loading translations
            load_plugin_textdomain('gravityformspaywayapi', FALSE, '/gravityformspaywayapi/languages');
        }

        if (!self::is_gravityforms_supported())
            return;

        if (is_admin()) {
            //loading translations
            load_plugin_textdomain('gravityformspaywayapi', FALSE, '/gravityformspaywayapi/languages');



            //integrating with Members plugin
            if (function_exists('members_get_capabilities'))
                add_filter('members_get_capabilities', array("GFPayWayApi", "members_get_capabilities"));

            //creates the subnav left menu
            add_filter("gform_addon_navigation", array('GFPayWayApi', 'create_menu'));

            if (self::is_paywayapi_page()) {

                //enqueueing sack for AJAX requests
                wp_enqueue_script(array("sack"));

                //loading data lib
                require_once(self::get_base_path() . "/data.php");


                //loading Gravity Forms tooltips
                require_once(GFCommon::get_base_path() . "/tooltips.php");
                add_filter('gform_tooltips', array('GFPayWayApi', 'tooltips'));

                //runs the setup when version changes
                self::setup();
            } else if (in_array(RG_CURRENT_PAGE, array("admin-ajax.php"))) {

                //loading data class
                require_once(self::get_base_path() . "/data.php");

                add_action('wp_ajax_gf_paywayapi_update_feed_active', array('GFPayWayApi', 'update_feed_active'));
                add_action('wp_ajax_gf_select_paywayapi_form', array('GFPayWayApi', 'select_paywayapi_form'));
                add_action('wp_ajax_gf_paywayapi_load_notifications', array('GFPayWayApi', 'load_notifications'));
            }
        } else {
            //loading data class
            require_once(self::get_base_path() . "/data.php");

            //handling post submission.
            add_filter("gform_confirmation", array("GFPayWayApi", "send_to_paywayapi"), 1000, 4);
            add_filter("gform_disable_user_notification", array("GFPayWayApi", "delay_autoresponder"), 10, 3);
            add_filter("gform_disable_admin_notification", array("GFPayWayApi", "delay_admin_notification"), 10, 3);
            add_filter("gform_disable_notification", array("GFPayWayApi", "delay_notification"), 10, 4);
        }
    }

    public static function update_feed_active() {
        check_ajax_referer('gf_paywayapi_update_feed_active', 'gf_paywayapi_update_feed_active');
        $id = $_POST["feed_id"];
        $feed = GFPayWayApiData::get_feed($id);
        GFPayWayApiData::update_feed($id, $feed["form_id"], $_POST["is_active"], $feed["meta"]);
    }

    public static function delay_admin_notification($is_disabled, $form, $lead) {

        $config = self::get_active_config($form);

        if (!$config)
            return $is_disabled;

        return isset($config["meta"]["delay_notification"]) ? $config["meta"]["delay_notification"] == true : $is_disabled;
    }

    public static function delay_autoresponder($is_disabled, $form, $lead) {

        $config = self::get_active_config($form);

        if (!$config)
            return $is_disabled;

        return isset($config["meta"]["delay_autoresponder"]) ? $config["meta"]["delay_autoresponder"] == true : $is_disabled;
    }

    public static function get_active_config($form) {

        require_once(self::get_base_path() . "/data.php");

        $configs = GFPayWayApiData::get_feed_by_form($form["id"], true);

        if (!$configs)
            return false;

        foreach ($configs as $config) {

            if (self::has_paywayapi_condition($form, $config))
                return $config;
        }

        return false;
    }

    public static function load_notifications() {

        $form_id = $_POST["form_id"];

        $form = RGFormsModel::get_form_meta($form_id);

        $notifications = array();

        if (is_array(rgar($form, "notifications"))) {

            foreach ($form["notifications"] as $notification) {

                $notifications[] = array("name" => $notification["name"], "id" => $notification["id"]);
            }
        }

        die(json_encode($notifications));
    }

    //------------------------------------------------------------------------
    //Creates PayWayApi left nav menu under Forms
    public static function create_menu($menus) {

        // Adding submenu if user has access
        $permission = self::has_access("gravityforms_paywayapi");
        if (!empty($permission))
            $menus[] = array("name" => "gf_paywayapi", "label" => __("PayWayApi", "gravityformspaywayapi"), "callback" => array("GFPayWayApi", "paywayapi_page"), "permission" => $permission);

        return $menus;
    }

    //Creates or updates database tables. Will only run when version changes
    private static function setup() {
        if (get_option("gf_paywayapi_version") != self::$version)
            GFPayWayApiData::update_table();

        update_option("gf_paywayapi_version", self::$version);
    }

    public static function delay_notification($is_disabled, $notification, $form, $lead) {

        $config = self::get_active_config($form);

        if (!$config)
            return $is_disabled;

        $selected_notifications = is_array(rgar($config["meta"], "selected_notifications")) ? rgar($config["meta"], "selected_notifications") : array();

        return isset($config["meta"]["delay_notifications"]) && in_array($notification["id"], $selected_notifications) ? true : $is_disabled;
    }

    private static function get_selected_notifications($config, $form) {

        $selected_notifications = is_array(rgar($config['meta'], 'selected_notifications')) ? rgar($config['meta'], 'selected_notifications') : array();

        if (empty($selected_notifications)) {

            if (!rgempty("delay_autoresponder", $config['meta'])) {

                $user_notification = self::get_notification_by_type($form, "user");

                if ($user_notification)
                    $selected_notifications[] = $user_notification["id"];
            }

            if (!rgempty("delay_notification", $config['meta'])) {

                $admin_notification = self::get_notification_by_type($form, "admin");

                if ($admin_notification)
                    $selected_notifications[] = $admin_notification["id"];
            }
        }

        return $selected_notifications;
    }

    private static function get_notification_by_type($form, $notification_type) {

        if (!is_array($form["notifications"]))
            return false;

        foreach ($form["notifications"] as $notification) {

            if ($notification["type"] == $notification_type)
                return $notification;
        }

        return false;
    }

    //Adds feed tooltips to the list of tooltips
    public static function tooltips($tooltips) {
        $paywayapi_tooltips = array(
            "paywayapi_biller_code" => "<h6>" . __("PayWayApi Biller Code", "gravityformspaywayapi") . "</h6>" . __("Enter the PayWayApi biller code where payment should be received.", "gravityformspaywayapi"),
            "paywayapi_mode" => "<h6>" . __("Mode", "gravityformspaywayapi") . "</h6>" . __("Select Production to receive live payments. Select Test for testing purposes when using the PayWayApi development sandbox.", "gravityformspaywayapi"),
            "paywayapi_transaction_type" => "<h6>" . __("Transaction Type", "gravityformspaywayapi") . "</h6>" . __("Select which PayWayApi transaction type should be used. Products and Services, Donations or Subscription.", "gravityformspaywayapi"),
            "paywayapi_gravity_form" => "<h6>" . __("Gravity Form", "gravityformspaywayapi") . "</h6>" . __("Select which Gravity Forms you would like to integrate with PayWayApi.", "gravityformspaywayapi"),
            "paywayapi_customer" => "<h6>" . __("Customer", "gravityformspaywayapi") . "</h6>" . __("Map your Form Fields to the available PayWayApi customer information fields.", "gravityformspaywayapi"),
            "paywayapi_conditional" => "<h6>" . __("PayWayApi Condition", "gravityformspaywayapi") . "</h6>" . __("When the PayWayApi condition is enabled, form submissions will only be sent to PayWayApi when the condition is met. When disabled all form submissions will be sent to PayWayApi.", "gravityformspaywayapi")
        );
        return array_merge($tooltips, $paywayapi_tooltips);
    }

    public static function paywayapi_page() {
        $view = rgget("view");
        if ($view == "edit")
            self::edit_page(rgget("id"));
        else
            self::list_page();
    }

    //Displays the paywayapi feeds list page
    private static function list_page() {
        if (!self::is_gravityforms_supported()) {
            die(__(sprintf("PayWayApi Add-On requires Gravity Forms %s. Upgrade automatically on the %sPlugin page%s.", self::$min_gravityforms_version, "<a href='plugins.php'>", "</a>"), "gravityformspaywayapi"));
        }

        if (rgpost('action') == "delete") {
            check_admin_referer("list_action", "gf_paywayapi_list");

            $id = absint($_POST["action_argument"]);
            GFPayWayApiData::delete_feed($id);
            ?>
            <div class="updated fade" style="padding:6px"><?php _e("Feed deleted.", "gravityformspaywayapi") ?></div>
            <?php
        } else if (!empty($_POST["bulk_action"])) {
            check_admin_referer("list_action", "gf_paywayapi_list");
            $selected_feeds = $_POST["feed"];
            if (is_array($selected_feeds)) {
                foreach ($selected_feeds as $feed_id)
                    GFPayWayApiData::delete_feed($feed_id);
            }
            ?>
            <div class="updated fade" style="padding:6px"><?php _e("Feeds deleted.", "gravityformspaywayapi") ?></div>
            <?php
        }
        ?>
        <div class="wrap">
            <img alt="<?php _e("PayWayApi Transactions", "gravityformspaywayapi") ?>" src="<?php echo self::get_base_url() ?>/images/paywayapi_wordpress_icon_32.png" style="float:left; margin:15px 7px 0 0;"/>
            <h2><?php
                _e("PayWayApi Forms", "gravityformspaywayapi");
                ?>
                <a class="button add-new-h2" href="admin.php?page=gf_paywayapi&view=edit&id=0"><?php _e("Add New", "gravityformspaywayapi") ?></a>

            </h2>

            <form id="feed_form" method="post">
                <?php wp_nonce_field('list_action', 'gf_paywayapi_list') ?>
                <input type="hidden" id="action" name="action"/>
                <input type="hidden" id="action_argument" name="action_argument"/>

                <div class="tablenav">
                    <div class="alignleft actions" style="padding:8px 0 7px 0;">
                        <label class="hidden" for="bulk_action"><?php _e("Bulk action", "gravityformspaywayapi") ?></label>
                        <select name="bulk_action" id="bulk_action">
                            <option value=''> <?php _e("Bulk action", "gravityformspaywayapi") ?> </option>
                            <option value='delete'><?php _e("Delete", "gravityformspaywayapi") ?></option>
                        </select>
                        <?php
                        echo '<input type="submit" class="button" value="' . __("Apply", "gravityformspaywayapi") . '" onclick="if( jQuery(\'#bulk_action\').val() == \'delete\' && !confirm(\'' . __("Delete selected feeds? ", "gravityformspaywayapi") . __("\'Cancel\' to stop, \'OK\' to delete.", "gravityformspaywayapi") . '\')) { return false; } return true;"/>';
                        ?>
                    </div>
                </div>
                <table class="widefat fixed" cellspacing="0">
                    <thead>
                        <tr>
                            <th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
                            <th scope="col" id="active" class="manage-column check-column"></th>
                            <th scope="col" class="manage-column"><?php _e("Form", "gravityformspaywayapi") ?></th>
                            <th scope="col" class="manage-column"><?php _e("Transaction Type", "gravityformspaywayapi") ?></th>
                        </tr>
                    </thead>

                    <tfoot>
                        <tr>
                            <th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
                            <th scope="col" id="active" class="manage-column check-column"></th>
                            <th scope="col" class="manage-column"><?php _e("Form", "gravityformspaywayapi") ?></th>
                            <th scope="col" class="manage-column"><?php _e("Transaction Type", "gravityformspaywayapi") ?></th>
                        </tr>
                    </tfoot>

                    <tbody class="list:user user-list">
                        <?php
                        $settings = GFPayWayApiData::get_feeds();
                        if (is_array($settings) && sizeof($settings) > 0) {
                            foreach ($settings as $setting) {
                                ?>
                                <tr class='author-self status-inherit' valign="top">
                                    <th scope="row" class="check-column"><input type="checkbox" name="feed[]" value="<?php echo $setting["id"] ?>"/></th>
                                    <td><img src="<?php echo self::get_base_url() ?>/images/active<?php echo intval($setting["is_active"]) ?>.png" alt="<?php echo $setting["is_active"] ? __("Active", "gravityformspaywayapi") : __("Inactive", "gravityformspaywayapi"); ?>" title="<?php echo $setting["is_active"] ? __("Active", "gravityformspaywayapi") : __("Inactive", "gravityformspaywayapi"); ?>" onclick="ToggleActive(this, <?php echo $setting['id'] ?>);" /></td>
                                    <td class="column-title">
                                        <a href="admin.php?page=gf_paywayapi&view=edit&id=<?php echo $setting["id"] ?>" title="<?php _e("Edit", "gravityformspaywayapi") ?>"><?php echo $setting["form_title"] ?></a>
                                        <div class="row-actions">
                                            <span class="edit">
                                                <a title="<?php _e("Edit", "gravityformspaywayapi") ?>" href="admin.php?page=gf_paywayapi&view=edit&id=<?php echo $setting["id"] ?>" ><?php _e("Edit", "gravityformspaywayapi") ?></a>
                                                |
                                            </span>
                                            <span class="view">
                                                <a title="<?php _e("View Entries", "gravityformspaywayapi") ?>" href="admin.php?page=gf_entries&view=entries&id=<?php echo $setting["form_id"] ?>"><?php _e("Entries", "gravityformspaywayapi") ?></a>
                                                |
                                            </span>
                                            <span class="trash">
                                                <a title="<?php _e("Delete", "gravityformspaywayapi") ?>" href="javascript: if(confirm('<?php _e("Delete this feed? ", "gravityformspaywayapi") ?> <?php _e("\'Cancel\' to stop, \'OK\' to delete.", "gravityformspaywayapi") ?>')){ DeleteSetting(<?php echo $setting["id"] ?>);}"><?php _e("Delete", "gravityformspaywayapi") ?></a>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="column-date">
                                        <?php
                                        switch ($setting["meta"]["type"]) {
                                            case "product" :
                                                _e("Product and Services", "gravityformspaywayapi");
                                                break;

                                            case "donation" :
                                                _e("Donation", "gravityformspaywayapi");
                                                break;
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="4" style="padding:20px;">
                                    <?php echo sprintf(__("You don't have any PayWayApi feeds configured. Let's go %screate one%s!", "gravityformspaywayapi"), '<a href="admin.php?page=gf_paywayapi&view=edit&id=0">', "</a>"); ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </form>
        </div>
        <script type="text/javascript">
            function DeleteSetting(id) {
                jQuery("#action_argument").val(id);
                jQuery("#action").val("delete");
                jQuery("#feed_form")[0].submit();
            }
            function ToggleActive(img, feed_id) {
                var is_active = img.src.indexOf("active1.png") >= 0
                if (is_active) {
                    img.src = img.src.replace("active1.png", "active0.png");
                    jQuery(img).attr('title', '<?php _e("Inactive", "gravityformspaywayapi") ?>').attr('alt', '<?php _e("Inactive", "gravityformspaywayapi") ?>');
                } else {
                    img.src = img.src.replace("active0.png", "active1.png");
                    jQuery(img).attr('title', '<?php _e("Active", "gravityformspaywayapi") ?>').attr('alt', '<?php _e("Active", "gravityformspaywayapi") ?>');
                }

                var mysack = new sack("<?php echo admin_url("admin-ajax.php") ?>");
                mysack.execute = 1;
                mysack.method = 'POST';
                mysack.setVar("action", "gf_paywayapi_update_feed_active");
                mysack.setVar("gf_paywayapi_update_feed_active", "<?php echo wp_create_nonce("gf_paywayapi_update_feed_active") ?>");
                mysack.setVar("feed_id", feed_id);
                mysack.setVar("is_active", is_active ? 0 : 1);
                mysack.encVar("cookie", document.cookie, false);
                mysack.onError = function () {
                    alert('<?php _e("Ajax error while updating feed", "gravityformspaywayapi") ?>')
                };
                mysack.runAJAX();

                return true;
            }


        </script>
        <?php
    }

    private static function get_product_field_options($productFields, $selectedValue) {
        $options = "<option value=''>" . __("Select a product", "gravityformspaywayapi") . "</option>";
        foreach ($productFields as $field) {
            $label = GFCommon::truncate_middle($field["label"], 30);
            $selected = $selectedValue == $field["id"] ? "selected='selected'" : "";
            $options .= "<option value='{$field["id"]}' {$selected}>{$label}</option>";
        }

        return $options;
    }

    // Edit Page
    private static function edit_page() {
        ?>
        <style>
            #paywayapi_submit_container{clear:both;}
            .paywayapi_col_heading{padding-bottom:2px; border-bottom: 1px solid #ccc; font-weight:bold; width:120px;}
            .paywayapi_field_cell {padding: 6px 17px 0 0; margin-right:15px;}

            .paywayapi_validation_error{ background-color:#FFDFDF; margin-top:4px; margin-bottom:6px; padding-top:6px; padding-bottom:6px; border:1px dotted #C89797;}
            .paywayapi_validation_error span {color: red;}
            .left_header{float:left; width:200px;}
            .margin_vertical_10{margin: 10px 0; padding-left:5px;}
            .margin_vertical_30{margin: 30px 0; padding-left:5px;}
            .width-1{width:300px;}
            .gf_paywayapi_invalid_form{margin-top:30px; background-color:#FFEBE8;border:1px solid #CC0000; padding:10px; width:600px;}
        </style>

        <script type="text/javascript">

            var form = Array();

            function ToggleNotifications() {

                var container = jQuery("#gf_paywayapi_notification_container");

                var isChecked = jQuery("#gf_paywayapi_delay_notifications").is(":checked");

                if (isChecked) {

                    container.slideDown();

                    var isLoaded = jQuery(".gf_paywayapi_notification").length > 0

                    if (!isLoaded) {

                        container.html("<li><img src='<?php echo self::get_base_url() ?>/images/loading.gif' title='<?php _e("Please wait...", "gravityformspaywayapi"); ?>'></li>");

                        jQuery.post(ajaxurl, {
                            action: "gf_paywayapi_load_notifications",
                            form_id: form["id"],
                        },
                                function (response) {

                                    var notifications = jQuery.parseJSON(response);

                                    if (!notifications) {

                                        container.html("<li><div class='error' padding='20px;'><?php _e("Notifications could not be loaded. Please try again later or contact support", "gravityformspaywayapi") ?></div></li>");

                                    } else if (notifications.length == 0) {

                                        container.html("<li><div class='error' padding='20px;'><?php _e("The form selected does not have any notifications.", "gravityformspaywayapi") ?></div></li>");

                                    } else {

                                        var str = "";

                                        for (var i = 0; i < notifications.length; i++) {

                                            str += "<li class='gf_paywayapi_notification'>"

                                                    + "<input type='checkbox' value='" + notifications[i]["id"] + "' name='gf_paywayapi_selected_notifications[]' id='gf_paywayapi_selected_notifications' checked='checked' /> "

                                                    + "<label class='inline' for='gf_paywayapi_selected_notifications'>" + notifications[i]["name"] + "</label>";

                                            +"</li>";

                                        }

                                        container.html(str);

                                    }

                                }

                        );

                    }

                    jQuery(".gf_paywayapi_notification input").prop("checked", true);

                } else {

                    container.slideUp();

                    jQuery(".gf_paywayapi_notification input").prop("checked", false);

                }

            }

        </script>
        <div class="wrap">
            <img alt="<?php _e("PayWayApi", "gravityformspaywayapi") ?>" style="margin: 15px 7px 0pt 0pt; float: left;" src="<?php echo self::get_base_url() ?>/images/paywayapi_wordpress_icon_32.png"/>
            <h2><?php _e("PayWayApi Transaction Settings", "gravityformspaywayapi") ?></h2>

            <?php
            //getting setting id (0 when creating a new one)
            $id = !empty($_POST["paywayapi_setting_id"]) ? $_POST["paywayapi_setting_id"] : absint($_GET["id"]);
            $config = empty($id) ? array("meta" => array(), "is_active" => true) : GFPayWayApiData::get_feed($id);
            $is_validation_error = false;
            $config["form_id"] = rgpost("gf_paywayapi_submit") ? absint(rgpost("gf_paywayapi_form")) : $config["form_id"];

            $form = isset($config["form_id"]) && $config["form_id"] ? $form = RGFormsModel::get_form_meta($config["form_id"]) : array();
            //updating meta information
            if (rgpost("gf_paywayapi_submit")) {

                $config["form_id"] = absint(rgpost("gf_paywayapi_form"));
                //$config["meta"]["biller_code"] = trim(rgpost("gf_paywayapi_biller_code"));
                $config["meta"]["merchant_id"] = trim(rgpost("gf_paywayapi_merchant_id"));
                $config["meta"]["username"] = trim(rgpost("gf_paywayapi_username"));
                $config["meta"]["password"] = trim(rgpost("gf_paywayapi_password"));
                //$config["meta"]["encr_key"] = trim(rgpost("gf_paywayapi_encr_key"));
                //$config["meta"]["proxy_host"] = trim(rgpost("gf_paywayapi_proxy_host"));
                //$config["meta"]["proxy_port"] = trim(rgpost("gf_paywayapi_proxy_port"));
                //config["meta"]["proxy_user"] = trim(rgpost("gf_paywayapi_proxy_user"));
                //$config["meta"]["proxy_password"] = trim(rgpost("gf_paywayapi_proxy_password"));
                $config["meta"]["certificate"] = trim(rgpost("gf_paywayapi_certificate"));

                $config["meta"]["mode"] = rgpost("gf_paywayapi_mode");
                $config["meta"]["type"] = rgpost("gf_paywayapi_type");
                //$config["meta"]["style"] = rgpost("gf_paywayapi_page_style");
                if (isset($form["notifications"])) {

                    //new notification settings

                    $config["meta"]["delay_notifications"] = rgpost('gf_paywayapi_delay_notifications');

                    $config["meta"]["selected_notifications"] = $config["meta"]["delay_notifications"] ? rgpost('gf_paywayapi_selected_notifications') : array();

                    if (isset($config["meta"]["delay_autoresponder"]))
                        unset($config["meta"]["delay_autoresponder"]);

                    if (isset($config["meta"]["delay_notification"]))
                        unset($config["meta"]["delay_notification"]);
                } else {

                    //legacy notification settings (for backwards compatibility)

                    $config["meta"]["delay_autoresponder"] = rgpost('gf_paywayapi_delay_autoresponder');

                    $config["meta"]["delay_notification"] = rgpost('gf_paywayapi_delay_notification');

                    if (isset($config["meta"]["delay_notifications"]))
                        unset($config["meta"]["delay_notifications"]);

                    if (isset($config["meta"]["selected_notifications"]))
                        unset($config["meta"]["selected_notifications"]);
                }
                // paywayapi conditional
                $config["meta"]["paywayapi_conditional_enabled"] = rgpost('gf_paywayapi_conditional_enabled');
                $config["meta"]["paywayapi_conditional_field_id"] = rgpost('gf_paywayapi_conditional_field_id');
                $config["meta"]["paywayapi_conditional_operator"] = rgpost('gf_paywayapi_conditional_operator');
                $config["meta"]["paywayapi_conditional_value"] = rgpost('gf_paywayapi_conditional_value');

                //-----------------

                $customer_fields = self::get_customer_fields();
                $config["meta"]["customer_fields"] = array();
                foreach ($customer_fields as $field) {
                    $config["meta"]["customer_fields"][$field["name"]] = $_POST["paywayapi_customer_field_{$field["name"]}"];
                }

                $config = apply_filters('gform_paywayapi_save_config', $config);

                $is_validation_error = apply_filters("gform_paywayapi_config_validation", false, $config);

                if (!$is_validation_error) {
                    $id = GFPayWayApiData::update_feed($id, $config["form_id"], $config["is_active"], $config["meta"]);
                    ?>
                    <div class="updated fade" style="padding:6px"><?php echo sprintf(__("Feed Updated. %sback to list%s", "gravityformspaywayapi"), "<a href='?page=gf_paywayapi'>", "</a>") ?></div>
                    <?php
                } else {
                    $is_validation_error = true;
                }
            }

            $form = isset($config["form_id"]) && $config["form_id"] ? $form = RGFormsModel::get_form_meta($config["form_id"]) : array();
            ?>
            <form method="post" action="">
                <input type="hidden" name="paywayapi_setting_id" value="<?php echo $id ?>" />

                <div class="margin_vertical_10 <?php echo $is_validation_error ? "paywayapi_validation_error" : "" ?>">
                    <?php
                    if ($is_validation_error) {
                        ?>
                        <span><?php _e('There was an issue saving your feed. Please address the errors below and try again.'); ?></span>
                        <?php
                    }
                    ?>
                </div> <!-- / validation message -->

                <div class="margin_vertical_10">
                    <label class="left_header" for="gf_paywayapi_email"><?php _e("PayWayApi Merchant id", "gravityformspaywayapi"); ?> <?php gform_tooltip("paywayapi_merchant_id") ?></label>
                    <input type="text" name="gf_paywayapi_merchant_id" id="gf_paywayapi_merchant_id" value="<?php echo rgar($config['meta'], 'merchant_id') ?>" class="width-1"/>
                </div>
                <div class="margin_vertical_10">
                    <label class="left_header" for="gf_paywayapi_username"><?php _e("PayWayApi Username", "gravityformspaywayapi"); ?> <?php gform_tooltip("paywayapi_username") ?></label>
                    <input type="text" name="gf_paywayapi_username" id="gf_paywayapi_username" value="<?php echo rgar($config['meta'], 'username') ?>" class="width-1"/>
                </div>
                <div class="margin_vertical_10">
                    <label class="left_header" for="gf_paywayapi_password"><?php _e("PayWayApi Password", "gravityformspaywayapi"); ?> <?php gform_tooltip("paywayapi_password") ?></label>
                    <input type="text" name="gf_paywayapi_password" id="gf_paywayapi_password" value="<?php echo rgar($config['meta'], 'password') ?>" class="width-1"/>
                </div>
                <div class="margin_vertical_10">
                    <label class="left_header" for="gf_paywayapi_certificate"><?php _e("PayWayApi certificate", "gravityformspaywayapi"); ?> <?php gform_tooltip("paywayapi_certificate") ?></label>
                    <input type="text" name="gf_paywayapi_certificate" id="gf_paywayapi_encr_key" value="<?php echo rgar($config['meta'], 'certificate') ?>" class="width-1"/> (wp-content/plugins/gravityformspaywayapi/)
                </div>

                <div class="margin_vertical_10">
                    <label class="left_header"><?php _e("Mode", "gravityformspaywayapi"); ?> <?php gform_tooltip("paywayapi_mode") ?></label>

                    <input type="radio" name="gf_paywayapi_mode" id="gf_paywayapi_mode_production" value="production" <?php echo rgar($config['meta'], 'mode') != "test" ? "checked='checked'" : "" ?>/>
                    <label class="inline" for="gf_paywayapi_mode_production"><?php _e("Production", "gravityformspaywayapi"); ?></label>
                    &nbsp;&nbsp;&nbsp;
                    <input type="radio" name="gf_paywayapi_mode" id="gf_paywayapi_mode_test" value="test" <?php echo rgar($config['meta'], 'mode') == "test" ? "checked='checked'" : "" ?>/>
                    <label class="inline" for="gf_paywayapi_mode_test"><?php _e("Test", "gravityformspaywayapi"); ?></label>
                </div>
                <div class="margin_vertical_10">
                    <label class="left_header" for="gf_paywayapi_type"><?php _e("Transaction Type", "gravityformspaywayapi"); ?> <?php gform_tooltip("paywayapi_transaction_type") ?></label>

                    <select id="gf_paywayapi_type" name="gf_paywayapi_type" onchange="SelectType(jQuery(this).val());">
                        <option value=""><?php _e("Select a transaction type", "gravityformspaywayapi") ?></option>
                        <option value="product" <?php echo rgar($config['meta'], 'type') == "product" ? "selected='selected'" : "" ?>><?php _e("Products and Services", "gravityformspaywayapi") ?></option>
                        <option value="donation" <?php echo rgar($config['meta'], 'type') == "donation" ? "selected='selected'" : "" ?>><?php _e("Donations", "gravityformspaywayapi") ?></option>
                    </select>
                </div>
                <div id="paywayapi_form_container" valign="top" class="margin_vertical_10" <?php echo empty($config["meta"]["type"]) ? "style='display:none;'" : "" ?>>
                    <label for="gf_paywayapi_form" class="left_header"><?php _e("Gravity Form", "gravityformspaywayapi"); ?> <?php gform_tooltip("paywayapi_gravity_form") ?></label>

                    <select id="gf_paywayapi_form" name="gf_paywayapi_form" onchange="SelectForm(jQuery('#gf_paywayapi_type').val(), jQuery(this).val(), '<?php echo rgar($config, 'id') ?>');">
                        <option value=""><?php _e("Select a form", "gravityformspaywayapi"); ?> </option>
                        <?php
                        $active_form = rgar($config, 'form_id');
                        $available_forms = GFPayWayApiData::get_available_forms($active_form);

                        foreach ($available_forms as $current_form) {
                            $selected = absint($current_form->id) == rgar($config, 'form_id') ? 'selected="selected"' : '';
                            ?>

                            <option value="<?php echo absint($current_form->id) ?>" <?php echo $selected; ?>><?php echo esc_html($current_form->title) ?></option>

                            <?php
                        }
                        ?>
                    </select>
                    &nbsp;&nbsp;
                    <img src="<?php echo GFPayWayApi::get_base_url() ?>/images/loading.gif" id="paywayapi_wait" style="display: none;"/>

                    <div id="gf_paywayapi_invalid_product_form" class="gf_paywayapi_invalid_form"  style="display:none;">
                        <?php _e("The form selected does not have any Product fields. Please add a Product field to the form and try again.", "gravityformspaywayapi") ?>
                    </div>
                    <div id="gf_paywayapi_invalid_donation_form" class="gf_paywayapi_invalid_form" style="display:none;">
                        <?php _e("The form selected does not have any Donation fields. Please add a Donation field to the form and try again.", "gravityformspaywayapi") ?>
                    </div>
                </div>
                <div id="paywayapi_field_group" valign="top" <?php echo empty($config["meta"]["type"]) || empty($config["form_id"]) ? "style='display:none;'" : "" ?>>
                    <div class="margin_vertical_10">
                        <label class="left_header"><?php _e("Customer", "gravityformspaywayapi"); ?> <?php gform_tooltip("paywayapi_customer") ?></label>

                        <div id="paywayapi_customer_fields">
                            <?php
                            if (!empty($form))
                                echo self::get_customer_information($form, $config);
                            ?>
                        </div>
                    </div>



                    <div class="margin_vertical_10"

                         id="gf_paywayapi_notifications" <?php echo!isset($form["notifications"]) ? "style='display:none;'" : "" ?>>

                        <label

                            class="left_header"><?php _e("Notifications", "gravityformspaywayapi"); ?><?php gform_tooltip("paywayapi_notifications") ?></label>

                        <?php
                        $has_delayed_notifications = rgar($config['meta'], 'delay_notifications') || rgar($config['meta'], 'delay_notification') || rgar($config['meta'], 'delay_autoresponder');
                        ?>

                        <div style="overflow:hidden;">

                            <input type="checkbox" name="gf_paywayapi_delay_notifications" id="gf_paywayapi_delay_notifications"

                                   value="1"

                                   onclick="ToggleNotifications();" <?php checked("1", $has_delayed_notifications) ?> />

                            <label class="inline"

                                   for="gf_paywayapi_delay_notifications"><?php _e("Send notifications only when payment is received.", "gravityformspaywayapi"); ?></label>

                            <ul id="gf_paywayapi_notification_container"

                                style="padding-left:20px; <?php echo $has_delayed_notifications ? "" : "display:none;" ?>">

                                <?php
                                if (!empty($form) && is_array($form["notifications"])) {

                                    $selected_notifications = self::get_selected_notifications($config, $form);

                                    foreach ($form["notifications"] as $notification) {
                                        ?>

                                        <li class="gf_paywayapi_notification">

                                            <input type="checkbox" name="gf_paywayapi_selected_notifications[]"

                                                   id="gf_paywayapi_selected_notifications"

                                                   value="<?php echo $notification["id"] ?>" <?php checked(true, in_array($notification["id"], $selected_notifications)) ?> />

                                            <label class="inline"

                                                   for="gf_paywayapi_selected_notifications"><?php echo $notification["name"]; ?></label>

                                        </li>

                                        <?php
                                    }
                                }
                                ?>

                            </ul>

                        </div>

                    </div>


                    <?php do_action("gform_paywayapi_add_option_group", $config, $form); ?>

                    <div id="gf_paywayapi_conditional_section" valign="top" class="margin_vertical_10">
                        <label for="gf_paywayapi_conditional_optin" class="left_header"><?php _e("PayWayApi Condition", "gravityformspaywayapi"); ?> <?php gform_tooltip("paywayapi_conditional") ?></label>

                        <div id="gf_paywayapi_conditional_option">
                            <table cellspacing="0" cellpadding="0">
                                <tr>
                                    <td>
                                        <input type="checkbox" id="gf_paywayapi_conditional_enabled" name="gf_paywayapi_conditional_enabled" value="1" onclick="if (this.checked) {
                                                    jQuery('#gf_paywayapi_conditional_container').fadeIn('fast');
                                                } else {
                                                    jQuery('#gf_paywayapi_conditional_container').fadeOut('fast');
                                                }" <?php echo rgar($config['meta'], 'paywayapi_conditional_enabled') ? "checked='checked'" : "" ?>/>
                                        <label for="gf_paywayapi_conditional_enable"><?php _e("Enable", "gravityformspaywayapi"); ?></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div id="gf_paywayapi_conditional_container" <?php echo!rgar($config['meta'], 'paywayapi_conditional_enabled') ? "style='display:none'" : "" ?>>

                                            <div id="gf_paywayapi_conditional_fields" <?php echo empty($selection_fields) ? "style='display:none'" : "" ?>>
                                                <?php _e("Send to PayWayApi if ", "gravityformspaywayapi") ?>

                                                <select id="gf_paywayapi_conditional_field_id" name="gf_paywayapi_conditional_field_id" class="optin_select" onchange='jQuery("#gf_paywayapi_conditional_value").html(GetFieldValues(jQuery(this).val(), "", 20));'>
                                                    <?php echo $selection_fields ?>
                                                </select>
                                                <select id="gf_paywayapi_conditional_operator" name="gf_paywayapi_conditional_operator">
                                                    <option value="is" <?php echo rgar($config['meta'], 'paywayapi_conditional_operator') == "is" ? "selected='selected'" : "" ?>><?php _e("is", "gravityformspaywayapi") ?></option>
                                                    <option value="isnot" <?php echo rgar($config['meta'], 'paywayapi_conditional_operator') == "isnot" ? "selected='selected'" : "" ?>><?php _e("is not", "gravityformspaywayapi") ?></option>
                                                </select>
                                                <select id="gf_paywayapi_conditional_value" name="gf_paywayapi_conditional_value" class='optin_select'></select>

                                            </div>

                                            <div id="gf_paywayapi_conditional_message" <?php echo!empty($selection_fields) ? "style='display:none'" : "" ?>>
                                                <?php _e("To create a registration condition, your form must have a drop down, checkbox or multiple choice field", "gravityform") ?>
                                            </div>

                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>

                    </div> <!-- / paywayapi conditional -->

                    <div id="paywayapi_submit_container" class="margin_vertical_30">
                        <input type="submit" name="gf_paywayapi_submit" value="<?php echo empty($id) ? __("  Save  ", "gravityformspaywayapi") : __("Update", "gravityformspaywayapi"); ?>" class="button-primary"/>
                        <input type="button" value="<?php _e("Cancel", "gravityformspaywayapi"); ?>" class="button" onclick="javascript:document.location = 'admin.php?page=gf_paywayapi'" />
                    </div>
                </div>
            </form>
        </div>

        <script type="text/javascript">

            function SelectType(type) {
                jQuery("#paywayapi_field_group").slideUp();

                jQuery("#paywayapi_field_group input[type=\"text\"], #paywayapi_field_group select").val("");


                jQuery("#paywayapi_field_group input:checked").attr("checked", false);

                if (type) {
                    jQuery("#paywayapi_form_container").slideDown();
                    jQuery("#gf_paywayapi_form").val("");
                } else {
                    jQuery("#paywayapi_form_container").slideUp();
                }
            }

            function SelectForm(type, formId, settingId) {
                if (!formId) {
                    jQuery("#paywayapi_field_group").slideUp();
                    return;
                }

                jQuery("#paywayapi_wait").show();
                jQuery("#paywayapi_field_group").slideUp();

                var mysack = new sack("<?php bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php");
                mysack.execute = 1;
                mysack.method = 'POST';
                mysack.setVar("action", "gf_select_paywayapi_form");
                mysack.setVar("gf_select_paywayapi_form", "<?php echo wp_create_nonce("gf_select_paywayapi_form") ?>");
                mysack.setVar("type", type);
                mysack.setVar("form_id", formId);
                mysack.setVar("setting_id", settingId);
                mysack.encVar("cookie", document.cookie, false);
                mysack.onError = function () {
                    jQuery("#paywayapi_wait").hide();
                    alert('<?php _e("Ajax error while selecting a form", "gravityformspaywayapi") ?>')
                };
                mysack.runAJAX();

                return true;
            }

            function EndSelectForm(form_meta, customer_fields, recurring_amount_options) {
                //setting global form object
                form = form_meta;

                var type = jQuery("#gf_paywayapi_type").val();

                jQuery(".gf_paywayapi_invalid_form").hide();
                if ((type == "product" || type == "subscription") && GetFieldsByType(["product"]).length == 0) {
                    jQuery("#gf_paywayapi_invalid_product_form").show();
                    jQuery("#paywayapi_wait").hide();
                    return;
                } else if (type == "donation" && GetFieldsByType(["product", "donation"]).length == 0) {
                    jQuery("#gf_paywayapi_invalid_donation_form").show();
                    jQuery("#paywayapi_wait").hide();
                    return;
                }

                jQuery(".paywayapi_field_container").hide();
                jQuery("#paywayapi_customer_fields").html(customer_fields);

                //Calling callback functions
                jQuery(document).trigger('paywayapiFormSelected', [form]);

                jQuery("#gf_paywayapi_conditional_enabled").attr('checked', false);
                SetPayWayApiCondition("", "");

                jQuery("#paywayapi_field_container_" + type).show();
                jQuery("#paywayapi_field_group").slideDown();
                jQuery("#paywayapi_wait").hide();
            }



            function GetFieldsByType(types) {
                var fields = new Array();
                for (var i = 0; i < form["fields"].length; i++) {
                    if (IndexOf(types, form["fields"][i]["type"]) >= 0)
                        fields.push(form["fields"][i]);
                }
                return fields;
            }

            function IndexOf(ary, item) {
                for (var i = 0; i < ary.length; i++)
                    if (ary[i] == item)
                        return i;

                return -1;
            }

        </script>

        <script type="text/javascript">

            // Conditional Functions

        <?php
        if (!empty($config["form_id"])) {
            ?>

                // initilize form object
                form = <?php echo GFCommon::json_encode($form) ?>;

                // initializing registration condition drop downs
                jQuery(document).ready(function () {
                    var selectedField = "<?php echo str_replace('"', '\"', $config["meta"]["paywayapi_conditional_field_id"]) ?>";
                    var selectedValue = "<?php echo str_replace('"', '\"', $config["meta"]["paywayapi_conditional_value"]) ?>";
                    SetPayWayApiCondition(selectedField, selectedValue);
                });

            <?php
        }
        ?>

            function SetPayWayApiCondition(selectedField, selectedValue) {

                // load form fields
                jQuery("#gf_paywayapi_conditional_field_id").html(GetSelectableFields(selectedField, 20));
                var optinConditionField = jQuery("#gf_paywayapi_conditional_field_id").val();
                var checked = jQuery("#gf_paywayapi_conditional_enabled").attr('checked');

                if (optinConditionField) {
                    jQuery("#gf_paywayapi_conditional_message").hide();
                    jQuery("#gf_paywayapi_conditional_fields").show();
                    jQuery("#gf_paywayapi_conditional_value").html(GetFieldValues(optinConditionField, selectedValue, 20));
                } else {
                    jQuery("#gf_paywayapi_conditional_message").show();
                    jQuery("#gf_paywayapi_conditional_fields").hide();
                }

                if (!checked)
                    jQuery("#gf_paywayapi_conditional_container").hide();

            }

            function GetFieldValues(fieldId, selectedValue, labelMaxCharacters) {
                if (!fieldId)
                    return "";

                var str = "";
                var field = GetFieldById(fieldId);
                if (!field || !field.choices)
                    return "";

                var isAnySelected = false;

                for (var i = 0; i < field.choices.length; i++) {
                    var fieldValue = field.choices[i].value ? field.choices[i].value : field.choices[i].text;
                    var isSelected = fieldValue == selectedValue;
                    var selected = isSelected ? "selected='selected'" : "";
                    if (isSelected)
                        isAnySelected = true;

                    str += "<option value='" + fieldValue.replace(/'/g, "&#039;") + "' " + selected + ">" + TruncateMiddle(field.choices[i].text, labelMaxCharacters) + "</option>";
                }

                if (!isAnySelected && selectedValue) {
                    str += "<option value='" + selectedValue.replace(/'/g, "&#039;") + "' selected='selected'>" + TruncateMiddle(selectedValue, labelMaxCharacters) + "</option>";
                }

                return str;
            }

            function GetFieldById(fieldId) {
                for (var i = 0; i < form.fields.length; i++) {
                    if (form.fields[i].id == fieldId)
                        return form.fields[i];
                }
                return null;
            }

            function TruncateMiddle(text, maxCharacters) {
                if (!text)
                    return "";

                if (text.length <= maxCharacters)
                    return text;
                var middle = parseInt(maxCharacters / 2);
                return text.substr(0, middle) + "..." + text.substr(text.length - middle, middle);
            }

            function GetSelectableFields(selectedFieldId, labelMaxCharacters) {
                var str = "";
                var inputType;
                for (var i = 0; i < form.fields.length; i++) {
                    fieldLabel = form.fields[i].adminLabel ? form.fields[i].adminLabel : form.fields[i].label;
                    inputType = form.fields[i].inputType ? form.fields[i].inputType : form.fields[i].type;
                    if (inputType == "checkbox" || inputType == "radio" || inputType == "select") {
                        var selected = form.fields[i].id == selectedFieldId ? "selected='selected'" : "";
                        str += "<option value='" + form.fields[i].id + "' " + selected + ">" + TruncateMiddle(fieldLabel, labelMaxCharacters) + "</option>";
                    }
                }
                return str;
            }

        </script>

        <?php
    }

    public static function select_paywayapi_form() {

        check_ajax_referer("gf_select_paywayapi_form", "gf_select_paywayapi_form");

        $type = $_POST["type"];
        $form_id = intval($_POST["form_id"]);
        $setting_id = intval($_POST["setting_id"]);

        //fields meta
        $form = RGFormsModel::get_form_meta($form_id);

        $customer_fields = self::get_customer_information($form);


        die("EndSelectForm(" . GFCommon::json_encode($form) . ", '" . str_replace("'", "\'", $customer_fields) . "');");
    }

    public static function add_permissions() {
        global $wp_roles;
        $wp_roles->add_cap("administrator", "gravityforms_paywayapi");
        $wp_roles->add_cap("administrator", "gravityforms_paywayapi_uninstall");
    }

    //Target of Member plugin filter. Provides the plugin with Gravity Forms lists of capabilities
    public static function members_get_capabilities($caps) {
        return array_merge($caps, array("gravityforms_paywayapi", "gravityforms_paywayapi_uninstall"));
    }

    public static function send_to_paywayapi($confirmation, $form, $entry, $ajax) {

        // ignore requests that are not the current form's submissions
        if (RGForms::post("gform_submit") != $form["id"])
            return $confirmation;

        require_once(self::get_base_path() . "/Qvalent_PayWayAPI.php");
        require_once(self::get_base_path() . "/data.php");
        $paywayAPI = new Qvalent_PayWayAPI();
        $config = GFPayWayApiData::get_feed_by_form($form["id"]);
        if (!$config)
            return $confirmation;
        else
            $config = $config[0]; //using first paywayapi feed (only one paywayapi feed per form is supported)
// ignore requests when paywayapi conditional is active and condition is not met
        if (!self::has_paywayapi_condition($form, $config))
            return $confirmation;

        //updating lead's payment_status to Processing
        RGFormsModel::update_lead_property($entry["id"], "payment_status", 'Processing');

        $init = "caFile=" . self::get_base_path() . "/cacerts.crt&" .
                "certificateFile=" . self::get_base_path() . "/" . $config["meta"]['certificate'] . "&" .
                "logDirectory=" . self::get_base_path() . '/logs';
        $paywayAPI->initialise($init);
        $invoice_id = apply_filters("gform_paywayapi_invoice", "", $form, $entry);

        $expdate = $_POST["input_" . str_replace(".", "_", $config["meta"]["customer_fields"]["exp"])];
        $exp_field = str_replace("_month", "", $config["meta"]["customer_fields"]["exp"]);
        $exp_field = str_replace("_year", "", $exp_field);
        $exp_field = "input_" . str_replace(".", "_", $exp_field);
        $expdate = $_POST[$exp_field];

        $params["order.type"] = "capture";
        $params["customer.username"] = $config["meta"]['username'];
        $params["customer.password"] = $config["meta"]['password'];
        $params["customer.merchant"] = $config["meta"]['merchant_id'];
        $params["customer.orderNumber"] = $entry['id']; //$invoice_id;
        $params["card.PAN"] = $_POST["input_" . str_replace(".", "_", $config["meta"]["customer_fields"]["ccnumber"])];
        $params["card.CVN"] = $_POST["input_" . str_replace(".", "_", $config["meta"]["customer_fields"]["cv2"])];
        $params["card.expiryYear"] = substr($expdate[1], 2);
        $params["card.expiryMonth"] = sprintf('%02d', $expdate[0]);
        $params["card.currency"] = "AUD";
        $total = GFCommon::get_order_total($form, $entry);
        $params["order.amount"] = $total * 100;
        $params["order.ECI"] = "SSL";
        $params["customer.customerReferenceNumber"] = "PCCC" . $entry["id"];
        $params["card.cardHolderName"] = $_POST["input_" . str_replace(".", "_", $config["meta"]["customer_fields"]["cardholder_name"])];

        $requestText = $paywayAPI->formatRequestParameters($params);
        $responseText = $paywayAPI->processCreditCard($requestText);
        $result = $paywayAPI->parseResponseParameters($responseText);



        if ($result === false) {

            $message = 'There has been an error processing your payment.';

            GFFormDisplay::$submission[$form["id"]] = array("is_confirmation" => false, "confirmation_message" => $message, "form" => $form);
            RGFormsModel::update_lead_property($entry["id"], "payment_status", 'Failed');
        } else {
            $txnid = $result['response.receiptNo'];

            if ($result['response.summaryCode'] === '0') {
                $message = "Your payment has been processed";

                if (isset($config["meta"]["delay_notifications"])) {
                    //sending delayed notifications
                    GFCommon::send_notifications($config["meta"]["selected_notifications"], $form, $entry, true, "form_submission");
                } else {
                    //sending notifications using the legacy structure
                    if ($config["meta"]["delay_notification"]) {
                        GFCommon::send_admin_notification($form, $entry);
                    }

                    if ($config["meta"]["delay_autoresponder"]) {
                        GFCommon::send_user_notification($form, $entry);
                    }
                }

                RGFormsModel::update_lead_property($entry["id"], "payment_amount", $total);
                RGFormsModel::update_lead_property($entry["id"], "transaction_id", $txnid);
                RGFormsModel::update_lead_property($entry["id"], "payment_status", 'Approved');

                if (is_array($confirmation) && isset($confirmation["redirect"])) {
                    header("Location: {$confirmation["redirect"]}");
                    exit;
                } else {
                    GFFormDisplay::$submission[$form["id"]] = array("is_confirmation" => true, "confirmation_message" => $confirmation . $message, "form" => $form, "lead" => $lead);
                }


            } else {

                if (isset($result["response.responseCode"]) && isset($result["response.text"])) {
                    $message = "Error " . $result["response.responseCode"] . ": " . $result["response.text"];
                } elseif (isset($result["response.text"])) {
                    $message = "Error: " . $result["response.text"];
                } else {
                    $message = "There has been an error processing your payment. Please try later or contact us for help.";
                }

                RGFormsModel::update_lead_property($entry["id"], "transaction_id", $txnid);
                RGFormsModel::update_lead_property($entry["id"], "payment_status", 'Failed');
                $_SESSION['error_message'] = $message;
                add_filter("the_content", array("GFPayWayApi", "error_on_form_page"), 10, 1);
            }
        }
    }

    public static function error_on_form_page($content) {
        $msg = '<div class="gform_wrapper"><div class="validation_error">' . $_SESSION['error_message'] . '</div></div>';
        return $msg . $content;
    }

    /* 	add our filter function to the hook */

    public static function has_paywayapi_condition($form, $config) {

        $config = $config["meta"];

        $operator = $config["paywayapi_conditional_operator"];
        $field = RGFormsModel::get_field($form, $config["paywayapi_conditional_field_id"]);

        if (empty($field) || !$config["paywayapi_conditional_enabled"])
            return true;

        // if conditional is enabled, but the field is hidden, ignore conditional
        $is_visible = !RGFormsModel::is_field_hidden($form, $field, array());

        $field_value = RGFormsModel::get_field_value($field, array());

        $is_value_match = RGFormsModel::is_value_match($field_value, $config["paywayapi_conditional_value"]);
        $is_match = $is_value_match && $is_visible;

        $go_to_paywayapi = ($operator == "is" && $is_match) || ($operator == "isnot" && !$is_match);

        return $go_to_paywayapi;
    }

    public static function get_config($form_id) {
        if (!class_exists("GFPayWayApiData"))
            require_once(self::get_base_path() . "/data.php");

        //Getting paywayapi settings associated with this transaction
        $config = GFPayWayApiData::get_feed_by_form($form_id);

        //Ignore IPN messages from forms that are no longer configured with the PayWayApi add-on
        if (!$config)
            return false;

        return $config[0]; //only one feed per form is supported
    }

    public static function maybe_thankyou_page() {
        if (!self::is_gravityforms_supported())
            return;

        if ($str = RGForms::get("gf_paywayapi_return")) {
            $str = base64_decode($str);

            parse_str($str, $query);
            if (wp_hash("ids=" . $query["ids"]) == $query["hash"]) {
                list($form_id, $lead_id) = explode("|", $query["ids"]);

                $form = RGFormsModel::get_form_meta($form_id);
                $lead = RGFormsModel::get_lead($lead_id);

                if (!class_exists("GFFormDisplay"))
                    require_once(GFCommon::get_base_path() . "/form_display.php");

                $confirmation = GFFormDisplay::handle_confirmation($form, $lead, false);

                if (is_array($confirmation) && isset($confirmation["redirect"])) {
                    header("Location: {$confirmation["redirect"]}");
                    exit;
                }

                GFFormDisplay::$submission[$form_id] = array("is_confirmation" => true, "confirmation_message" => $confirmation, "form" => $form, "lead" => $lead);
            }
        }
    }

    public static function uninstall() {

        //loading data lib
        require_once(self::get_base_path() . "/data.php");

        if (!GFPayWayApi::has_access("gravityforms_paywayapi_uninstall"))
            die(__("You don't have adequate permission to uninstall the PayWayApi Add-On.", "gravityformspaywayapi"));

        //droping all tables
        GFPayWayApiData::drop_tables();

        //removing options
        delete_option("gf_paywayapi_site_name");
        delete_option("gf_paywayapi_auth_token");
        delete_option("gf_paywayapi_version");

        //Deactivating plugin
        $plugin = "gravityformspaywayapi/paywayapi.php";
        deactivate_plugins($plugin);
        update_option('recently_activated', array($plugin => time()) + (array) get_option('recently_activated'));
    }

    private static function is_gravityforms_installed() {
        return class_exists("RGForms");
    }

    private static function is_gravityforms_supported() {
        if (class_exists("GFCommon")) {
            $is_correct_version = version_compare(GFCommon::$version, self::$min_gravityforms_version, ">=");
            return $is_correct_version;
        } else {
            return false;
        }
    }

    protected static function has_access($required_permission) {
        $has_members_plugin = function_exists('members_get_capabilities');
        $has_access = $has_members_plugin ? current_user_can($required_permission) : current_user_can("level_7");
        if ($has_access)
            return $has_members_plugin ? $required_permission : "level_7";
        else
            return false;
    }

    private static function get_customer_information($form, $config = null) {

        //getting list of all fields for the selected form
        $form_fields = self::get_form_fields($form);

        $str = "<table cellpadding='0' cellspacing='0'><tr><td class='paywayapi_col_heading'>" . __("PayWayApi Fields", "gravityformspaywayapi") . "</td><td class='paywayapi_col_heading'>" . __("Form Fields", "gravityformspaywayapi") . "</td></tr>";
        $customer_fields = self::get_customer_fields();
        foreach ($customer_fields as $field) {
            $selected_field = $config ? $config["meta"]["customer_fields"][$field["name"]] : "";
            $str .= "<tr><td class='paywayapi_field_cell'>" . $field["label"] . "</td><td class='paywayapi_field_cell'>" . self::get_mapped_field_list($field["name"], $selected_field, $form_fields) . "</td></tr>";
        }
        $str .= "</table>";

        return $str;
    }

    private static function get_customer_fields() {
        return array(
            array("name" => "cardholder_name", "label" => "Cardholder Name"),
            array("name" => "email", "label" => "Email"),
            array("name" => "exp", "label" => "Expire Date"),
            array("name" => "ccnumber", "label" => "Credit Card Number"),
            array("name" => "cv2", "label" => "CVV")
        );
    }

    private static function get_mapped_field_list($variable_name, $selected_field, $fields) {
        $field_name = "paywayapi_customer_field_" . $variable_name;
        $str = "<select name='$field_name' id='$field_name'><option value=''></option>";
        foreach ($fields as $field) {
            $field_id = $field[0];
            $field_label = esc_html(GFCommon::truncate_middle($field[1], 40));

            $selected = $field_id == $selected_field ? "selected='selected'" : "";
            $str .= "<option value='" . $field_id . "' " . $selected . ">" . $field_label . "</option>";
        }
        $str .= "</select>";
        return $str;
    }

    private static function get_product_options($form, $selected_field) {
        $str = "<option value=''>" . __("Select a field", "gravityformspaywayapi") . "</option>";
        $fields = GFCommon::get_fields_by_type($form, array("product"));

        foreach ($fields as $field) {
            $field_id = $field["id"];
            $field_label = RGFormsModel::get_label($field);

            $selected = $field_id == $selected_field ? "selected='selected'" : "";
            $str .= "<option value='" . $field_id . "' " . $selected . ">" . $field_label . "</option>";
        }

        $selected = $selected_field == 'all' ? "selected='selected'" : "";
        $str .= "<option value='all' " . $selected . ">" . __("Form Total", "gravityformspaywayapi") . "</option>";

        return $str;
    }

    private static function get_form_fields($form) {
        $fields = array();

        if (is_array($form["fields"])) {
            foreach ($form["fields"] as $field) {
                if (isset($field["inputs"]) && is_array($field["inputs"])) {

                    foreach ($field["inputs"] as $input)
                        $fields[] = array($input["id"], GFCommon::get_label($field, $input["id"]));
                } else if (!rgar($field, 'displayOnly')) {
                    $fields[] = array($field["id"], GFCommon::get_label($field));
                }
            }
        }
        return $fields;
    }

    private static function return_url($form_id, $lead_id) {
        $pageURL = 'http';
        if (rgar($_SERVER, "HTTPS") == "on")
            $pageURL .= "s";
        $pageURL .= "://";

        if ($_SERVER["SERVER_PORT"] != "80")
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        else
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];

        return $pageURL;
    }

    private static function is_paywayapi_page() {
        $current_page = trim(strtolower(RGForms::get("page")));
        return in_array($current_page, array("gf_paywayapi"));
    }

    //Returns the url of the plugin's root folder
    protected function get_base_url() {
        return plugins_url(null, __FILE__);
    }

    //Returns the physical path of the plugin's root folder
    protected function get_base_path() {
        $folder = basename(dirname(__FILE__));
        return WP_PLUGIN_DIR . "/" . $folder;
    }

}

if (!function_exists("rgget")) {

    function rgget($name, $array = null) {
        if (!isset($array))
            $array = $_GET;

        if (isset($array[$name]))
            return $array[$name];

        return "";
    }

}

if (!function_exists("rgpost")) {

    function rgpost($name, $do_stripslashes = true) {
        if (isset($_POST[$name]))
            return $do_stripslashes ? stripslashes_deep($_POST[$name]) : $_POST[$name];

        return "";
    }

}

if (!function_exists("rgar")) {

    function rgar($array, $name) {
        if (isset($array[$name]))
            return $array[$name];

        return '';
    }

}

if (!function_exists("rgars")) {

    function rgars($array, $name) {
        $names = explode("/", $name);
        $val = $array;
        foreach ($names as $current_name) {
            $val = rgar($val, $current_name);
        }
        return $val;
    }

}

if (!function_exists("rgempty")) {

    function rgempty($name, $array = null) {
        if (!$array)
            $array = $_POST;

        $val = rgget($name, $array);
        return empty($val);
    }

}

if (!function_exists("rgblank")) {

    function rgblank($text) {
        return empty($text) && strval($text) != "0";
    }

}
?>