<?php
/*
Plugin Name: Canvas Extension - Top Nav Manager (WCAPI)
Plugin URI: http://pootlepress.com/
Description: An extension for WooThemes Canvas that allow you to manage top navigation.
Version: 2.0
Author: PootlePress
Author URI: http://pootlepress.com/
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
Prefix: cxtnm
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Displays an inactive message if the API License Key has not yet been activated
 */
if ( get_option( 'pootlepress_cx_top_nav_manager_activated' ) != 'Activated' ) {
    add_action( 'admin_notices', 'API_Manager_Example::am_example_inactive_notice' );
}

require_once( 'pootlepress-top-nav-manager-functions.php' );
require_once( 'classes/api-manager-example.php' );
require_once( 'classes/class-pootlepress-top-nav-manager.php' );
require_once( 'classes/class-pootlepress-canvas-options.php' );

$GLOBALS['pootlepress_top_nav_manager'] = new Pootlepress_Top_Nav_Manager( __FILE__ );
$GLOBALS['pootlepress_top_nav_manager']->version = '2.0';

?>
