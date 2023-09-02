<?php

/**
 * Plugin Name:       Frontend Messages For DirectoryPress
 * Plugin URI:        https://directorypress.co/product/directorypress-frontend-messages-addon/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           5.4.1
 * Author:            Designinvento
 * Author URI:        https://designinvento.net
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       directorypress-frontend-messages
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'DIRECTORYPRESS_FRONTEND_MESSAGES_VERSION', '5.4.1' );
global $wpdb;
			
define('DIFP_PLUGIN_VERSION', '5.3.4' );
define('DIFP_PLUGIN_FILE',  __FILE__ );
define('DIFP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define('DIFP_PLUGIN_URL', plugins_url( '/', __FILE__ ) );

if(is_admin() && in_array('directorypress/directorypress.php', apply_filters('active_plugins', get_option('active_plugins')))){
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$directorypress_data = get_plugin_data( WP_PLUGIN_DIR .'/directorypress/directorypress.php' );
	if(version_compare($directorypress_data['Version'], '3.4.0', '<') ){
		deactivate_plugins(plugin_basename(__FILE__));
	}
}
			
if ( !defined ('DIFP_MESSAGES_TABLE' ) )
define('DIFP_MESSAGES_TABLE',$wpdb->prefix.'difp_messages');
			
if ( !defined ('DIFP_META_TABLE' ) )
	define('DIFP_META_TABLE',$wpdb->prefix.'difp_meta');

function activate_directorypress_frontend_messages() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-directorypress-frontend-messages-activator.php';
	Directorypress_Frontend_Messages_Activator::activate();
}

function deactivate_directorypress_frontend_messages() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-directorypress-frontend-messages-deactivator.php';
	Directorypress_Frontend_Messages_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_directorypress_frontend_messages' );
register_deactivation_hook( __FILE__, 'deactivate_directorypress_frontend_messages' );

require plugin_dir_path( __FILE__ ) . 'includes/class-directorypress-frontend-messages.php';

function run_directorypress_frontend_messages() {

	$plugin = new Directorypress_Frontend_Messages();
	$plugin->run();

}
run_directorypress_frontend_messages();
