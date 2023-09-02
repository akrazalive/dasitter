<?php

/**
 * Plugin Name:       DirectoryPress MultiDirectory
 * Plugin URI:         https://directorypress.co/product/directorypress-multi-directory-addon/
 * Description:       This Plugin provides multi-directorytype feature for DirectoryPress Plugin.
 * Version:           2.8.1
 * Author:            DirectoryPress
 * Author URI:         https://directorypress.co/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       directorypress-multidirectory
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'DIRECTORYPRESS_MULTIDIRECTORY_VERSION', '2.8.1' );
define('DPMD_PATH', plugin_dir_path(__FILE__));
define('DPMD_URL', plugins_url('/', __FILE__));
define( 'DPMD_TEMPLATES_PATH', DPMD_PATH . 'public/');

if(is_admin() && in_array('directorypress/directorypress.php', apply_filters('active_plugins', get_option('active_plugins')))){
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$directorypress_data = get_plugin_data( WP_PLUGIN_DIR .'/directorypress/directorypress.php' );
	if(version_compare($directorypress_data['Version'], '3.4.0', '<') ){
		deactivate_plugins(plugin_basename(__FILE__));
	}
}

function activate_directorypress_multidirectory() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-directorypress-multidirectory-activator.php';
	Directorypress_Multidirectory_Activator::activate();
}

function deactivate_directorypress_multidirectory() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-directorypress-multidirectory-deactivator.php';
	Directorypress_Multidirectory_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_directorypress_multidirectory' );
register_deactivation_hook( __FILE__, 'deactivate_directorypress_multidirectory' );

require plugin_dir_path( __FILE__ ) . 'includes/class-directorypress-multidirectory.php';

function run_directorypress_multidirectory() {

	$multidirectory_instance = new Directorypress_Multidirectory();
	$multidirectory_instance->run();

}
add_action( 'directorypress_loaded', 'run_directorypress_multidirectory' );
