<?php

/**
 * Plugin Name:       DirectoryPress Frontend Listing
 * Plugin URI:        https://directorypress.co/product/directorypress-frontend-listing-addon/
 * Description:       Frontend Ads listing functionality for DirectoryPress Plugin.
 * Version:           2.5.3
 * Author:            DirectoryPress
 * Author URI:         https://directorypress.co/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       directorypress-frontend
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'DIRECTORYPRESS_FRONTEND_VERSION', '2.5.3' );
define('DPFL_PATH', plugin_dir_path(__FILE__));
define('DPFL_URL', plugins_url('/', __FILE__));
define( 'DPFL_TEMPLATES_PATH', DPFL_PATH . 'public/');

if(is_admin() && in_array('directorypress/directorypress.php', apply_filters('active_plugins', get_option('active_plugins')))){
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$directorypress_data = get_plugin_data( WP_PLUGIN_DIR .'/directorypress/directorypress.php' );
	if(version_compare($directorypress_data['Version'], '3.4.0', '<') ){
		deactivate_plugins(plugin_basename(__FILE__));
	}
}
function activate_directorypress_frontend() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-directorypress-frontend-activator.php';
	Directorypress_Frontend_Activator::activate();
}

function deactivate_directorypress_frontend() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-directorypress-frontend-deactivator.php';
	Directorypress_Frontend_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_directorypress_frontend' );
register_deactivation_hook( __FILE__, 'deactivate_directorypress_frontend' );

require plugin_dir_path( __FILE__ ) . 'includes/class-directorypress-frontend.php';

function run_directorypress_frontend() {
	//global $directorypress_object;
	$directorypress_fsubmit_instance = new Directorypress_Frontend();
	$directorypress_fsubmit_instance->run();
}
add_action( 'directorypress_after_loaded', 'run_directorypress_frontend' );
