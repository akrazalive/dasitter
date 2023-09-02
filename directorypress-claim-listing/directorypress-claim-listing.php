<?php

/**
 * Plugin Name:       DirectoryPress Claim Listing
 * Plugin URI:        https://directorypress.co/product/directorypress-claim-listing
 * Description:       This Addon provide claim functionality for DirectoryPress Plugin
 * Version:           1.0.1
 * Author:            DirectoryPress
 * Author URI:        https://directorypress.co/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       directorypress-claim-listing
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('DIRECTORYPRESS_CLAIM_LISTING_VERSION', '1.0.1');
define('DIRECTORYPRESS_CLAIM_LISTING_PATH', plugin_dir_path(__FILE__));
define('DIRECTORYPRESS_CLAIM_LISTING_URL', plugins_url('/', __FILE__));
define('DIRECTORYPRESS_CLAIM_LISTING_ASSETS_PATH', DIRECTORYPRESS_CLAIM_LISTING_PATH . 'assets/');
define('DIRECTORYPRESS_CLAIM_LISTING_ASSETS_URL', plugins_url('/', __FILE__) . 'assets/');
define('DIRECTORYPRESS_CLAIM_LISTING_TEMPLATES_PATH', DIRECTORYPRESS_CLAIM_LISTING_PATH . 'public/');


function activate_directorypress_claim_listing() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-directorypress-claim-listing-activator.php';
	Directorypress_Claim_Listing_Activator::activate();
}

function deactivate_directorypress_claim_listing() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-directorypress-claim-listing-deactivator.php';
	Directorypress_Claim_Listing_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_directorypress_claim_listing' );
register_deactivation_hook( __FILE__, 'deactivate_directorypress_claim_listing' );

require plugin_dir_path( __FILE__ ) . 'includes/class-directorypress-claim-listing.php';

function run_directorypress_claim_listing() {

	$directorypress_claim_listing_object = new Directorypress_Claim_Listing();
	$directorypress_claim_listing_object->run();

}
add_action( 'directorypress_after_loaded', 'run_directorypress_claim_listing' );
