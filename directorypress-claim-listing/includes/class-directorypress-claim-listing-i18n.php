<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://directorypress.co/
 * @since      1.0.0
 *
 * @package    Directorypress_Claim_Listing
 * @subpackage Directorypress_Claim_Listing/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Directorypress_Claim_Listing
 * @subpackage Directorypress_Claim_Listing/includes
 * @author     DirectoryPress <team@directorypress.co>
 */
class Directorypress_Claim_Listing_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'directorypress-claim-listing',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
