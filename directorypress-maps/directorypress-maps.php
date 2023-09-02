<?php

/**
 * Plugin Name:       DirectoryPress Maps
 * Plugin URI:         https://directorypress.co/product/directorypress-maps-addon/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.4.1
 * Author:            DirectoryPress
 * Author URI:         https://directorypress.co/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       directorypress-maps
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'DIRECTORYPRESS_MAPS_VERSION', '1.4.1' );
define('DPMBV', 'v1.12.0');
define('DIRECTORYPRESS_MAPS_PATH', plugin_dir_path(__FILE__));
define('DIRECTORYPRESS_MAPS_URL', plugins_url('/', __FILE__));

define('DIRECTORYPRESS_MAPS_ASSETS_PATH', DIRECTORYPRESS_MAPS_PATH . 'assets/');
define('DIRECTORYPRESS_MAPS_ASSETS_URL', DIRECTORYPRESS_MAPS_URL . 'assets/');
define('DIRECTORYPRESS_MAPS_ICONS_PATH', DIRECTORYPRESS_MAPS_ASSETS_PATH . 'images/clusters/');
define('DIRECTORYPRESS_MAPS_ICONS_URL', DIRECTORYPRESS_MAPS_ASSETS_URL . 'images/clusters/');

if(is_admin() && in_array('directorypress/directorypress.php', apply_filters('active_plugins', get_option('active_plugins')))){
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$directorypress_data = get_plugin_data( WP_PLUGIN_DIR .'/directorypress/directorypress.php' );
	if(version_compare($directorypress_data['Version'], '3.4.0', '<') ){
		deactivate_plugins(plugin_basename(__FILE__));
	}
}
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-directorypress-maps-activator.php
 */
function activate_directorypress_maps() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-directorypress-maps-activator.php';
	Directorypress_Maps_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-directorypress-maps-deactivator.php
 */
function deactivate_directorypress_maps() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-directorypress-maps-deactivator.php';
	Directorypress_Maps_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_directorypress_maps' );
register_deactivation_hook( __FILE__, 'deactivate_directorypress_maps' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-directorypress-maps.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
 
define('DPMS_PATH', plugin_dir_path(__FILE__));
define('DPMS_URL', plugins_url('/', __FILE__));
define( 'DPMS_TEMPLATES_PATH', DPMS_PATH . 'public/');
function run_directorypress_maps() {

	$maps_instance = new Directorypress_Maps_Core();
	$maps_instance->run();
}


add_action( 'directorypress_loaded', 'run_directorypress_maps' );
