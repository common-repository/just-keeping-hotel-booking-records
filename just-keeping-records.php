<?php
/**
 * Plugin Name:       Just Keeping Hotel Booking Records
 * Description:       This is simple plugin to keep records of booking by admins.
 * Version:           1.0
 * Requires PHP:      5.6
 * Author:            E-Learning expert
 * Author URI:        https://profiles.wordpress.org/manishswamy077/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       just_keeping_records
 * Domain Path:       /languages
 */

//Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'plugins_loaded',  'jkr_textdomain_pot');

if (!function_exists('jkr_textdomain_pot')) {
	function jkr_textdomain_pot(){
		load_plugin_textdomain('just_keeping_records', false, dirname(plugin_basename(__FILE__)).'/languages/');

	}
}

require_once 'includes/constants.php';
require_once 'includes/functions.php';
require_once 'admin/admin.php';
require_once 'public/public.php';
