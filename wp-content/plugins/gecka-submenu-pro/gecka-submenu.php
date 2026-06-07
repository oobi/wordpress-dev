<?php
/*
Plugin Name: Gecka Submenu Pro
Plugin URI: http://gecka-apps.com/wordpress-plugins/gecka-submenu-pro/
Description: Enhances the worpdress nav menu system
Version: 100.0.1-firefly
Author: Gecka
Author URI: http://gecka-apps.com
*/

/* Copyright 2010-2012  Gecka SARL (email: contact@gecka.nc). All rights reserved

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define('GKSM_PATH' , WP_PLUGIN_DIR . "/" . plugin_basename(dirname(__FILE__)) );
define('GKSM_URL'  , WP_PLUGIN_URL . "/" . plugin_basename(dirname(__FILE__)) );

define('GKSM_VERSION', '100.0.0-firefly');

// requires PHP 5
function gksm_activation_check(){
	if (version_compare(PHP_VERSION, '5.0.0', '<')) {
		deactivate_plugins( basename(dirname(__FILE__)) . '/' . basename(__FILE__) ); // Deactivate ourself
		wp_die("Sorry, Gecka Submenu requires PHP 5 or higher. Ask your host how to enable PHP 5 as the default on your servers.");
	}
}
register_activation_hook(__FILE__, 'gksm_activation_check');

//needed global vars for widget usage (hugly hack...)
$GKSM_ID = $GKSM_MENUID = null;

require GKSM_PATH . '/gecka-submenu.class.php';

if (class_exists('Gecka_Submenu')) {
    if (!isset($GkSm)) {

    	include GKSM_PATH . '/models/Submenu.php';
    	$GkSm = new Gecka_Submenu();

    }
}
