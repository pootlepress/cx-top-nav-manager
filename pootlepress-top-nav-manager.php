<?php
/*
Plugin Name: Canvas Extension - Top Nav Manager
Plugin URI: http://pootlepress.com/
Description: An extension for WooThemes Canvas that allow you to manage top navigation.
Version: 1.3
Author: PootlePress
Author URI: http://pootlepress.com/
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once( 'pootlepress-top-nav-manager-functions.php' );
require_once( 'classes/class-pootlepress-top-nav-manager.php' );
require_once( 'classes/class-pootlepress-canvas-options.php' );
require_once( 'classes/class-pootlepress-updater.php');

$GLOBALS['pootlepress_top_nav_manager'] = new Pootlepress_Top_Nav_Manager( __FILE__ );
$GLOBALS['pootlepress_top_nav_manager']->version = '1.3';

add_action('init', 'pp_tnm_updater');
function pp_tnm_updater()
{
    if (!function_exists('get_plugin_data')) {
        include(ABSPATH . 'wp-admin/includes/plugin.php');
    }
    $data = get_plugin_data(__FILE__);
    $wptuts_plugin_current_version = $data['Version'];
    $wptuts_plugin_remote_path = 'http://www.pootlepress.com/?updater=1';
    $wptuts_plugin_slug = plugin_basename(__FILE__);
    new Pootlepress_Updater ($wptuts_plugin_current_version, $wptuts_plugin_remote_path, $wptuts_plugin_slug);
}
?>
